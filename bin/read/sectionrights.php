<?php

require_once(CMSPATH_LIB . "rights/sectionrights.php");

class SectionRightsReadClass extends ReadModuleBaseClass {
	
	public function CreateXML() {
		if ("superadmin" != $this->auth->GetRoleName()) {
			return true;
		}
		
		$sectionId = $this->_GetSimpleParam("id");
		if (!IsGoodNum($sectionId)) {
			return true;
		}
		
		$stmt = $this->db->SQL(
			"SELECT id, title FROM sys_roles WHERE name != 'superadmin'"
		);
		while ($role = $stmt->fetchObject()) {
			$sectionRights = new SectionRights($role->id, $sectionId);
			$node = X_CreateNode($this->xml, $this->parentNode, "right");
			$node->setAttribute("roleId", $role->id);
			$node->setAttribute("roleTitle", $role->title);
			
			$rights = $sectionRights->GetRightsArray();
			$node->setAttribute("read", $rights["Read"] ? 1 : 0);
			$node->setAttribute("create", $rights["Create"] ? 1 : 0);
			$node->setAttribute("edit", $rights["Edit"] ? 1 : 0);
			$node->setAttribute("delete", $rights["Delete"] ? 1 : 0);
			$node->setAttribute("editName", $rights["EditName"] ? 1 : 0);
			$node->setAttribute("editEnabled", $rights["EditEnabled"] ? 1 : 0);
		}
		
		return true;
	}
}

?>