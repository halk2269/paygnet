<?php

/**
 * Тест работы Крона
 * @author IDM
 */

class TestCronClass extends CronBaseClass {
	
	function MakeChanges() {
		$this->report = "Test module works!\n";
		$this->report .= "Second line of the report\n";
	}
	
}

?>