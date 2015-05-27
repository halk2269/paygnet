<?php
require_once(CMSPATH_LIB . "rights/modulerights.php");
/**
 * Изменение прав на модули для всех ролей, кроме суперадмина
 */
class ModuleRightsWriteClass extends WriteModuleBaseClass {

	public function MakeChanges() {
		if ("superadmin" != $this->auth->GetRoleName()) {
			return true;
		}
	
		$sectionId = $this->_GetParam("section_id");
		if (!IsGoodNum($sectionId)) {
			return false;
		}

		$stmt = $this->db->SQL("
			SELECT 
				r.id AS moduleId,
				ro.id AS roleId
			FROM 
				sys_references r
				JOIN sys_roles ro
			WHERE ro.name != 'superadmin' AND r.ref = {$sectionId}
		");
		
		while ($role = $stmt->fetchObject()) {
			$moduleRightsModifier = new ModuleRights($role->roleId, $role->moduleId);
			$moduleRightsModifier->UpdateRights(
				$this->_GetNewRightsString($role->moduleId, $role->roleId)
			);
		}

		$this->_WriteInfo('ModuleRightsWasChanged');

		return true;
	}

	private function _GetNewRightsString($moduleId, $roleId) {
		$newRights = ($this->_GetParam("{$moduleId}_{$roleId}_read")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$moduleId}_{$roleId}_create")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$moduleId}_{$roleId}_createEnabled")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$moduleId}_{$roleId}_edit")) ? "1" : "0";
		$newRights .= ($this->_GetParam("{$moduleId}_{$roleId}_delete")) ? "1" : "0";
		
		return $newRights;
	}
}
?>