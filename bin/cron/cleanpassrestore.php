<?php

/**
 * Удаляет старые запросы восстановления пароля
 *
 */
class CleanPassRestoreCronClass extends CronBaseClass {

	function MakeChanges() {
 		$this->report = "Starting cleaning pass restore table...\n";
 		
		$stmt = $this->db->SQL(
			"DELETE FROM sys_pass_restore WHERE DATE_SUB(CURDATE(), INTERVAL 15 DAY) > `date`
			");
 		$this->report .= $stmt->rowCount() . " entries wasdeleted\n";
	}
}

?>