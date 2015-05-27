<?php
/**
 * Работа с правами секции
 * @author fred
 */
class SectionRights {
	/**
	 * Класс работы с БД
	 *
	 * @var DBClass
	 */
	var $db;

	var $error = false;
	var $rights;
	var $defaultRights;

	var $sectionId;
	var $roleId;

	/**
	 * @param int $roleId
	 * @param int $sectionId
	 * @return SectionRights
	 */
	public function __construct($roleId, $sectionId) {
		$this->db = DBClass::GetInstance();
		$this->sectionId = $sectionId;
		$this->roleId = $roleId;

		if (!IsGoodId($this->sectionId)) {
			$this->error = "BadSection";
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
	 * Возвращает строку с правами на секцию
	 *
	 * @return string
	 */
	function GetRights() {
		return (is_string($this->rights) and strlen($this->rights) == 6) ? $this->rights : $this->defaultRights;
	}

	function GetRightsArray() {
		$rightsStr = $this->GetRights();
		$rights = array();
		$rights["Read"] = ($rightsStr[0] == 1);
		$rights["Create"] = ($rightsStr[1] == 1);
		$rights["Edit"] = ($rightsStr[2] == 1);
		$rights["Delete"] = ($rightsStr[3] == 1);
		$rights["EditName"] = ($rightsStr[4] == 1);
		$rights["EditEnabled"] = ($rightsStr[5] == 1);
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
	 * Обновляет права на секцию
	 *
	 * @param string $newRights
	 */
	function UpdateRights($newRights) {
		if (!preg_match("~^[0-1]{6}$~", $newRights)) {
			$this->error = "BadNewRights";
			return;
		}

		if ($this->rights and $newRights != $this->defaultRights) {
			$this->db->SQL("UPDATE sys_section_rights SET rights = '{$newRights}' WHERE section_id = {$this->sectionId} AND role_id = {$this->roleId}");
		} elseif($newRights != $this->defaultRights) {
			$this->db->SQL("INSERT INTO sys_section_rights (section_id, role_id, rights) VALUES ({$this->sectionId}, {$this->roleId}, '{$newRights}')");
		} else {
			$this->db->SQL("DELETE FROM sys_section_rights WHERE section_id = {$this->sectionId} AND role_id = {$this->roleId}");
		}
	}

	function _SetDefaultRights() {
		$this->defaultRights = $this->db->GetValue("SELECT defsectionrights FROM sys_roles WHERE id = {$this->roleId}");
	}

	function _SetRights() {
		$this->rights = $this->db->GetValue("SELECT rights FROM sys_section_rights WHERE section_id = {$this->sectionId} AND role_id = {$this->roleId}");
	}
}
?>