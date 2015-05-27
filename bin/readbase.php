<?php

class ReadModuleBaseClass extends BaseClass {
	
	public $redirectPath = "";

	/**
	 * ID текущей связи
	 */ 
	protected $thisID;
	
	/**
	 * Строка запроса без каких-либо изменений
	 */     
	protected $query;
	
	/**
	 * @var DOMDocument
	 */
	protected $xml;

	/**
	 * @var DOMElement
	 */
	protected $parentNode;
	
	// Имя текущей секции
	protected $SCName;
	// Referer     
	protected $referer;
	// Параметры инициализации модуля    
	protected $params;
	// Список подключаемых к странице xsl-файлов     
	protected $xslList;
	
	/**
	 * @var array
	 */    
	protected $headers;

	/**
	 * @var array
	 */
	protected $rights;
	
	/** 
	 * Флаг админского режима ($rights["CreateEnabled"] || $rights["Edit"] || $rights["Delete"])
	 */
	protected $adminMode;
	
	protected $uriIsModified;
	protected $badParamsDescr = "";
	protected $accessDeniedReason = "";
	protected $accessDenied = false;

	protected $varDumpNeeded = false;
	protected $errorList = array();
	protected $infoList = array();
		
	/**
	 * @var QueryClass
	 */
	private $queryClass;
	
	public function __construct(
		$thisID, $queryClass, DOMDocument $xml, $parentNode, 
		&$params, array &$xslList, &$headers, array &$rights, $adminMode
	) {
		parent::__construct();
		
		$this->queryClass = $queryClass;
				
		$this->thisID = $thisID;
		$this->xml = $xml;
		$this->parentNode = $parentNode;
		$this->params = &$params;
		$this->xslList = &$xslList;
		$this->headers = &$headers;
		$this->rights = &$rights;
		$this->adminMode = $adminMode;
		
		$this->query = $this->queryClass->GetQuery();
		$this->SCName = $this->queryClass->GetSCName();
		$this->referer = $this->queryClass->GetReferer();
		$this->uriIsModified = $this->queryClass->IsURIModified();
	}

	/**
	 * Переопределяемая ф-ция
	 * Возвращает false, если error404 или ошибка входных параметров.
	 * Если $module->GetBadParamsDescr() != "", значит, ошибка в параметрах.
	 * В полученной строке - описание ошибки
	 */
	public function CreateXML() {
		// Эта штука должна быть вызвана в переопределяемой ф-ции, если она хочет получить входные параметры
		eval($this->params);
	}

	public function GetBadParamsDescr() {
		return $this->badParamsDescr;
	}
	
	public function WriteStoredInfo($parentNode, $xml) {
		// делаем дамп переменных
		if ($this->_IsVarDumpNeeded()) {
			$varsNode = $xml->createElement("rVars");
			
			$this->DumpArray($varsNode, $xml, $this->thisID, $_GET);
			$this->DumpArray($varsNode, $xml, $this->thisID, $_POST);
			
			$parentNode->appendChild($varsNode);
		}

		// записываем информацию об ошибках
		if (count($this->errorList) !== 0) {
			$baseNode = $xml->createElement("rError");
			$this->ExportInfoPart($baseNode, $xml, $this->errorList);
			
			$parentNode->appendChild($baseNode);
		}
		
		// записываем информацию о сообщениях
		if (count($this->infoList) !== 0) {
			$baseNode = $xml->createElement("rInfo");
			$this->ExportInfoPart($baseNode, $xml, $this->infoList);
			
			$parentNode->appendChild($baseNode);
		}
	}

	protected function _SetBadParamsDescr($descr) {
		$this->badParamsDescr = $descr;
	}

	public function GetAccessDenied() {
		return $this->accessDenied;
	}

	public function GetAccessDeniedReason() {
		return $this->accessDeniedReason;
	}
	
	/**
	 * Этой штукой надо пользоваться очень осторожно - при вызове ф-ции с параметром true доступ
	 * закрывается ко всей секции, а не только к тому модулю.
	 */
	protected function _SetAccessDenied($accessDeniedReason) {
		$this->accessDenied = true;
		$this->accessDeniedReason = $accessDeniedReason;
	}

	public function URLReplaceOwnParam($paramName, $paramValue)	{
		$q = $this->query;
		$q = preg_replace("/[&?]$/", "", $q);
		$paramName = "r" . $this->thisID . "_" . $paramName;
		if (preg_match("/{$paramName}=[^&]$/", $q) > 0) {
			return preg_replace("/{$paramName}=[^&]$/", "{$paramName}={$paramValue}", $q);
		}
		if (preg_match("/{$paramName}=[^&]&/", $q) > 0) {
			return  preg_replace("/{$paramName}=[^&]&/", "{$paramName}={$paramValue}&", $q);
		}
		if (preg_match("/\?/", $q) > 0) {
			return "{$q}&{$paramName}={$paramValue}";
		} else {
			return "{$q}?{$paramName}={$paramValue}";
		}
	}
	
	/**
	 * Вычищает содержимое (документы, картинки, ...) для данного модуля
	 *
	 * @param int $thisID идентификатор модуля
	 * @param string $params список параметров
	 * @static
	 */
	static public function Clean($thisID, $params) {
		$inDTName = "";
		
		eval($params);
		if (!trim($inDTName)) {
			return;
		}
		
		$db = DBClass::GetInstance();
		$dtConf = DTConfClass::GetInstance();
		
		$tableName = DocCommonClass::GetTableName($inDTName);						
				
		$stmt = $db->SQL("SELECT * FROM {$tableName} WHERE ref = {$thisID}");
		while ($row = $stmt->fetchObject()) {
			foreach ($dtConf->dtf[$inDTName] as $idx => $val) {
				$type = $val["type"];
				if ($type == "file" or $type == "image") {
					$table = ($type == "file") ? "sys_dt_files" : "sys_dt_images";
					$filename = $db->GetValue("SELECT filename FROM {$table} WHERE id = {$row->$idx}");
					if ($filename) {
						$filePath = CMSPATH_UPLOAD . $filename;
						if (file_exists($filePath)) {
							unlink($filePath);
						}
					}
					
					$db->SQL("DELETE FROM sys_dt_files WHERE id = {$row->$idx}");
				} elseif ($type == "select") {
					$db->SQL("DELETE FROM sys_dt_select WHERE id = {$row->$idx}");
				} elseif ($type == "strlist")  {
					$db->SQL("DELETE FROM sys_dt_strlist WHERE id = {$row->$idx}");
				}
			}
			
			$db->SQL("DELETE FROM {$tableName} WHERE id = {$row->id}");
		}
	}
	
	protected function _GetParam($paramName)	{
		return $this->queryClass->GetParam("r" . $this->thisID . "_" . $paramName);
	}
	
	protected function _GetSimpleParam($paramName)	{
		return $this->queryClass->GetParam($paramName);
	}
	
	protected function _DumpVars() {
		$this->varDumpNeeded = true;
	}
	
	protected function _IsVarDumpNeeded() {
		return $this->varDumpNeeded;
	}

	protected function _WriteInfo($name, $descr = "") {
		$this->infoList[] = array("name" => $name, "descr" => $descr);
	}

	protected function _WriteError($name, $descr = "") {
		$this->errorList[] = array("name" => $name, "descr" => $descr);
	}

	private function DumpArray($parentNode, $xml, $refID, &$array) {
		foreach ($array as $idx => $val) {
			$val = htmlspecialchars($val);
			if (strlen($idx) > 255) {
				continue;
			}
			
			if (preg_match("~^r([0-9]){1,11}_(.*)$~", $idx, $match) > 0) {
				if ((int)$match[1] != $refID) {
					continue;
				}
				$match[2] = htmlspecialchars($match[2]);

				$newNode = $xml->createElement("var", $match[2]);
				$newNode->setAttribute("name", $idx);
			} else {
				$idx = htmlspecialchars($idx);

				$newNode = $xml->createElement("var", $val);
				$newNode->setAttribute("name", $idx);
			}
			
			$parentNode->appendChild($newNode);
		}
	}

	private function ExportInfoPart($parentNode, $xml, array $array) {
		foreach ($array as $item) {
			$idx = htmlspecialchars($item['name']);
			$newNode = $xml->createElement("item", htmlspecialchars($item['descr']));
			$newNode->setAttribute("name", $idx);
			
			$parentNode->appendChild($newNode);
		}
	}
	

}

?>