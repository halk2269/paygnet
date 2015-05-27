<?php

/**
 * Класс, проверяющий права на редактирование документа
 */

class DocCommonClass {
	
	private $rights = array();
	private $params = array();
	private $enabled = false;

	private $paramsInStr = "";

	private $badRights;

	/**
	 * @var DTConfClass
	 */
	private $dtconf;
	
	/**
	 * @var DBClass
	 */
	private $db;
	
	/**
	 * @var AuthClass
	 */
	private $auth;

	public function __construct() {
		$this->dtconf = DTConfClass::GetInstance();
		$this->auth = AuthClass::GetInstance();
		$this->db = DBClass::GetInstance();
	}
	
	static public function GetInstance() {
		static $instance;
		
		if (!is_object($instance)) {
			$instance = new DocCommonClass();
		}
		
		return $instance;
	}

	/**
	 * @param string $dtName
	 * @param boolean $disableDTPrefix
	 * @return string
	 */
	static public function GetTableName($dtName, $disableDTPrefix = false) {
		$dtConf = DTConfClass::GetInstance();
		
		return (isset($dtConf->dttbl[$dtName])) 
			? $dtConf->dttbl[$dtName] 
			: ($disableDTPrefix ? "" : "dt_") . $dtName;
	}
	
	/**
	 * @param string $act
	 * @param string $className
	 * @param int $qref
	 * @param int $docId
	 * 
	 * @return bool
	 */
	public function CheckRights($act, $className, $qref, $docId) {
		if (false === $qref || !IsGoodNum($qref)) {
			return false;
		}
				
		if (false === $docId || !IsGoodNum($docId)) {
			return false;
		}

		$this->_SetRights($qref, $className);
		$this->_SetParams($qref);

		if (!$this->GetDtName()) {
			return false;
		}
				
		if (!isset($this->dtconf->dtf[$this->GetDtName()])) {
			return false;
		}

		$this->badRights = ($this->isRefRightsOK($act)) ? false : true;
		if ($this->badRights) {
			return false;
		}
		
		$docId = (int)$docId;

		if ($docId && !$this->isDocExist($qref, $docId)) {
			return false;
		}

		if (!$this->isChangesInRefAllowed($qref, $docId)) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function isBadRights() {
		return ($this->badRights) ? true : false;
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function GetParam($name) {
		return (isset($this->params[$name])) ? $this->params[$name] : null;
	}

	/**
	 * Возвращает строку с параметрами из базы данных до eval()
	 * @return string
	 */
	public function GetParams() {
		return $this->paramsInStr;
	}

	/**
	 * Возвращает право
	 * @param string $rightName
	 * @return bool
	 */
	public function GetRight($rightName) {
		return (isset($this->rights[$rightName])) ? $this->rights[$rightName] : false;
	}

	/**
	 * Проверяет, можно ли в данном референсе создавать документы с enabled = 1
	 * @return bool
	 */
	public function CanCreateEnabled() {
		return $this->GetRight("CreateEnabled");
	}

	/**
	 * Возвращает название ТД для данного референса
	 * @return string
	 */
	public function GetDtName() {
		return $this->GetParam("inDTName");
	}

	/**
	 * Выгружаем для данного ref права на запись 
	 * из базы в поле rights
	 * 
	 * @param int $qref
	 * @param string $className
	 */
	private function _SetRights($qref, $className) {
		$rightsStr = $this->db->GetValue("
			SELECT 
				rights 
			FROM 
				sys_docwrite_rights 
			WHERE 
				(role_id = " . $this->auth->GetRoleID() . " OR role_id = 0) 
				AND (ref = {$qref} OR ref = 0) 
				AND (write_class = '{$className}' OR write_class = '%') 
			ORDER BY id DESC 
			LIMIT 1
		");

		if ($rightsStr) {
			$this->rights["Create"] = ($rightsStr[0] == 1);
			$this->rights["CreateEnabled"] = ($rightsStr[1] == 1);
			$this->rights["Edit"] = ($rightsStr[2] == 1);
			$this->rights["Delete"] = ($rightsStr[3] == 1);
		} else {
			$adminMode = 0;
			$this->rights = $this->auth->GetRefRights($qref, $adminMode);
		}
	}

	/**
	 * Выгружаем параметры для данного референса из базы
	 * @param int $qref
	 */
	private function _SetParams($qref) {
		$this->paramsInStr = $this->db->GetValue("SELECT params FROM sys_references WHERE id = {$qref}");
		eval($this->paramsInStr);

		if (isset($inDTName)) {
			$this->params["inDTName"] = $inDTName;
		}
		if (isset($inSelectRef)) {
			$this->params["inSelectRef"] = $inSelectRef;
		}
	}

	/**
	 * @param int $qref
	 * @param int $id
	 * @return int
	 */
	function _GetDocEnabled($qref, $id) {
		$dtName = $this->GetParam("inDTName");
		$tblName = self::GetTableName($dtName);
		// документ может относится к нескольким категориям
		if (isset($this->dtconf->dtm[$dtName]) and $this->dtconf->dtm[$dtName]) {
			return (int)$this->db->GetValue("
					SELECT 
						dt.enabled 
					FROM
						{$tblName} dt
						JOIN dt_{$dtName}_ref dtr ON dtr.doc_id = dt.id
					WHERE 
						dt.id = {$id} 
						AND dtr.cat_id = {$qref}
			");
		} else {
			return (int)$this->db->GetValue("SELECT enabled FROM {$tblName} WHERE id = {$id} AND ref = {$qref}");
		}
	}

	/**
	 * Проверка на существование документа
	 * @param int $qref
	 * @param int $id
	 * @return bool
	 */
	private function isDocExist($qref, $id) {
		$dtName = $this->GetParam("inDTName");
		$tblName = self::GetTableName($dtName);
		
		// документ может относиться к нескольким категориям
		if (isset($this->dtconf->dtm[$dtName]) && $this->dtconf->dtm[$dtName]) {
			return $this->db->RowExists("
					SELECT 
						dt.id
					FROM
						{$tblName} dt
						JOIN dt_{$dtName}_ref dtr ON dtr.doc_id = dt.id
					WHERE 
						dt.id = {$id} 
						AND dtr.cat_id = {$qref}
			");
		} else {
			return $this->db->RowExists("SELECT id FROM {$tblName} WHERE id = {$id} AND ref = {$qref}");
		}
	}

	/**
	 * Проверка права
	 * @param string $act
	 * @return bool
	 */
	private function isRefRightsOK($act) {
		return ($this->GetRight($act) || ($act == "Create" && $this->GetRight("CreateEnabled")));
	}

	/**
	 * Является ли референс нужного нам типа
	 *
	 * @param id $qref
	 * @param id $id
	 * @return bool
	 */
	private function isChangesInRefAllowed($qref, $id) {
		return (
			!$this->GetParam("inSelectRef") 
			|| ("own" == $this->GetParam("inSelectRef")) 
			|| ("owndeep" == $this->GetParam("inSelectRef"))
		);
	}

}

?>