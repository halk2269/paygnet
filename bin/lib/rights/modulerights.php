<?php
/**
 * Работа с правами модуля
 * @author fred
 */
class ModuleRights {
	/**
	 * Класс работы с БД
	 *
	 * @var DBClass
	 */
	var $db;

	var $error = false;
	var $rights;
	var $defaultRights;

	var $moduleId;
	var $roleId;

	/**
	 * @param int $roleId
	 * @param int $moduleId
	 * @return ModuleRights
	 */
	public function __construct($roleId, $moduleId) {
		$this->db = DBClass::GetInstance();
		$this->moduleId = $moduleId;
		$this->roleId = $roleId;

		if (!IsGoodId($this->moduleId)) {
			$this->error = "BadModule";
			return;
		}

		if (!IsGoodId($this->roleId)) {
			$this->error = "BadRole";
			return;
		}

		$this->_SetDefaultRights();
		$this->_SetRights();
	}

	/**
	 * Возвращает строку с правами на модуль
	 *
	 * @return string
	 */
	function GetRights() {
		return (is_string($this->rights) and strlen($this->rights) == 5) ? $this->rights : $this->defaultRights;
	}

	function GetRightsArray() {
		$rightsStr = $this->GetRights();
		$rights = array();
		$rights["Read"] = ($rightsStr[0] == 1);
		$rights["Create"] = ($rightsStr[1] == 1);
		$rights["CreateEnabled"] = ($rightsStr[2] == 1);
		$rights["Edit"] = ($rightsStr[3] == 1);
		$rights["Delete"] = ($rightsStr[4] == 1);
		return $rights;
	}

	/**
	 * Возвращает строку с правами по-умолчанию
	 *
	 * @return string
	 */
	function GetDefaultRights() {
		return $this->defaultRights;
	}

	/**
	 * Возвращает ошибку, если она произошла
	 *
	 * @return string
	 */
	function GetError() {
		return $this->error;
	}

	/**
	 * Обновляет права на модуль
	 *
	 * @param string $newRights
	 */
	function UpdateRights($newRights) {
		if (!preg_match("~^[0-1]{5}$~", $newRights)) {
			$this->error = "BadNewRights";
			return;
		}

		if ($this->rights and $newRights != $this->defaultRights) {
			$this->db->SQL("UPDATE sys_ref_rights SET rights = '{$newRights}' WHERE ref_id = {$this->moduleId} AND role_id = {$this->roleId}");
		} elseif($newRights != $this->defaultRights) {
			$this->db->SQL("INSERT INTO sys_ref_rights (ref_id, role_id, rights) VALUES ({$this->moduleId}, {$this->roleId}, '{$newRights}')");
		} else {
			$this->db->SQL("DELETE FROM sys_ref_rights WHERE ref_id = {$this->moduleId} AND role_id = {$this->roleId}");
		}
	}

	function _SetDefaultRights() {
		$this->defaultRights = $this->db->GetValue("SELECT defrights FROM sys_roles WHERE id = {$this->roleId}");
	}

	function _SetRights() {
		$this->rights = $this->db->GetValue("SELECT rights FROM sys_ref_rights WHERE ref_id = {$this->moduleId} AND role_id = {$this->roleId}");
	}
}
?>