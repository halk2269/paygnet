<?php

/**
 * Модуль очистки голосований конкурса
 */

class VotesCleanCronClass extends CronBaseClass {

	public function MakeChanges() {
		$stmt = $this->db->SQL("DELETE FROM inquirer_votes WHERE DATE_SUB(NOW(), INTERVAL 1 DAY) > time");
		$r = $stmt->rowCount();
		$this->report .= "inquirer_votes was cleaned. {$r} rows were deleted\n";
	}

}

?>