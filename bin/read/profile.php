<?php

class ProfileReadClass extends ReadModuleBaseClass {
	
	const DEFAULT_ROLE = "user";

	public function CreateXML() {
		if ($this->_DoCheck()) {
			return true;
		}
				
		$dts = array();
		$dts[] = self::DEFAULT_ROLE;

		$additionalData = $this->_GetAdditionalData();
		if (isset($this->dtconf->dtn[$additionalData])) {
			$dts[] = $additionalData;
		}

		$this->_WriteInfo("AllowLoginChange", $this->IsLoginChangeAllowed() ? "1" : "0");
		foreach ($dts as $dtName) {
			$this->dt->GetFieldList($this->xml, $this->parentNode, $dtName);
			
			/**
			 * если пользователь авторизован, то создаем для него ноду с его данными
			 * если пользователь авторизован, то не надо ещё раз выгребать его информацию,
			 * если она содержится в ноде Visitor
			 */ 
			if ($this->auth->GetUserID() && !$this->CreateUserDataXML($dtName)) {
				return false;
			}
		}
		
		return true;
	}
	
	protected function _GetAdditionalData() {
		eval($this->params);

		if (isset($inUserRole) && isset($this->dtconf->dtn[self::DEFAULT_ROLE . '_' . $inUserRole])) {
			return self::DEFAULT_ROLE . '_' . $inUserRole;
		}
						
		return self::DEFAULT_ROLE . '_' . $this->auth->GetRoleName();
	}
	
	protected function _DoCheck() {
		return false;
	}

	private function IsLoginChangeAllowed() {
		eval($this->params);
		
		return (isset($inAllowLoginChange) && $inAllowLoginChange);
	}

	private function CreateUserDataXML($dtName) {
		$stmt = $this->dt->FormatSelectQuery(
			$dtName, $this->xml, $this->parentNode, 
			"*", "", "", "dt.id = " . $this->auth->GetUserID()
		);
		
		if (!$stmt || !$stmt->rowCount()) {
			return false;
		}
		
		$this->dt->Select2XML_V2($stmt, $this->xml, $this->parentNode, $dtName);
		return true;
	}
			
}
?>