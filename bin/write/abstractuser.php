<?php

require_once(CMSPATH_MOD_WRITE . "docwriting.php");

/**
 * Общий класс для редактирования профиля и редактирования пользователей
 */
class AbstractUserWriteClass extends DocWritingWriteClass {
	
	const DEFAULT_ROLE = "user";
	
	protected $userRef;

	protected $userId = 0;
	protected $insertedId = 0;

	protected $userRoleName;
	protected $userRoleId;
	
	protected $eshop;

	protected function _IsLoginPassedExists() {
		$loginQ = $this->db->quote($this->_GetParam("login"));
		return $this->db->RowExists("SELECT id FROM dt_user WHERE login = '{$loginQ}' AND id <> {$this->userId}");
	}

	protected function _IsEmailUnique() {
		eval($this->readParams);
		
		$inEmailIsUnique = (isset($inEmailIsUnique) && $inEmailIsUnique);
		if (!$inEmailIsUnique) {
			return true;
		}

		$emailQ = $this->db->quote($this->_GetParam("email"));
		$userIdWithMail = $this->db->GetValue("SELECT id FROM dt_user WHERE email = '{$emailQ}'");
		if (!$userIdWithMail) {
			return true;
		}

		return ($this->userId && $userIdWithMail == $this->userId);
	}

	protected function _WriteToDB() {
		$action = ($this->userId) ? "Edit" : "Create";
		
		$this->db->Begin();

		$lastID = 0;
		$rv1 = $this->_GoWriting($action, $this->userRef, $this->userId, self::DEFAULT_ROLE, true, $lastID);
		$this->insertedId = $lastID;

		$additionalData = $this->getAdditional();
		$rv2 = (isset($this->dtconf->dtn[$additionalData]))
			? $this->_GoWriting($action, $this->userRef, $this->insertedId, $additionalData, true, $lastID)
			: true;

		if ($rv1 && $rv2) {
			$this->db->Commit(); 
		} else {
			$this->db->Rollback();
		}
		
		return ($rv1 && $rv2);
	}
	
	protected function getAdditional() {
		return self::DEFAULT_ROLE . '_' . $this->userRoleName;
	}
	
	protected function setRoleData() {
		$this->userId = $this->auth->GetUserID();
		
		eval($this->readParams);
		
		if (isset($inUserRole) && isset($this->dtconf->dtn[self::DEFAULT_ROLE . '_' . $inUserRole])) {
			$roleId = $this->db->GetValue(
				"SELECT id FROM sys_roles WHERE name = ?",
				array($inUserRole)
			);
			
			if ($roleId) {
				$this->userRoleId = $roleId;
				$this->userRoleName = $inUserRole;

				return;
			}
		}

		$this->userRoleId = $this->auth->GetRoleID();
		$this->userRoleName = $this->auth->GetRoleName();
	}
	
	protected function MergePurchases($userID) {
		require_once CMSPATH_PLIB . $this->conf->Param("EshopCommonPath");
		
		$commonClassName = $this->conf->Param('EshopCommonClassName');
		$this->eshop = new $commonClassName();
		if ($this->eshop->GetPurchaseID()) {
			return ($this->eshop->MergeAllow($userID)) 
				? $this->eshop->Merge($userID) 
				: $this->eshop->DeleteAll();
		}
		
		return true;
	}

	protected function _IPBIntegration() {
		$extraParams = array();
		$extraParams["icq_number"] = $this->_GetParam("icq");
		$extraParams["msnname"]    = $this->_GetParam("msn");
		$extraParams["website"]    = $this->_GetParam("site");

		SwitchErrorHandler(); // отключаем собственный обработчик ошибок

		if (!$this->userId) {
			// создаем на форуме нового пользователя
			$memberID = $this->auth->IPB->create_account($this->_GetParam("login"), $this->_GetParam("pass"), $this->_GetParam("email"));
			if (!$memberID) {
				// ошибка при создании аккаунта пользователя на форуме
				$this->error->StopScript(get_class($this), "IPB->create_account error! [Login: " . $this->_GetParam("login") . "] " . $this->auth->IPB->sdk_error());
			}
			
			$this->db->SQL("UPDATE dt_user SET ipb_id = '{$memberID}' WHERE id = {$this->insertedId}");
		} else {
			$memberID = $this->db->GetValue("SELECT ipb_id FROM dt_user WHERE id = {$this->userId}");
			if (!$memberID) {
				$this->error->StopScript(get_class($this), "IPB->name2id! [Login: " . $this->_GetParam("login") . "]");
			
			}
			
			if (!$this->auth->IPB->update_fucking_login($this->_GetParam("login"), $memberID)) {
				$this->error->StopScript(get_class($this), "IPB->update_fucking_login error! [ID: {$memberID}] " . $this->auth->IPB->sdk_error());
			}
			
			if (!$this->auth->IPB->update_fucking_password($this->_GetParam("pass"), $memberID)) {
				$this->error->StopScript(get_class($this), "IPB->update_fucking_password error! [ID: {$memberID}] " . $this->auth->IPB->sdk_error());
			}
			
			if (!$this->auth->IPB->update_fucking_email($this->_GetParam("email"), $memberID)) {
				$this->error->StopScript(get_class($this), "IPB->update_fucking_email error! [ID: {$memberID}] " . $this->auth->IPB->sdk_error());
			}
		}

		// обновляем информацию для нового пользователя
		if (!$this->auth->IPB->update_member($extraParams, $memberID, 1)) {
			// ошибка при попытке записи дополнительной информации
			$this->error->StopScript(get_class($this), "IPB->update_member error! [ID: {$memberID}] " . $this->auth->IPB->sdk_error());
		}

		SwitchErrorHandler(); // включаем собственный обработчик ошибок
	}
}

?>