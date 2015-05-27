<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class FloatFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "float";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$dtName = $params["dtName"];
		$atValue = $row[$name];
		$signDigitCount = isset($this->dtconf->dtf[$dtName][$name]["sdgt"]) ? $this->dtconf->dtf[$dtName][$name]["sdgt"] : 4;
		$floatValue = $inVal = number_format($atValue, 2, '.', '');
		$text = $this->xml->createTextNode($floatValue);
		$field->appendChild($text);
		return true;
	}

	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		if (isset($this->dtconf->dtf[$dtName][$name]["minv"])) $field->setAttribute("min", $this->dtconf->dtf[$dtName][$name]["minv"]);
		if (isset($this->dtconf->dtf[$dtName][$name]["maxv"])) $field->setAttribute("max", $this->dtconf->dtf[$dtName][$name]["maxv"]);
		if (isset($this->dtconf->dtf[$dtName][$name]["sdgt"])) $field->setAttribute("sdgt", $this->dtconf->dtf[$dtName][$name]["sdgt"]);
		return true;
	}
}
?>