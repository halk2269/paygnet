<?php

/**
 * Анализ запроса, получаемого от пользователя
 * 
 * @todo XSLNeed()
 * @todo 404 возвращается из других классов, в query запросов к БД вообще нет
 * @todo Переменные, относящиеся к write-модулю. 404 при write
 * @todo Обработать запрос JS-модуля. 404 при JS
 *
 */
class QueryClass {

	const ERROR_MESSAGE = 'Ошибка во входных парамерах. Проверьте prefix в файле conf/global.conf.php и работает ли файл .htaccess';
	
	private $referer;            // Referer-ссылка
	private $userAgent;          // Браузер пользователя
	private $host;               // Хост, с которого пришёл запрос
	private $userIP;             // IP пользователя

	/** 
	 * Запрошенное действие (значение параметра ser, передаваемого через GET или POST)
	 */
	private $action;             
	private $moduleType;
	private $requestMethod;

	/** 
	 * Переменные, актуальные только для модуля чтения 
	 */
	
	private $query;              // Строка запроса
	private $url;                // Строка запроса без параметров (до первого символа "?")
	private $SCName;             // ID текущей секции
	private $xslNeed;            // Нужен ли нам XSL, или запрошен только XML
	private $uriIsModified = false;     // Была ли модифицирована адресная строка через URIModifyClass

	/**
	 * Переменные, актуальные только для модуля записи 
	 */
	
	private $writeModuleName;    // Если модуль записи, какой id
	private $writeRequestMethod; // Если модуль записи, как получен параметр writemodule

	/**
	 * Переменные, актуальные только для модуля JS 
	 */
	
	private $jsModuleName;       // Если модуль js, включена ли текущая секция
	
	private $error404 = false;

	/**
	 * @var GlobalConfClass
	 */
	private $conf;
	
	/**
	 * @var GlobalVarsClass
	 */
	private $globalvars;
	
	/**
	 * Обработка ошибок
	 * @var ErrorClass
	 */
	private $error;
	
	public function __construct() {
		$this->conf = GlobalConfClass::GetInstance();
		$this->globalvars = GlobalVarsClass::GetInstance();
		$this->error = ErrorClass::GetInstance();
		
		$this->_Initialize();
	}

	public function IsError404() {
		return $this->error404;
	}

	/**
	 * Возвращает значение запрашиваемого параметра, если такой существует
	 * @param string $paramName
	 * @return mixed
	 */
	public function GetParam($paramName) {
		if (
			"read" == $this->moduleType 
			|| "write" == $this->moduleType 
			&& "GET" == $this->writeRequestMethod
		) {
			return (isset($_GET[$paramName])) ? $_GET[$paramName] : false;
		} 
		
		if (
			"write" == $this->moduleType 
			&& "POST" == $this->writeRequestMethod
		) {
			return (isset($_POST[$paramName])) ? $_POST[$paramName] : false;
		}

		if (
			'js' == $this->moduleType
		) {
			return (isset($_REQUEST[$paramName])) ? $_REQUEST[$paramName] : false;	
		}
			
		return false;
	}

	/**
	 * Возвращает referer
	 * @return string
	 */
	public function GetReferer() {
		return $this->referer;
	}

	/**
	 * Возвращает браузер пользователя
	 * @return string
	 */
	public function GetUserAgent() {
		return $this->userAgent;
	}

	/**
	 * Возвращает хост
	 * @return string
	 */
	public function GetHost() {
		return $this->host;
	}

	/**
	 * Возвращает IP пользователя
	 * @return string
	 */
	public function GetUserIP() {
		return $this->userIP;
	}

	/**
	 * Возвращает тип выдачи.
	 * Возможные значения параметра ser.
	 * 1) normal - html (этот режим также возвращается при отсутствии параметра ser)
	 * 2) xml - xml-дерево (отображается в браузерах IE 6.0+, Firefox 1.0+)
	 * 3) txml - форматированное xml-дерево (отображается во всех браузерах)
	 * 4) xsl - xsl-преобразование (отображается в браузерах IE 6.0+, Firefox 1.0+)
	 * 5) sql - EXPLAIN всех SQL запросов на странице
	 *
	 * @return string
	 */
	public function GetAction() {
		return $this->action;
	}

	/**
	 * Возвращает тип модуля  - read, write, js.
	 * @return string
	 */
	public function GetModuleType() {
		return $this->moduleType;
	}

	public function GetQuery() {
		return $this->query;
	}

	public function GetQueryEscaped() {
		return ($this->conf->Param("StaticURL")) ? rawurlencode(rawurlencode($this->query)) : rawurlencode($this->query);
	}

	public function GetURL() {
		return $this->url;
	}

	public function GetSCName() {
		return $this->SCName;
	}

	public function IsURIModified() {
		return $this->uriIsModified;
	}

	/**
	 * Выдаёт, нужно ли нам генерировать XSL, или запрошен только XML. true - XSL нужен.
	 * @return bool
	 */
	public function XSLNeed() {
		return (
			$this->action != "xml" 
			&& $this->action != "txml" 
			&& $this->action != "sql"
		);
	}

	public function GetWriteModuleName() {
		return $this->writeModuleName;
	}

	public function GetWriteRequestMethod() {
		return $this->writeRequestMethod;
	}

	public function GetJSModuleName() {
		return $this->jsModuleName;
	}
	
	private function _Initialize() {
		$this->_InitializeQuery();
		if (!$this->_IsPrefixOk()) {
			$this->error->StopScript('QueryClass', self::ERROR_MESSAGE);
		}
		
		$this->_InitializeURL();
		$this->_InitializeSCName();
		$this->_RunUriModify();
		if ($this->conf->Param("StaticURL") && !$this->uriIsModified) {
			$this->_ParseStaticURL();
		}
		
		$this->_InitializeModuleType();
		$this->_InitializeAction();
		$this->_DecodeRetErrPaths();
		$this->_InitializeServerParams();
	}
	
	private function _IsPrefixOk() {
		$prefixQuoted = preg_quote($this->conf->Param("Prefix"));
		return preg_match("~^{$prefixQuoted}~", $this->query);
	}

	private function _InitializeQuery() {
		$requestUri = trim($_SERVER["REQUEST_URI"]);
		if ($requestUri{0} != "/") {
			$requestUri = "/" . $requestUri;
		}
		$this->query = $requestUri;
	}

	private function _InitializeURL() {
		$pos = strpos($this->query, '?');
		$this->url = (false !== $pos) ? substr($this->query, 0, $pos) : $this->query;
	}

	private function _InitializeSCName() {
		$SCName = substr($this->url, strlen($this->conf->Param("Prefix")));
		if ("/" === substr($SCName, -1, 1)) {
			// Отбрасываем '/'
			$SCName = substr($SCName, 0, -1);
		}
		$this->SCName = ($SCName) ? $SCName : $this->globalvars->GetStr('DefaultSectionName');
	}

	private function _ParseStaticURL() {
		$resultArray = explode("/", $this->SCName);

		$num = count($resultArray);
		if (0 == $num % 2) {
			// должно быть нечетное число элементов (имя секции + пары параметров "имя"-"значение")
			$this->error404 = true;
			return;
		}

		// переопеделение имени секции и url
		$this->SCName = $resultArray[0];
		$this->url = $this->conf->Param("Prefix") . $this->SCName . "/";

		// параметры
		for ($i = 1; $i < $num; $i += 2) {
			$_GET[$resultArray[$i]] = $resultArray[$i+1];
		}
	}
	
	private function _RunUriModify() {
		if (file_exists(CMSPATH_PBIN . "uri.php")) {
			require_once(CMSPATH_PBIN . "uri.php");
			$uriClass = new URIModifyClass();

			$SCName = $this->SCName;
			$this->uriIsModified = $uriClass->DoModify($SCName);
			
			if ($this->uriIsModified) {
				$this->SCName = $SCName;
			}
		}
	}
	
	function _InitializeModuleType() {
		if (isset($_GET["writemodule"])) {
			$this->moduleType = "write";
			$this->writeModuleName = $_GET["writemodule"];
			$this->writeRequestMethod = "GET";
			$this->requestMethod = "GET";
		} else if (isset($_POST["writemodule"])) {
			$this->moduleType = "write";
			$this->writeModuleName = $_POST["writemodule"];
			$this->writeRequestMethod = "POST";
			$this->requestMethod = "POST";
		} else if (isset($_GET["jsmodule"])) {
			$this->moduleType = "js";
			$this->jsModuleName = $_GET["jsmodule"];
		} else if (isset($_POST["jsmodule"])) {
			$this->moduleType = "js";
			$this->jsModuleName = $_POST["jsmodule"];
		} else {
			$this->moduleType = "read";
			$this->requestMethod = "GET";
		}
	}

	function _InitializeAction() {
		// Если разрешено использование ser и всегда используется XML-выдача - ставим action в "xml"
		if ($this->conf->Param("AllowSer") && $this->conf->Param("AlwaysXML")) {
			$this->action = "xml";
			return;
		}

		// Если выдача XML при любых параметрах отключена, но использование ser разрешено - читаем параметр ser и в соответствии с ним выставляем action
		if ($this->conf->Param("AllowSer") and $this->GetParam("ser")) {
			switch ($this->GetParam("ser")) {
				case "normal": 
					$this->action = "normal"; 
					break;
					
				case "xml": 
					$this->action = "xml"; 
					break;
					
				case "txml": 
					$this->action = "txml"; 
					break;
					
				case "xslt": 
					$this->action = "xsl"; 
					break;
					
				case "xsl": 
					$this->action = "xsl"; 
					break;
					
				case "sql": 
					$this->action = "sql"; 
					break;
				
				default: 
					$this->error->StopScript(
						"QueryClass", 
						"Ошибочное значение параметра GET 'ser'"
					);
			}
			
			return;
		}

		// Если ser запрещён, ставим для action дефолтное значение "normal"
		$this->action = "normal";
	}

	private function _InitializeServerParams() {
		$this->referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
		$this->userAgent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
		
		$this->host = $_SERVER["HTTP_HOST"];
		$this->userIP = $_SERVER["REMOTE_ADDR"];
	}

	private function _DecodeRetErrPaths() {
		if ($this->conf->Param("StaticURL")) {
			if (isset($_GET["retpath"])) {
				$_GET["retpath"] = rawurldecode(rawurldecode($_GET["retpath"]));
			}
			if (isset($_GET["errpath"])) {
				$_GET["errpath"] = rawurldecode(rawurldecode($_GET["errpath"]));
			}
		} else {
			if (isset($_GET["retpath"])) {
				$_GET["retpath"] = rawurldecode($_GET["retpath"]);
			}
			if (isset($_GET["errpath"])) {
				$_GET["errpath"] = rawurldecode($_GET["errpath"]);
			}
		}
	}
	
}

?>