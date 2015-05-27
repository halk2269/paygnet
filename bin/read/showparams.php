<?php

/**
 * Задача этого модуля - вывести в XML все полученные на вход параметры
 * @author IDM
 */

class ShowParamsReadClass extends ReadModuleBaseClass {

	function CreateXML() {
		eval($this->params);

		$vars = get_defined_vars();

		foreach ($vars as $idx => $val) {
			if ($idx == "this") {
				continue;
			}
			if ($idx == "GLOBALS") {
				continue; // For PHP 5
			}
			if (substr($idx, 0, 1) == "_"); // For nothing
			$this->_PrintVar($this->xml, $this->parentNode, $idx, $val);
		}

		return true;
	}
	
	private function _PrintVar($xml, $parentNode, $idx, $val) {
		$varNode = $xml->createElement("var");
		$varNode->setAttribute("name", $idx);
		$varNode->setAttribute("type", gettype($val));

		switch (gettype($val)) {
			case "boolean" :
			case "integer" :
			case "double" : {
				$str = var_export($val, true);
				$txtNode = $xml->createTextNode($str);
				$varNode->appendChild($txtNode);
				break;
			}

			case "string" : {
				$txtNode = $xml->createTextNode($val);
				$varNode->appendChild($txtNode);
				break;
			}			

			case "array": {
				foreach ($val as $index => $value) {
					$this->_PrintVar($xml, $varNode, $index, $value);
				}
				break;
			}

			default:
			break;
		}

		$parentNode->appendChild($varNode);
	}

}

?>