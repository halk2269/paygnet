<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class PasswordFTClass extends BaseFTClass {

	function __construct($xml, $dt) {
		$this->type = "password";
		parent::__construct($xml, $dt);
	}
	
	function CreateContent($field, $row, $name, $params) {
		return true;
	}
	
	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		return true;
	}
}

?>