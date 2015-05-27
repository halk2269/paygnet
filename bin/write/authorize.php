<?php

class AuthorizeWriteClass extends WriteModuleBaseClass {
	
	protected $eshop;

	public function MakeChanges() {
		if ($this->_GetParam("logoff") !== false) {
			$this->OnBeforeLogOff();
			$this->auth->LogOff();
			$this->retPath = $this->conf->Param("Prefix");
			return true;
		}

		$login = $this->_GetParam("login");
		$pass = $this->_GetParam("pass");

		if ($login === false or $pass === false) {
			$this->inputError = true;
			return false;
		}
		
		$this->retPath = URLDeleteParam("SID", $this->retPath);
		$this->errPath = URLDeleteParam("SID", $this->errPath);

		$ret1 = $this->OnBeforeLogIn($login, $pass);

		// В случае интернет-магазина объединяем старый и новый заказы, если таковые имеются
		if ($this->conf->Param('IsEshop') && !$this->MergePurchases($login, $pass)) {
			return false;
		}
		
		$ret2 = $this->auth->LogIn($login, $pass);
		$this->OnAfterLogIn($login, $ret2);

		if (!$ret1 || !$ret2) {
			$this->_WriteError("AuthFail");
			$this->_NeedDumpVars();
			return false;
		}
		
		return true;
	}

	protected function MergePurchases($login, $pass) {
		// Если пользователь авторизован
		if ($this->auth->IsAuth()) {
			return true;
		}
		
		$login = $this->PrepareLogin($login);
		$pass = $this->PreparePass($pass); 
		
		$userID = $this->db->GetValue("SELECT id FROM dt_user WHERE login = '{$login}' AND pass = '{$pass}' AND enabled = 1");
		if (!$userID) {
			$this->_WriteError("AuthFail");
			return false;
		}

		require_once(CMSPATH_PLIB . $this->conf->Param("EshopCommonPath"));
		$commonClassName = $this->conf->Param('EshopCommonClassName');
		$this->eshop = new $commonClassName();
				
		if ($this->eshop->GetPurchaseID()) {
			// Объединять заказы можно только для определённых пользователей
			if ($this->eshop->MergeAllow($userID)) {
				return $this->eshop->Merge($userID);
			} else {
				if ("1" == $this->_GetParam("authWithOrder")) {
					$this->_WriteError("NotCustomer");
					return false;
				} else {
					return $this->eshop->DeleteAll();
				}
			}
		}
		
		return true;
	}

	protected function PrepareLogin($login) {
		return $this->db->quote(mb_substr($login, 0, 40));
	}

	protected function PreparePass($pass) {
		return $this->db->quote(
			md5(mb_substr($pass, 0, 40))
		);
	}

	protected function OnBeforeLogIn($login, $pass) {
		return true;
	}

	protected function OnBeforeLogOff() { }
	
	protected function OnAfterLogIn($login, $loginSuccess) { }

}

?>