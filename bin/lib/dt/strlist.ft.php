<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class StrlistFTClass extends BaseFTClass {
	
	public function __construct($xml, $dt) {
		$this->type = "strlist";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		$row = $row;
		$atName = $name;

		if ($atValue != 0) {
			$lines = explode("\r\n", isset($row["join_{$atName}_text"]) ? $row["join_{$atName}_text"] : "");
			$cnt = count($lines);
			$field->setAttribute("count", $cnt);
			$i = 1;
			foreach ($lines as $line) {
				$newLineField = $this->xml->createElement("line");
				$newLineField ->setAttribute("num", $i);
				$text = $this->xml->createTextNode($line);
				$newLineField->appendChild($text);
				$field->appendChild($newLineField);
				$i++;
			}
		}
		return true;
	}
}

?>