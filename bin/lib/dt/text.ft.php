<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class TextFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "text";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		$editMode = $params["editMode"];
		$dtName = $params["dtName"];

		if (isset($this->dtconf->dtf[$dtName][$name]["mode"]) and $this->dtconf->dtf[$dtName][$name]["mode"] == 'nl2br' and $editMode) {
			$atValue = preg_replace("/<[^>]*>/", "", $atValue);
			$atValue = unhtmlspecialchars($atValue);
		}
		
		if ((!isset($this->dtconf->dtf[$dtName][$name]["mode"]) or $this->dtconf->dtf[$dtName][$name]["mode"] == 'wyswyg') and $editMode) {
			$prefix = $this->conf->Param("Prefix");
			$atValue = preg_replace("/(\"|\')wyswyg/i", "\\1{$prefix}wyswyg", $atValue);
		}
		
		if ((!isset($this->dtconf->dtf[$dtName][$name]["mode"]) or $this->dtconf->dtf[$dtName][$name]["mode"] == 'wyswyg') and !$editMode) {
			$prefix = $this->conf->Param("Prefix");
			$atValue = preg_replace("/(window\.open\(')wyswyg/i", "\\1{$prefix}wyswyg", $atValue);
		}

		$text = $this->xml->createTextNode($atValue);
		$field->appendChild($text);
		return true;
	}

	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		$mode = (isset($this->dtconf->dtf[$dtName][$name]["mode"])) ? $this->dtconf->dtf[$dtName][$name]["mode"] : "wyswyg";
		$field->setAttribute("mode", $mode);
		if (isset($this->dtconf->dtf[$dtName][$name]["leng"])) {
			$field->setAttribute("length", $this->dtconf->dtf[$dtName][$name]["leng"]);
		}
		
		if (isset($this->dtconf->dtf[$dtName][$name]["nosp"]) && $this->dtconf->dtf[$dtName][$name]["nosp"]) {
			$field->setAttribute("noSpacesLimit", "1");
		}
		
		return true;
	}
}

?>