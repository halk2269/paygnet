<?php

/**
 * Модуль очистки поиска
 * @author IDM
 */

class SearchCleanCronClass extends CronBaseClass {

	public function MakeChanges() {
		$stmt = $this->db->SQL("DELETE FROM sys_search_cache WHERE DATE_SUB(NOW(), INTERVAL 3 HOUR) > addtime");
		$number = $stmt->rowCount();
		
		$this->report .= "sys_search_cache was cleaned. {$number} row(s) were deleted\n";
	}

}

?>