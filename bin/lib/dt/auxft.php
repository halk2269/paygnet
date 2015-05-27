<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class AuxFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "string";
		parent::__construct($xml, $dt);
	}
	
	function CreateField($name, $dtName) {
		$field = $this->xml->createElement("aux");
		$field->setAttribute("name", $name);
		return $field;
	}
		
	function CreateContent($field, $row, $name, $params) {
		$text = $this->xml->createTextNode($row[$name]);
		$field->appendChild($text);
		return true;
	}
	
}
?>