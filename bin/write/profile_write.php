<?php

require_once(CMSPATH_MOD_WRITE . "abstractuser.php");

class ProfileWriteClass extends AbstractUserWriteClass {
	
	public function MakeChanges() {
		$isAuth = $this->auth->IsAuth();
		
		$this->setRoleData();

		if (!$this->ref) {
			return false;
		}
		
		if (!$this->_IsProfileAllowed()) {
			return false;
		}
		
		if ($this->_IsChangeNeeded()) {
			$this->_SetParams();
		}
				
		if (!$this->auth->GetUserID() && !$this->_IsRegisterAllowed()) {
			return false;
		}
		
		if ($this->auth->GetUserID() and !$this->_IsLoginChangeAllowed()) {
			$this->_SetParam("login", $this->auth->GetUserLogin());
		}

		if ($this->_IsLoginPassedExists()) {
			$this->_WriteError("UserExists", $this->_GetParam("login"));
		}

		if (!$this->_IsEmailUnique()) {
			$this->_WriteError("DuplicateEmail", $this->_GetParam("email"));
		}

		$this->_SetUserRef();
		
		$result = $this->_WriteToDB();
		if (!$result) {
			return false;
		}
				
		$act = ($this->userId) ? "Edit" : "Create";
		$userIdChange = ($this->userId) ? $this->userId : $this->insertedId;
		
		$this->_SendNotify($this->userRef, $act, "user", $userIdChange);
		
		if ('Create' == $act && $this->conf->Param('IsEshop') && $result) {
			$this->MergePurchases($userIdChange);
		}
		
		if ($this->conf->Param("IPBIntegration")) {
			$this->_IPBIntegration();
		}

		if (!$this->auth->GetUserID()) {
			$this->auth->Login(
				$this->_GetParam("login"), 
				$this->_GetParam("pass")
			);
		}
		
		if ($this->conf->Param("IPBIntegration") && !$isAuth) {
			$this->_IPBLogin();
		}

		return true;
	}

	private function _IsProfileAllowed() {
		eval($this->readParams);
		
		return (isset($inAllowProfile) && $inAllowProfile);
	}

	private function _SetUserRef() {
		eval($this->readParams);
		
		$this->userRef = (isset($inUsersRef) && "this" == $inUsersRef) ? $this->ref : $inUsersRef;
	}

	private function _IsRegisterAllowed() {
		eval($this->readParams);
		
		return (isset($inAllowRegister) && $inAllowRegister);
	}

	private function _IsLoginChangeAllowed() {
		eval($this->readParams);
		
		return (isset($inAllowLoginChange) && $inAllowLoginChange);
	}

	private function _IPBLogin() {
		SwitchErrorHandler();
		
		if (!$this->auth->IPB->login($this->_GetParam("login"), $this->_GetParam("pass"))) {
			// ошибке при попытке входа на форум
			$this->error->StopScript(get_class($this), "IPB->login error! " . $this->auth->IPB->sdk_error());
		}
		
		SwitchErrorHandler();
	}
	
	protected function _SetParams() {
		$this->_SetParam("role_id", $this->userRoleId);
		$this->_SetParam("enabled", "1");
	}
	
	protected function _IsChangeNeeded() {
		return true;
	}
				
}

?>