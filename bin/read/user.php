<?php

class UserReadClass extends ReadModuleBaseClass {
	
	public function CreateXML() {
		$userID = $this->_GetParam("id");
		if ($userID && !IsGoodId($userID)) {
			return false;
		}

		if ($this->_GetParam("id") && $this->_GetParam("id") == $this->auth->GetUserID()) {
			$this->redirectPath = "http://" . $this->queryClass->GetHost() . $this->conf->Param("Prefix") . "profile/";
		}
		
		if ($userID) {
			$roleName = $this->db->GetValue("SELECT r.name FROM dt_user u JOIN sys_roles r ON u.role_id = r.id WHERE u.id = {$userID}");
		} else {
			$roleName = $this->db->quote($this->_GetSimpleParam("rolename"));
		}
		
		$dts = array();
		$dts[] = "user";
		if ($userID or $this->_GetSimpleParam("rolename")) {
			$additionalData = "user_" . $roleName;
			if (isset($this->dtconf->dtn[$additionalData])) $dts[] = $additionalData;
		}

		if ($userID) {
			$this->xslList[] = array("filename" => $this->dtconf->dtt["user"], "match" => "document[@docTypeName = 'user']");
			$this->parentNode->setAttribute("documentID", $userID);
			foreach ($dts as $dtName) {
				$this->dt->GetFieldList($this->xml, $this->parentNode, $dtName);
				if (!$this->_CreateUserDataXML($dtName, $userID)) {
					return false;
				}
			}
		} else if ($this->_GetSimpleParam("rolename")) {
			$this->xslList[] = array("filename" => $this->dtconf->dtt["user"], "match" => "doctype[@name = 'user']");
			foreach ($dts as $dtName) {
				$this->dt->GetFieldList($this->xml, $this->parentNode, $dtName);
			}
		} else {
			if (!$this->_CreateUserDataXML("user", 0)) {
				return false;
			}
		}
		
		$this->_GetUserRoleName();

		return true;
	}

	private function _CreateUserDataXML($dtName, $userID) {
		$whereClause = ($userID) ? ("dt.id = " . $userID) : "";
		
		if ("superadmin" != $this->auth->GetRoleName() && "user" == $dtName) {
			$whereClause .= ($userID) ? " AND " : "";
			$whereClause .= " dt.role_id != 3";
		}
		
		$stmt = $this->dt->FormatSelectQuery($dtName, $this->xml, $this->parentNode, "*", "", "", $whereClause);
		if (!$stmt->rowCount()) {
			return false;
		}
		
		$this->dt->Select2XML_V2($stmt, $this->xml, $this->parentNode, $dtName, "", $this->thisID, true, true);
		return true;
	}
	
	private function _GetUserRoleName() {
		$rolesNode = X_CreateNode($this->xml, $this->parentNode, "roles");
		
		$where = "name != 'superadmin'";
		if ("admin" == $this->auth->GetRoleName()) {
			$where .= " AND name != 'admin'"; 
		}
		
		$stmt = $this->db->SQL("SELECT name, title FROM sys_roles WHERE {$where};");
		
		while ($role = $stmt->fetchObject()) {
			$roleNode = X_CreateNode($this->xml, $rolesNode, "role", $role->title);
			$roleNode->setAttribute("name", $role->name);
		}
	}
}
?>