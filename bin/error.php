<?php

require_once(CMSPATH_BIN . 'response.php');

/**
 * Класс, отвечающий за обработку ошибок - критических 
 * (приводящих к останову работы скрипта)
 */
class ErrorClass {
	
	private $conf;
	private $logger;
	
	private static $instance; 

	private function __construct() {
		$this->conf = GlobalConfClass::GetInstance();
		$this->logger = LogClass::GetInstance();
	}
	
	public static function GetInstance() {
		if (!self::$instance instanceof ErrorClass) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	public function GlobalError() {
		global $phpError;
		
		return $phpError;
	}

	/**
	 * @param string $level
	 */
	public function SetReportingLevel($level) {
		error_reporting($level);
	}

	/**
	 * Останавливает работу скрипта, пишет ошибку в лог.
	 * Если разрешено, пишет ошибку в выходной поток.
	 */
	public function StopScript($className, $errorDescr) {
		$this->logger->writeError($className, $errorDescr);
		
		ResponseClass::SetHeaders("html");
		
		if ($this->conf->Param("ShowError")) {
			trigger_error("Error in class {$className}: " . nl2br($errorDescr));
		} else {
			echo $this->conf->Param("CriticalErrorMsg");
		}
		
		exit();
	}

}

?>