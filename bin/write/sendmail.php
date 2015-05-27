<?php

/**
 * Запись документов с определённым типом
 */

define('SUBSCRIBED_OK', 1);
define('WAIT_FOR_SUBSCRIBE', 2);
define('WAIT_FOR_DELETE', 3);

class SendMailWriteClass extends WriteModuleBaseClass {

	public function MakeChanges() {
		$ref = $this->_GetParam("ref");
		// На вход не пришёл параметр ref? В сад.
		if (!$ref) {
			return false;
		}
		
		// ref - не число? В сад.
		if (!IsGoodNum($ref)) {
			return false;
		}
		
		$id = $this->_GetParam("id");
		// На вход не пришёл параметр id? В сад.
		if (!$id) {
			return false;
		}
		
		// id - не число? В сад.
		if (!IsGoodNum($id)) {
			return false;
		}
		
		$subact = $this->_GetParam("subact");
		// На вход не пришёл параметр subact? В сад.
		if ($subact !== "now") {
			$subact = "delay";
		}

		// Нет прав? В сад.
		$rights = $this->auth->GetRefRights($ref, $c);
		if (!(
			$rights["Read"] 
			&& $rights["Create"] 
			&& $rights["CreateEnabled"]
			&& $rights["Edit"] 
			&& $rights["Delete"]
		)) {
			return false;
		}
		
		$stmt = $this->db->SQL("SELECT params FROM sys_references WHERE id = {$ref}");
		if ($stmt->rowCount() < 1) {
			return false;
		}
		
		$row = $stmt->fetchObject();
		$params = $row->params;
		eval($params);
		
		if (!isset($inDTName) || !isset($inAllowWriteDocClass)) {
			return false;
		}
		
		$stmt = $this->db->SQL(
			"SELECT id, subject, body FROM dt_{$inDTName} WHERE id = {$id} AND ref = {$ref}"
		);
		
		if (!$stmt->rowCount()) {
			return false;
		}

		// получаем параметры текущей рассылки
		$row = $stmt->fetchObject();
		$mailSubject = $row->subject;
		$mailBody = $row->body;

		$stmtUpdate = $this->db->SQL(
			"UPDATE dt_{$inDTName} SET senddate = NOW() WHERE id = '{$row->id}'"
		);
		if (!$stmtUpdate->rowCount()) {
			return false; 
		}
				
		// Нет такого ТД в объявлениях? В сад.
		if (!isset($this->dtconf->dtf[$inDTName])) {
			return false;
		}

		/* все проверки завершились успешно */
		$usersTableName = "dt_subscribeusers";
		$cronTableName = "sys_dt_subscribe";

		// если не прописаны retpath и errpath, то жестко устанавливаем
		if (!$this->_GetParam("errpath")) {
			$this->errPath = "http://" . $_SERVER['HTTP_HOST'] . $this->conf->Prefix . "subscribe";
		}
		
		if (!$this->_GetParam("retpath")) {
			$this->retPath = "http://" . $_SERVER['HTTP_HOST'] . $this->conf->Prefix . "subscribe";
		}

		// Добавляем условия на выборку подписчиков
		$usersWhere = $this->getUsersWhere();

		// селектируем всех подписчиков рассылки
		$stmtSubscr = $this->db->SQL("
			SELECT 
				id, email 
			FROM 
				{$usersTableName} 
			WHERE 
				status = " . SUBSCRIBED_OK . " 
				AND enabled = 1 {$usersWhere}
		");
		if (!$stmtSubscr->rowCount()) {
			return false;
		}
		
		while ($user = $stmtSubscr->fetchObject()) {
			$stmtInsert = $this->db->SQL("INSERT INTO {$cronTableName} (userid, subid) VALUES ({$user->id}, {$id})");
			
			if ($this->db->GetLastID() < 1) {
				$this->error->StopScript(
					"SendMailWriteClass", 
					"Can't create a new DB entry of DB error"
				);
			}
		}

		$this->_WriteInfo("SubscribeSendQueryed");
		return true;
	}
	
	protected function getUsersWhere() {
		return "";
	}
}

?>