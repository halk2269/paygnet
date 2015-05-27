<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class IntFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "int";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$text = $this->xml->createTextNode($row[$name]);
		$field->appendChild($text);
		return true;
	}

	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		if (isset($this->dtconf->dtf[$dtName][$name]["minv"])) $field->setAttribute("min", $this->dtconf->dtf[$dtName][$name]["minv"]);
		if (isset($this->dtconf->dtf[$dtName][$name]["maxv"])) $field->setAttribute("max", $this->dtconf->dtf[$dtName][$name]["maxv"]);
		return true;
	}
}

?>