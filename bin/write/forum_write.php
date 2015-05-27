<?php

/**
 * Запись документов с определённым типом
 */

require_once(CMSPATH_MOD_WRITE . "docwriting.php");

class ForumWriteClass extends DocWritingWriteClass {

	function MakeChanges() {
		// ID - пришло и является нормальным числом
		$id = $this->_GetParam("id");
		if ($id === false) return false;
		if (!IsGoodNum($id)) return false;

		// QRef - пришло, и является нормальным числом
		$qref = $this->_GetParam("qref");
		if ($qref === false) return false;
		if (!IsGoodNum($qref)) return false;

		$stmt = $this->db->SQL("SELECT params FROM sys_references WHERE id = {$qref}");
				
		if (!$stmt->rowCount()) {
			return false;
		} else {
			eval($row->params);
			
			if (
				(!isset($allowPostForUnreg) || !$allowPostForUnreg) 
				&& !$this->auth->GetUserID()
			) {
				return false;
			}
		}
		
		$row = $stmt->fetchObject();

		// Если тема закрыта, даем сообщение об ошибке
		$closed = $this->db->GetValue("SELECT `closed` FROM `dt_{$inDTName}` WHERE `id` = {$id}");
		if ($closed == "1") {
			$this->_WriteError("TopicClosed","");
			return false;
		}

		// Сообщение всегда активно
		$this->_SetParam("enabled", 1);
		$this->_SetParam("message", $this->PrepareUserMessages($this->_GetParam("message")));
		
		// Добавляем информацию о пользователе
		if (!$this->auth->getUserID()) {
			$this->_SetParam("user_id", "0");
		} else {
			$this->_SetParam("user_name", $this->auth->getUserLogin());
			$this->_SetParam("user_id", $this->auth->getUserID());
		}

		// Добавление темы в новости - есть ли права?
		$roleName = $this->auth->GetRoleName();
		if ($roleName != "admin" && $roleName != "superadmin") {
			$this->_SetParam("isnews", 0);
		}
		
		return $this->_WriteAll("Create", $qref, $id, $inDTName, true, "", $lastID);
	}
	
	/* Триггер, срабатывающий при удачной записи элемента массива. Возвращаемое значение не проверяется */
	public function OnSuccessfulSubWrite(
		$qref, $dtName, $id, $subAct, $subDTName, $subID, $rv, $lastID
	) {
		$lastAuthor = $this->auth->getUserLogin() 
			? $this->auth->getUserLogin() 
			: "Гость";
		
		$this->db->SQL(
			"UPDATE 
				dt_{$dtName} 
			SET 
				last_author = ?, last_message_time = NOW() 
			WHERE 
				id = ?",
			array($lastAuthor, $id)
		);
	}
	
	/* Триггер, срабатывающий при удачной записи документа. Возвращаемое значение не проверяется */
	public function OnSuccessfulWrite(
		$qref, $act, $dtName, $id, $canCreateEnabled, $rv, $lastID
	) {
		$lastAuthor = $this->auth->getUserLogin() 
			? $this->auth->getUserLogin() 
			: "Гость";
		
		$this->db->SQL(
			"UPDATE 
				dt_{$dtName} 
			SET 
				last_author = ?, 
				last_message_time = NOW() 
			WHERE 
				id = ?",
			array($lastAuthor, $lastID)
		);
	}
		
	function PrepareUserMessages($message) {
		$message = htmlspecialchars($message);
		$message = preg_replace("/(^|\s)(http:\/\/[\/=~?.&;a-zA-Z0-9]+)(\s|\$)/i", "<a href='$2'>$2</a>$3",$message);
		$message = preg_replace("/(^|\s)(?<!http:\/\/)(www\.[\/=~?.&a-zA-Z0-9]+)(\s|\$)/i", "<a href='http://$2'>$2</a>$3",$message);
		$message = nl2br($message);
		return $message;
	}

}

?>