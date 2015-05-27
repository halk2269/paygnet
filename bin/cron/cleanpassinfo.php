<?php

/**
 * Очищает таблицы sys_passinfo & sys_passvars от устаревших (старше 15 минут) данных.
 * Рекомендуется к выполнению раз в 15-60 минут.
 * @author IDM
 */

class CleanPassInfoCronClass extends CronBaseClass {

	function MakeChanges() {
		$this->report = "";
		
		$stmtInfo = $this->db->SQL("DELETE FROM sys_passinfo WHERE DATE_SUB(NOW(), INTERVAL 15 MINUTE) > time");
		$infoCount = $stmtInfo->rowCount();
		
		$infoMessage = "sys_passinfo was cleaned successfully";
		if ($infoCount) {
			$infoMessage .= " (deleted {$infoCount} row(s))";
		}
		
		$this->report .= $infoMessage . "\n";
		
		$stmtVars = $this->db->SQL("DELETE FROM sys_passvars WHERE DATE_SUB(NOW(), INTERVAL 15 MINUTE) > time");
		$varsCount = $stmtVars->rowCount();
		
		$varsMessage = "sys_passvars was cleaned successfully";
		if ($varsCount) {
			$this->report .= " (deleted {$varsCount} row(s))";
		}
			
		$this->report .= $varsMessage . "\n";	
	}

}

?>