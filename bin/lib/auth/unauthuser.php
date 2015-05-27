<?php
/**
 * Класс неавторизованного пользователя
 */
class UnAuthUser {

	protected $roleID;
	protected $roleName;
	protected $roleTitle;
	protected $defRights;
	protected $defSectionRights;
	protected $dtSuperAccess;
	protected $listEdit;
	protected $uploadAllowed;

	/**
	 * @param object $roleData
	 * @return UnAuthUser
	 */
	public function __construct($roleData) {
		$this->roleID = $roleData->roleID;
		$this->roleName = $roleData->roleName;
		$this->roleTitle = $roleData->roleTitle;
		$this->defRights = $roleData->defRights;
		$this->defSectionRights = $roleData->defSectionRights;
		$this->dtSuperAccess = (1 == $roleData->dtSuperAccess);
		$this->listEdit = (1 == $roleData->listEdit);
		$this->uploadAllowed = (1 == $roleData->uploadAllowed);
	}

	public function IsAuth() {
		return false;
	}

	public function GetUserID() {
		return 0;
	}

	public function GetUserLogin() {
		return "";
	}

	public function GetUserParam($paramName) {
		return false;
	}

	public function GetRoleID() {
		return (int)$this->roleID;
	}

	public function GetRoleName() {
		return $this->roleName;
	}

	public function GetRoleTitle() {
		return $this->roleTitle;
	}

	public function GetDefSectionRights() {
		return $this->defSectionRights;
	}

	public function GetDefRights() {
		return $this->defRights;
	}

	public function IsDTSuperAccess() {
		return $this->dtSuperAccess;
	}

	public function CanEditLists() {
		return $this->listEdit;
	}

	public function CanUploadFiles() {
		return $this->uploadAllowed;
	}
}
?>