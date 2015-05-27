<?php

/**
 * Модуль очистки пользователей рассылки
 * @author IDM
 */

class SubscribeUsersCleanCronClass extends CronBaseClass {

	function MakeChanges() {
		$this->db->SQL("DELETE FROM dt_subscribeusers WHERE DATE_SUB(NOW(), INTERVAL 14 DAY) > notifdate and status = 3");
		$r = $this->db->GetNumRows();
		$this->report .= "Users who not confirm subscribe in 2 week were cleaned. {$r} rows were deleted\n";
		$this->db->SQL("UPDATE dt_subscribeusers SET status = 1 WHERE DATE_SUB(NOW(), INTERVAL 14 DAY) > notifdate and status = 2");
		$r = $this->db->GetNumRows();
		$this->report .= "Users who not confirm unsubscribe in 2 week were cleaned. {$r} rows were deleted\n";
	}
	
}

?>