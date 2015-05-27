<?php

/**
 * Логирование различных событий: 
 * ошибок, доступа, отработки заданий.
 */

class LogClass {

	private $conf;
	
	static private $instance;
	
	public function __construct() {
		$this->conf = GlobalConfClass::GetInstance();
	}
	
	public static function GetInstance() {
		if (!self::$instance instanceof LogClass) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	/**
	 * @return void
	 */ 
	public function writeError($className, $errorDescr) {
		$errorDescr = substr($errorDescr, 0, 1024);
		error_log("Error: " . $this->getTime() . " " . $className . ": " . $errorDescr);
	}

	/**
	 * @return void
	 */
	public function writeWarning($className, $warning) {
		error_log("Warning: " . $this->getTime() . " " . $className . ": " . $warning);
	}

	/**
	 * @return void
	 */
	public function writeReport($logfolder, $logname, $message, $execTime) {
		if (!file_exists($logfolder . $logname)) {
			$res = mkdir($logfolder . $logname, 0755, true);
			if (!$res) {
				throw new Exception(
					"Can't create folder " . ($logfolder . $logname) . " for writing log"
				);
			}
		}

		$filename = $logfolder . $logname . "/" . date("Y-m-d_H-i-s") . ".log";
		$source = fopen($filename, "a+");
		if (!$source) {
			throw new Exception(
				"Can't open file {$filename} for data appending"
			);
		}
		
		fwrite(
			$source,
			$message
				. "\n========== Report End ==========\n"
				. "Execution time: {$execTime}\n\n"	
		);
		
		fclose($source);
	}

	private function getTime() {
		return date("Y-m-d, D H:i:s");
	}

}

?>