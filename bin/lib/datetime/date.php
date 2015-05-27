<?php

require_once(CMSPATH_LIB . "datetime/datetimebase.php");

class DateClass extends DateTimeBaseClass  {
	
	public function __construct($xml, $parentNode) {
		parent::__construct($xml, $parentNode);
		
		$this->CreateYearsNode();
		$this->CreateMonthsNode();
		$this->CreateDatesNode();
	}

}

?>