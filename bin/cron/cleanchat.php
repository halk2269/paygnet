<?php

/**
 * Очищает список пользователей чата.
 * Если пользователь неактивен в течение 12 минут (максимальное время апдейта на странице чата - 10 минут),
 * он удаляется.
 */

class CleanChatInfoCronClass extends CronBaseClass {

	function MakeChanges() {

		$MessageNumberWhenLogin = $this->globalvars->GetInt("ChatStartMessages");

		// chat_messages cleaning

		$sql = $this->db->SQL("SELECT COUNT(*) AS cnt FROM chat_messages");
		$row = mysql_fetch_object($sql);
		//$this->report .= "cnt = {$row->cnt}\n";
		if ($row->cnt > $MessageNumberWhenLogin) {
			$sql1 = $this->db->SQL("SELECT id FROM chat_messages ORDER BY id DESC LIMIT 30,1");
			if ($row1 = mysql_fetch_object($sql1)) {
				//$this->report .= "id = {$row1->id}\n";
				$this->db->SQL("DELETE FROM chat_messages WHERE id <= {$row1->id} and DATE_SUB(NOW(), INTERVAL 12 MINUTE) > time");
				$r = $this->db->GetNumRows();
				$this->report .= "chat_messages was cleaned. {$r} rows were deleted\n";
			} else {
				$this->report .= "Something strange while cleaning chat_messages...\n";
			}
		} else {
			$this->report .= "Nothing to clean in chat_messages ({$row->cnt} rows in table)\n";
		}

		// chat_users cleaning

		$this->report .= "Cleaning users...\n";

		
		$sql = $this->db->SQL("SELECT id, login, lastrequest, NOW() as now FROM chat_users WHERE DATE_SUB(NOW(), INTERVAL 12 MINUTE) > lastrequest or lastrequest = '0000-00-00 00:00:00'");
		while ($row = mysql_fetch_object($sql)) {
			$row->login = mysql_real_escape_string($row->login);
			$this->db->SQL("INSERT INTO chat_messages (type, login, message) VALUES ('delusr', '{$row->login}', '')");
			$this->db->SQL("DELETE FROM chat_users WHERE id = {$row->id}");
			$this->report .= "User '{$row->login}' was deleted (last request: {$row->lastrequest}, now: {$row->now})\n";
		}
	}

}

?>