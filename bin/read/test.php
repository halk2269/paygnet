<?php

/** 
 * Тестовый модуль чтения
 */

class TestReadClass extends ReadModuleBaseClass {

	function CreateXML() {
		
		$xml = $this->xml;
		$parentNode = $this->parentNode;
		$xslList = &$this->params;
		$rights = &$this->rights;
		$adminMode = $this->adminMode;
		
		eval($this->params);

		$test = $xml->createElement("Test");
		$txt = (isset($qwe)) ? $xml->createTextNode($qwe) : $xml->createTextNode("inner text");
		$test->appendChild($txt);
		$parentNode->appendChild($test);

		$bbb = $xml->createElement("test");
		$txt = $xml->createTextNode("<b>123</b>");
		$bbb->appendChild($txt);
		$parentNode->appendChild($bbb);
		
		return true;
	}

}

?>