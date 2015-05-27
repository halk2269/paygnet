<?php

require_once(CMSPATH_BIN . 'base.php');        // Базовый класс
require_once(CMSPATH_BIN . 'auxil.php');       // Набор хороших и полезных функций
require_once(CMSPATH_BIN . 'time.php');        // Время работы скрипта
require_once(CMSPATH_BIN . 'query.php');       // Анализ запроса
require_once(CMSPATH_BIN . 'response.php');    // Ответ пользователю
require_once(CMSPATH_BIN . 'passinfo.php');    // Передача информации из модуля записи модулю чтения

/**
 * Главный класс, запускаемый из index.php
 */
class MainClass {
	
	/**
	 * @var GlobalConfClass
	 */
	private $conf;
	
	/**
	 * @var DBClass
	 */
	private $db;
	
	/**
	 * @var ErrorClass
	 */
	private $error;
	
	public function __construct() {
		$this->conf = GlobalConfClass::GetInstance();
		$this->db = DBClass::GetInstance();
		$this->error = ErrorClass::GetInstance();

		/**
		 * Если нужно подключать форум, то это надо делать до всего.
		 * Раньше этого мы инициализируем только GlobalConf, ибо
		 * он необходим и ничего за собой не тащит.
		 * С использованием данного метода получаем объект класса AuthClass.
		 */
		if ($this->conf->Param('IPBIntegration')) {
			$this->IPBIntegration();
		}
	}
	
	static public function Clean() {
		$this->db->Close();
	}

	public function Run() {
		ini_set('error_log', CMSPATH_LOG . "error.log");
		// Если используем PEAR, то инициализируем путь к этой библиотеке
		if ($this->conf->Param('UsePEAR')) {
			ini_set('include_path', CMSPATH_LIB . 'pear/' . PATH_SEPARATOR . ini_get('include_path'));
		}
				
		date_default_timezone_set('Europe/Moscow');
		setlocale(LC_ALL, 'ru_RU.UTF8');
		
		mb_internal_encoding('UTF-8');
		mb_regex_encoding('UTF-8');
		
		ignore_user_abort(true);
				
		// Засекаем время начала работы скрипта (условно, от начала работы скрипта уже пройдёт некоторое время, пренебрежимо малое)
		$time = new TimeClass();

		// Класс анализа запроса
		$query = new QueryClass();

		// не учитываются 2 запроса на выборку из sys_var_ints и sys_var_strings
		// Запрошен ли sql-debug?
		$this->db->SetDebugMode('sql' == $query->GetAction());
		
		// Класс ответа клиенту
		$res = new ResponseClass($time, $query->GetAction());

		if ('read' == $query->GetModuleType()) {
			global $phpError;
			
			// Если запрошен модуль чтения
			require_once CMSPATH_BIN . 'xml.php'; // Активация модулей чтения, выдача XML
			require_once CMSPATH_BIN . 'readbase.php'; // Класс, базовый для модулей чтения
			require_once CMSPATH_BIN . 'cache.php'; // Класс для работы с кэшем

			// Класс работы с XML
			$xmlClass = new XMLClass(new PassInfoClass(), $query, $res);
			
			/**
			 * В буфере находится содержимое?
			 * Например, выдаётся содержимое файла 
			 */ 
			if ($query->GetAction() == 'normal' && ob_get_length() > 0 && !$phpError) {
				$headers = $xmlClass->GetHeaders();
								
				foreach ($headers as $header) {
					header($header);
				}
			} else {
				// Инициализируем переменную
				$xsl = null;

				// Если XSL нам нужен
				if (!$xmlClass->OnlyXMLNeeded() && $query->XSLNeed()) {
					require_once CMSPATH_BIN . 'xsl.php';
					$xslClass = new XSLClass($xmlClass->GetXSLList());
					
					// Получаем XSL в виде строки
					$xsl = $xslClass->GetXSL();
				}

				// Отвечаем клиенту
				$res->DoResponse($xmlClass->GetXML(), $xmlClass->GetXMLRoot(), $xsl, $xmlClass->OnlyXMLNeeded());
			}
		} else if ('write' == $query->GetModuleType()) {
			// Если запрошен модуль записи
			// Активация модулей записи, выдача адреса редиректа
			require_once CMSPATH_BIN . 'write.php'; 
			// Класс, активирующий нужный модуль записи
			$write = new WriteClass(new PassInfoClass(), $query);
			/**
			 * Активируем модуль. Если модуль не найден, переход на главную страницу сайта
			 * Стоит упаковать вызов StartModule() и Redirect() в конструктор класса
			 */
			$write->StartModule();
			
			// Переход на нужную страницу через header
			if (!$this->error->GlobalError()) {
				$res->Redirect($write->GetRedirectPath());
			}
		} else if ('js' == $query->GetModuleType()) {
			// Если запрошен js-модуль
			require_once CMSPATH_BIN . 'js.php';
			new JSClass($query, $res);
		}
	}

	public function IPBIntegration() {
		SwitchErrorHandler(); // отключаем собственный обработчик ошибок
		require_once('ipbsdk_class.inc.php');
		$IPB = new IPBSDK(
			array(
				'root_path' => 'forum/',
				'board_url' => $this->conf->Param('Prefix') . 'forum',
				'sdklang' => 'en',
				'board_version' => '',
				'allow_caching' => '1',
				'timer' => '',
				'debug' => ''
			)
		);
		SwitchErrorHandler(); // а теперь включаем собственный обработчик ошибок

		$auth = AuthClass::GetInstance();
		$auth->_SetIPBHandle($IPB);
	}
}
?>