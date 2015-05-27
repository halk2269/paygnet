<?php
require_once(CMSPATH_LIB . "rights/sectionrights.php");
/**
 * Изменение прав на секцию для всех ролей, кроме суперадмина
 */
class SectionRightsWriteClass extends WriteModuleBaseClass {

	public function MakeChanges() {
		if ("superadmin" != $this->auth->GetRoleName()) {
			return true;
		}

		$sectionId = $this->_GetParam("section_id");
		if (!IsGoodNum($sectionId)) {
			return false;
		}

		$stmt = $this->db->SQL("SELECT id, title FROM sys_roles WHERE name != 'superadmin'");
		while ($role = $stmt->fetchObject()) {
			$sectionRightsModifier = new SectionRights($role->id, $sectionId);
			$sectionRightsModifier->UpdateRights($this->_GetNewRightsString($role->id));
		}
		
		$this->_WriteInfo('SectionRightsWasChanged');

		return true;
	}

	private function _GetNewRightsString($roleId) {
		$newRights = ($this->_GetParam("{$roleId}_read")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$roleId}_create")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$roleId}_edit")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$roleId}_delete")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$roleId}_editName")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$roleId}_editEnabled")) ? "1" : "0";
		
		return $newRights;
	}
}
?>