<?php

/**
 *
 * @author IDM
 **/

require_once(CMSPATH_LIB . "datetime/datetimebase.php");

class DateTimeClass extends DateTimeBaseClass  {
	
	public function __construct($xml, $parentNode) {
		parent::__construct($xml, $parentNode);
		
		$this->CreateYearsNode();
		$this->CreateMonthsNode();
		$this->CreateDatesNode();
		
		$this->CreateHoursNode();
		$this->CreateMinutesNode();
	}

}

?>