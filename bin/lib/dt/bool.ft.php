<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class BoolFTClass extends BaseFTClass {
	
	public function __construct($xml, $dt) {
		$this->type = "bool";
		parent::__construct($xml, $dt);
	}
		
	function CreateContent($field, $row, $name, $params) {
		$text = $this->xml->createTextNode($row[$name]);
		$field->appendChild($text);
		return true;
	}
	
}

?>