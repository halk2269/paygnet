<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class TableFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		parent::__construct($xml, $dt);
		$this->type = "table";
	}

	public function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		if (trim($atValue) == '') {
		    return true;
		}

		$xmlTables = new DOMDocument("1.0", "UTF-8");
		if ($xmlTables->loadXML($atValue)) {
			// получаем список всех таблиц - xml-элементов
			$tableXmlArray = $xmlTables->getElementsByTagName("table");

			foreach ($tableXmlArray as $tableInXml) {
				// каждую строку копируем в основной xml
				$importedNode = $this->xml->importNode($tableInXml, true);
				$field->appendChild($importedNode);
			}
		} else {

		}

		return true;
	}

}

?>