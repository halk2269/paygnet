<?php

class WriteModuleBaseClass extends BaseClass {
	
	/**
	 * @var PassInfoClass
	 */
	protected $passInfo;
	
	/**
	 * @var QueryClass
	 */
	protected $query;
		
	protected $requestMethod; // GET or POST
	protected $referer;
	protected $host;
	protected $prefix;

	// ID запрошенного модуля
	protected $thisID; 
	
	protected $retPath; // Переход в случае успешного завершения работы модуля
	protected $errPath; // Переход в случае ошибки
	protected $redirectPath = ""; // Куда же мы всё-таки переходим
	
	// Модуль чтения, из которого происходит вызов модуля записи
	protected $ref; 
	// Ошибка входных данных
	protected $inputError = false;
	// В базе нет связи с таким id, или данная связь имеет enabled == 0
	protected $refError = false;

	// Устанавливаются в конструкторе
	
	// Класс того модуля чтения, на который ссылается $ref
	protected $readClass;
	// Параметры инициализации того модуля чтения, на который ссылается $ref
	protected $readParams; 
	protected $rights;

	protected $errorList = array();
	protected $infoList = array();
	
	protected $varDumpNeeded = false;
	protected $errorOccured = false;

	public function __construct($passInfo, $query, $thisID) {
		parent::__construct();
		
		$this->query = $query;
		$this->passInfo = $passInfo;
		$this->thisID = $thisID;
		
		$this->requestMethod = $this->query->GetWriteRequestMethod();
		$this->referer = $this->query->GetReferer();
		$this->host = $this->query->GetHost();
		$this->prefix = $this->conf->Param("Prefix");

		$retpath = $this->_GetParam("retpath");
		$this->retPath = ($retpath !== false) ? $retpath : $this->referer;
		
		$errpath = $this->_GetParam("errpath");
		$this->errPath = ($errpath !== false) ? $errpath : $this->referer;
		
		$ref = $this->_GetParam("ref");
		$ref = (IsGoodNum($ref)) ? (int)$ref : 0;
		
		$this->ref = $ref;
		if (!$this->ref) {
			return;
		}
				
		$stmt = $this->db->SQL(
			"SELECT class, params FROM sys_references WHERE id = {$this->ref} AND enabled = 1"
		);
		
		$this->refError = ($stmt->rowCount() != 1);
		if (!$this->refError) {
			$row = $stmt->fetchObject();
			$this->readClass = $row->class;
			$this->readParams = $row->params;
		}
		
		$this->rights = $this->auth->GetRefRights($this->ref, $adminMode);
		$this->passInfo->CleanPassInfoAndVars($this->ref);
	}

	/**
	 * Переопределяемая функция. Возвращает, успешно ли всё произошло.
	 * Если true, перенаправляем пользователя на $retPath, иначе на $errPath;
	 */ 
	public function MakeChanges() {
		return false;
	}

	// Возвращает строку редиректа после отработки модуля
	public function GetRedirectPath($success) {
		if ($this->inputError) {
			return $this->conf->Param("Prefix");
		} elseif ($this->redirectPath == "") {
			$this->redirectPath = ($success) ? $this->retPath : $this->errPath;
			return ($this->redirectPath == "") ? $this->conf->Param("Prefix") : $this->redirectPath;
		} else {
			return $this->redirectPath;
		}
	}
	
	public function IsRefError() {
		return $this->refError;
	}
	
	// true, если была вызвана _WriteError
	public function IsErrorOccured() {
		return $this->errorOccured;
	}

	public function WriteStoredInfo() {
		if (!$this->ref) {
			return;
		}
				
		foreach ($this->errorList as $value) {
			$this->passInfo->WriteError($this->ref, $value["name"], $value["descr"]);
		}
		
		foreach ($this->infoList as $value) {
			$this->passInfo->WriteInfo($this->ref, $value["name"], $value["descr"]);
		}
		
		if ($this->_IsVarDumpNeeded()) {
			$this->passInfo->DumpVars($this->ref);
		}
	}

	protected function _SetParam($paramName, $paramValue) {
		if ($this->requestMethod == "GET") {
			$_GET[$paramName] = $paramValue;
		} elseif ($this->requestMethod == "POST") {
			$_POST[$paramName] = $paramValue;
		} else {
			return false;
		}
		return true;
	}
	
	protected function _UnsetParam($paramName) {
		unset($_GET[$paramName]);
		unset($_POST[$paramName]);
		
		return true;
	}

	/**
	 * Приватная ф-ция, возвращает переданный параметр в зависимости от того, 
	 * каким образом был получен запрос - через GET или POST. 
	 * Нет параметра - возвращает false.
	 *
	 * @param string $paramName
	 * @return string
	 */
	protected function _GetParam($paramName) {
		return $this->query->GetParam($paramName);
	}
	
	protected function _WriteInfo($name, $descr = "") {
		$this->infoList[] = array("name" => $name, "descr" => $descr);
	}

	protected function _WriteError($name, $descr = "") {
		$this->_ErrorOccured();
		$this->errorList[] = array("name" => $name, "descr" => $descr);
	}

	protected function _NeedDumpVars() {
		$this->varDumpNeeded = true;
	}
	
	protected function _IsVarDumpNeeded() {
		return $this->varDumpNeeded;
	}

	protected function _ErrorOccured() {
		$this->errorOccured = true;
	}
	
	protected function _GetRMRights($allowWM = "", &$adminMode) {
		if (!$this->ref) {
			return false;
		}
				
		if ($allowWM != "") {
			$params = $this->db->GetValue("SELECT params FROM sys_references WHERE id = {$this->ref}");
			if (!$params) {
				return false;
			}
			
			eval($params);
			
			if (!isset($inAllowWM) || $inAllowWM != $allowWM) {
				return false;
			}
		}
		
		return $this->auth->GetRefRights($this->ref, $adminMode);
	}

}

?>