<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class DatetimeFTClass extends BaseFTClass {

	function __construct($xml, $dt) {
		$this->type = "datetime";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		$dtName = $params["dtName"];
	
		$d = $this->db->DateParser($atValue);
		$d = (($d < 0) ? 0 : $d);
		
		$field->setAttribute("value", ($d > 0) ? $atValue : "");
				
		if (isset($this->dtconf->dtf[$dtName][$name]["show"]) and $this->dtconf->dtf[$dtName][$name]["show"] == "selects") {
			$array = array("year" => "Y", "month" => "m", "date" => "d", "hour" => "H", "minute" => "i");
			foreach ($array as $idx => $val) {
				$field->setAttribute($idx, date($val, $d));
			}
		} elseif($d > 0) {
			$format = isset($this->dtconf->dtf[$dtName][$name]["view"]) ? $this->dtconf->dtf[$dtName][$name]["view"] : "Y-m-d H:i:s";
						
			$text = $this->xml->createTextNode(date($format, $d));
			$field->appendChild($text);
		}
		
		return true;
	}

	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		if (isset($this->dtconf->dtf[$dtName][$name]["view"])) $field->setAttribute("view", $this->dtconf->dtf[$dtName][$name]["view"]);
		if (isset($this->dtconf->dtf[$dtName][$name]["show"])) $field->setAttribute("show", $this->dtconf->dtf[$dtName][$name]["show"]);
		return true;
	}

	function AdditionalDeftContent($field, $name, $dtName, $showHidden) {
		if (isset($this->dtconf->dtf[$dtName][$name]["show"]) and $this->dtconf->dtf[$dtName][$name]["show"] == "selects") {
			$array = array("year" => "Y", "month" => "m", "date" => "d", "hour" => "H", "minute" => "i");
			
			if (isset($this->dtconf->dtf[$dtName][$name]["deft"]) and "NOW()" == strtoupper($this->dtconf->dtf[$dtName][$name]["deft"])) {
				foreach ($array as $idx => $val) {
					$node = X_CreateNode($this->xml, $field, $idx);
					X_AddText($this->xml, $node, date($val));
					$field->appendChild($node);
				}
			} else {
				foreach ($array as $idx => $val) {
					$node = X_CreateNode($this->xml, $field, $idx);
					X_AddText($this->xml, $node, date($val, mktime(0, 0, 0, 1, 1, 2000)));
					$field->appendChild($node);
				}
			}
						
			require_once(CMSPATH_LIB . "datetime/datetime.php");
			$nodeset = new DateTimeClass($this->xml, $field); 
		} else {
			$format = "Y-m-d H:i:s";
			
			if (isset($this->dtconf->dtf[$dtName][$name]["deft"]) and "NOW()" == strtoupper($this->dtconf->dtf[$dtName][$name]["deft"])) {
				$txtTemp = $this->xml->createTextNode(date($format));
			} else {
				$txtTemp = $this->xml->createTextNode(date($format, mktime(0, 0, 0, 1, 1, 2000)));
			}
			$field->appendChild($txtTemp);
		}

		return true;
		
	}
	
}
?>