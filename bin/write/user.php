<?php

require_once(CMSPATH_MOD_WRITE . "abstractuser.php");

class UserWriteClass extends AbstractUserWriteClass {

	public function MakeChanges() {
		if (!$this->_GetParam("ref") || !IsGoodId($this->_GetParam("ref"))) {
			return false;
		}
		$this->userRef = $this->_GetParam("ref");

		if ($this->_GetParam("id") and !IsGoodId($this->_GetParam("id"))) {
			return false;
		}
		$this->userId = (int)$this->_GetParam("id");
		
		/*
		if (!$this->_CheckRights()) {
			return false;
		}
		*/
		
		$this->_SetRoleData();
		if ($this->_IsAdminEditingSuperAdmin()) {
			return false;
		}

		if (false === $this->_GetParam("login")) {
			return false;
		}

		if ($this->_IsLoginPassedExists()) {
			$this->_WriteError("UserExists", $this->_GetParam("login"));
		}

		if (!$this->_IsEmailUnique()) {
			$this->_WriteError("DuplicateEmail", $this->_GetParam("email"));
		}

		$this->_SetParam("role_id", (string)$this->userRoleId);

		$rvResult = $this->_WriteToDB();
		if (!$rvResult) {
			return false;
		} else {
			$act = ($this->userId) ? "Edit" : "Create";
			$this->_SendNotify($this->userRef, $act, "user", $this->userId);
		}
		
		// если запись прошла успешно, пытаемся записать ту же информацию о пользователе и для форума
		if ($this->conf->Param("IPBIntegration")) {
			$this->_IPBIntegration();
		}

		return true;

	}

	protected function _SetRoleData() {
		if ($this->userId) {
			$role = $this->db->GetRow("
				SELECT 
					r.name AS 'name', r.id AS 'id' 
				FROM 
					dt_user u 
				JOIN 
					sys_roles r ON u.role_id = r.id 
				WHERE 
					u.id = {$this->userId}
			");
		} else {
			$role = $this->db->GetRow(
				"SELECT id, name FROM sys_roles WHERE name = ?",
				array($this->_GetParam("rolename"))
			);
		}

		$this->userRoleId = $role->id;
		$this->userRoleName = $role->name;
	}

	function _IsAdminEditingSuperAdmin() {
		if ("superadmin" == $this->userRoleName && "superadmin" != $this->auth->GetRoleName()) {
			return true;
		}
	}

	function _CheckRights() {
		$qref = "";
		$dtName = "user";
		$params = "";
		$canCreateEnabled = false;
		$act = ($this->_GetParam("id")) ? "Edit" : "Create";
		$id = "";

		return $this->_CheckSecurity($act, $qref, $id, $dtName, $canCreateEnabled, $params);
	}
}

?>