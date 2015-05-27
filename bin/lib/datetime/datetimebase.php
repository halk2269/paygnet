<?php

class DateTimeBaseClass extends BaseClass {
	
	private $xml;
	private $parentNode;
	
	private $years;
	
	public function __construct($xml, $parentNode) {
		$this->xml = $xml;
		$this->parentNode = $parentNode;
				
		$this->years["first"] = 1970;
		$this->years["last"] = 2037;
		
		parent::__construct();
	}
	
	function CreateYearsNode() {
		$this->_CreateNodeSet($this->years["first"], $this->years["last"], "years", $type = "year");
	}
	
	function CreateMonthsNode() {
		$monthsNode = X_CreateNode($this->xml, $this->parentNode, "months");
		
		$months = array(1 => "Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря");
		$this->_CreateItemNode($this->xml, $monthsNode, $months, $type = "month");
		
		$this->parentNode->appendChild($monthsNode);
		
		return;
	}
	
	function CreateDatesNode() {
		$type = "date";
		$this->_CreateNodeSet(1, 31, "dates", $type = "date");
	}
	
	function CreateHoursNode() {
		$type = "hour";
		$this->_CreateNodeSet(0, 23, "hours", $type = "hour");
	}
	
	function CreateMinutesNode() {
		$type = "minute";
		$this->_CreateNodeSet(0, 59, "minutes", $type = "minute");
	}
	
	function _CreateNodeSet($first, $last, $nodeName, $type) {
		$node = X_CreateNode($this->xml, $this->parentNode, $nodeName);
		
		$array = array();
		$this->_FillArray($first, $last, $array);
		$this->_CreateItemNode($this->xml, $node, $array, $type);
		
		$this->parentNode->appendChild($node);
		
		return;
	}
	
	function _FillArray($first, $last, &$array) {
		for ($index = $first; $index <= $last; $index++) {
			$array[$index] = $index;
		}
		
		return $array;
	}
	
	function _CreateItemNode(&$xml, &$parentNode, $array, $type) {
		foreach ($array as $idx => $val) {
			$itemNode = X_CreateNode($xml, $parentNode, "item");
			
			$index = ($type == "year") ? $idx : substr("0" . $idx, -2, 2);
			$itemNode->setAttribute("id", $index);
			$value = ($type == "year" or $type == "month") ? $val : substr("0" . $val, -2, 2);
			X_AddText($xml, $itemNode, $value);
												
			$parentNode->appendChild($itemNode);
		}
		
		return;
	}
	
}

?>