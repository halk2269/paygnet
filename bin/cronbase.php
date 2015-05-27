<?php

/**
 * Базовый класс для модуля Крона
 */
abstract class CronBaseClass extends BaseClass {
	
	const CRON_CLASS_POSTFIX = 'CronClass';
	
	protected $report = '';
	
	public function __construct() {
		parent::__construct();
	}
	
	abstract public function MakeChanges();
	
	public function GetReport() {
		return $this->report;
	}
		
}

?>