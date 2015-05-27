<?php
require_once(CMSPATH_LIB . "rights/modulerights.php");

class ModuleRightsReadClass extends ReadModuleBaseClass {

	public function CreateXML() {
		if ("superadmin" != $this->auth->GetRoleName()) {
			return true;
		}

		$stmt = $this->db->SQL("
			SELECT 
				r.id AS refId,
				r.params AS refParams,
				ro.title AS roleTitle,
				ro.id AS roleId
			FROM 
				sys_references r
			JOIN 
				sys_sections s ON s.id = r.ref AND s.name = '{$this->SCName}'
			JOIN 
				sys_roles ro
			WHERE 
				ro.name != 'superadmin'
			ORDER BY
				r.id ASC, ro.title ASC
		");
		
		$i = -1;
		while ($role = $stmt->fetchObject()) {
			if ($role->refId != $i) {
				$i = $role->refId;
				$module = $this->_CreateModuleNode($role);
			}
			
			$moduleRights = new ModuleRights($role->roleId, $role->refId);
			
			$node = X_CreateNode($this->xml, $module, "right");
			$node->setAttribute("roleId", $role->roleId);
			$node->setAttribute("roleTitle", $role->roleTitle);

			$rights = $moduleRights->GetRightsArray();
			$node->setAttribute("read", $rights["Read"] ? 1 : 0);
			$node->setAttribute("create", $rights["Create"] ? 1 : 0);
			$node->setAttribute("createEnabled", $rights["CreateEnabled"] ? 1 : 0);
			$node->setAttribute("edit", $rights["Edit"] ? 1 : 0);
			$node->setAttribute("delete", $rights["Delete"] ? 1 : 0);
		}
		
		return true;
	}
	
	private function _CreateModuleNode(&$role) {	
		$module = X_CreateNode($this->xml, $this->parentNode, "module");
		$module->setAttribute("id", $role->refId);
		
		if (
			$role->refParams 
			&& preg_match("~\\\$inDTName\s=\s\"([a-zA-Z0-9]+)\"~", $role->refParams, $dtName)
		) {
			$module->setAttribute("docTypeName", $this->dtconf->dtn[$dtName[1]]);
		}
		
		return $module;
	}
}

?>