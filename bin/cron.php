<?php

require_once(CMSPATH_BIN . "auxil.php");       // Набор хороших и полезных функций
require_once(CMSPATH_BIN . "base.php");        // Базовый класс
require_once(CMSPATH_BIN . "time.php");        // Время работы скрипта
require_once(CMSPATH_BIN . "cronbase.php");    // Базовый класс для модуля Крона

/**
 * Главный класс периодического запуска, запускаемый из cronstart.php
 */
class CronClass extends BaseClass {
	
	static private $instance;

	public function __construct() {
		parent::__construct();
	}
	
	public static function GetInstance() {
		if (!self::$instance instanceof CronClass) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	public function Run() {
		date_default_timezone_set('Europe/Moscow');
		setlocale(LC_ALL, 'ru_RU.UTF8');
		
		mb_internal_encoding("UTF-8");
		mb_regex_encoding("UTF-8");
		
		ignore_user_abort(true);
		
		ini_set('error_log', CMSPATH_LOG . "error.log");

		// Засекаем время начала работы скрипта (условно, от начала работы скрипта уже пройдёт некоторое время, пренебрежимо малое)
		$time = new TimeClass();

		// Если используем PEAR, то инициализируем путь к этой библиотеке
		if ($this->conf->Param("UsePEAR")) {
			ini_set("include_path", CMSPATH_LIB . "pear/" . PATH_SEPARATOR . ini_get("include_path"));
		}
		
		$stmt = $this->db->SQL("
			SELECT 
				id, class, filename 
			FROM 
				sys_cronmodules 
			WHERE 
				enabled = 1 
				AND DATE_SUB(NOW(), INTERVAL period DAY_MINUTE) > lastdate
		");
		
		while ($row = $stmt->fetchObject()) {
			echo "Including {$row->filename}...\n";
			
			require_once GenPath($row->filename, CMSPATH_MOD_CRON, CMSPATH_PMOD_CRON);
			echo "Starting class {$row->class}...\n";
			
			$startTime = GetMicrotime();
			$className = $row->class;
			
			echo "Running class {$row->class}...\n";
			$module = new $className();
			$module->MakeChanges();
			
			$this->db->SQL("UPDATE sys_cronmodules SET lastdate = '" . $this->db->GetTime() . "' WHERE id = '{$row->id}'");
			$execTime = $time->GetSub($startTime);
			echo "Writing report: \n";
			
			$report = $module->GetReport();
			echo "{$report}\n";
			
			$this->log->writeReport(CMSPATH_LOG_CRON, $row->class, $report, $execTime);
			echo "\n==========================\n\n";
		}

	}

	public function Clean() {
		$this->db->Close();
	}

}

?>