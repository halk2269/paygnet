<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class StringFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "string";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$text = $this->xml->createTextNode($row[$name]);
		$field->appendChild($text);
		return true;
	}

	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		if (isset($this->dtconf->dtf[$dtName][$name]["rphr"])) {
			$field->setAttribute("regexpDescription", $this->dtconf->dtf[$dtName][$name]["rphr"]);
		}
		
		if (isset($this->dtconf->dtf[$dtName][$name]["leng"])) {
			$field->setAttribute("length", $this->dtconf->dtf[$dtName][$name]["leng"]);
		}
		
		return true;
	}
}

?>