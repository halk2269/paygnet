<?php

/**
 * Класс восстановления пароля
 */

require_once(CMSPATH_MOD_WRITE . "docwriting.php");

class PassRestoreWriteClass extends DocWritingWriteClass {
	
	const MODE_LINK = 'Link';
	const MODE_PASS = 'Pass'; 
	
	/**
	 * Link - отправление емайла со ссылкой на получение пароля
	 * Pass - отправление письма с новым паролем
	 * @var string
	 */
	private $mode = '';

	/**
	 * @var string
	 */
	private $link = '';

	private $newPass = false;
	private $login = false;

	public function MakeChanges() {
		$path = $this->conf->Param("Prefix") 
			. $this->globalvars->GetStr('PassRestoreSection') 
			. '/';
		
		$this->retPath = $this->errPath = $path;
				
		$userLogin = $this->_GetParam("login");
		$key = $this->_GetParam("key");

		if (!$userLogin) {
			$this->_WriteError("NoData");
			return false;
		}

		$userRow = $this->db->GetRow("SELECT id, email FROM dt_user WHERE login = '{$userLogin}'");
		if (!$userRow) {
			$this->_WriteError("NoUser", $userLogin);
			return false;
		}
		
		$this->setLogin($userLogin);
		$this->setMode($key);

		if (self::MODE_LINK == $this->mode) {
			// генерация ключа
			$confirmKey = md5($userRow->id . $userRow->email . time() . rand());
			
			// внесение ключа в базу данных
			$this->db->SQL("
				INSERT INTO 
					sys_pass_restore (user_id, key, date) 
				VALUES
					('{$userRow->id}', '{$confirmKey}', NOW())
			");
			
			$this->link = 
				"http://" . $this->host . $this->conf->Param("Prefix") 
				. '?writemodule=PassRestore&ref=' . $this->ref 
				. '&login='	. $userLogin 
				. '&key=' . $confirmKey;
		} else {
			if (!preg_match("~^[a-f0-9]+$~i", $key) || strlen($key) != 32) {
				$this->_WriteError("BadKey");
				return false;
			}

			/**
			 * Если в базе есть запись, где ключ и ID пользователя совпадают, то удаляем его,
			 * иначе выводим ошибку о том, что пользователь не запрошивал восстановление пароля
			 */
			$stmtDelete = $this->db->SQL("
				DELETE FROM 
					sys_pass_restore 
				WHERE 
					user_id = '{$userRow->id}' AND key = '{$key}'
			");
			
			if (!$stmtDelete->rowCount()) {
				$this->_WriteError("NoSuchRequest");
				return false;
			}

			// генерация нового пароля
			$this->newPass = substr(
				md5($userRow->id . $userEmail . time() . rand()), 0, 7
			);

			// обновление пароля пользователя
			$stmtUpdate = $this->db->SQL("
				UPDATE 	
					dt_user 
				SET 
					pass = MD5('{$this->newPass}') 
				WHERE 
					id = '{$userRow->id}'
			");
			
			if (!$stmtUpdate->rowCount()) {
				$this->_WriteError("UpdateFails");
				return false;
			}
		}

		// отправка письма пользователю и выдача сообщения в браузер, что все хорошо
		$this->_SendNotify($this->ref, "Create", "user", $userRow->id, $userRow->email);
		
		$this->_WriteInfo($this->mode . "Sent", $userLogin);
		return true;
	}


	protected function _SendNotifyXMLModify($xml, $parentNode) {
		$restoreDataNode = X_CreateNode($xml, $parentNode, "restoreData");
		$restoreDataNode->setAttribute("mode", $this->mode);
		$restoreDataNode->setAttribute("login", $this->login);
		
		if ($this->mode == self::MODE_LINK) {
			$restoreDataNode->setAttribute("link", $this->link);
						
			$date = new DateTime('now');
			$date->modify('+14 day'); 
			
			$restoreDataNode->setAttribute("date", $date->format('d-m-Y'));
		} else {
			$restoreDataNode->setAttribute("pass", $this->newPass);
		}		
	}
	
	private function setMode($key) {
		$this->mode = (!$key) ? self::MODE_LINK : self::MODE_PASS;
	}
	
	private function setLogin($login) {
		$this->login = $login;
	}
}

?>
