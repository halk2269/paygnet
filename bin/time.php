<?php

/**
 * Класс, отвечающий за измерение времени исполнения скрипта 
 */

class TimeClass {

	private $startTime;

	public function __construct() {
		$this->startTime = GetMicrotime();
	}

	public function ScriptTime() {
		return (string)(GetMicrotime() - $this->startTime);
	}

	private function ScriptTimeString() {
		return "Время работы скрипта: " . $this->ScriptTime();
	}

	public function ScriptTimeComment() {
		$t = $this->ScriptTimeString();
		return "<!-- $t -->";
	}

	public function ScriptTimeJSComment() {
		$t = $this->ScriptTimeString();
		return "/* $t */";
	}

	public function ScriptTimePre() {
		$t = $this->ScriptTimeString();
		return "<pre>\n$t</pre>";
	}

	public function GetSub($startTime) {
		return (string)(GetMicrotime() - $startTime);
	}
}

?>