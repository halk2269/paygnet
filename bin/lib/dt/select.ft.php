<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class SelectFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "select";
		
		parent::__construct($xml, $dt);
	}

	protected function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		$atName = $name;

		if ($atValue != 0) {
			$field->setAttribute("list_id", isset($row["join_{$atName}_list_id"]) ? $row["join_{$atName}_list_id"] : "");
			$field->setAttribute("list_title", isset($row["join_{$atName}_list_title"]) ? $row["join_{$atName}_list_title"] : "");
			$field->setAttribute("item_id", isset($row["join_{$atName}_item_id"]) ? $row["join_{$atName}_item_id"] : "");
			$field->setAttribute("item_name", isset($row["join_{$atName}_item_name"]) ? $row["join_{$atName}_item_name"] : "");
			
			$text = $this->xml->createTextNode(
				isset($row["join_{$atName}_item_title"]) ? $row["join_{$atName}_item_title"] : ""
			);
			
			$field->appendChild($text);
			return true;
		}
	}

	protected function AdditionalDeftContent($field, $name, $dtName, $showHidden) {
		$stmt = $this->db->SQL("
			SELECT 
				id, name, title 
			FROM 
				sys_dt_select_items 
			WHERE 
				list_id = {$this->dtconf->dtf[$dtName][$name]['list']} AND sort <> -1 
			ORDER BY sort
		");

		while ($row = $stmt->fetchObject()) {
			$newItem = $this->xml->createElement("item", $row->title);
			$newItem->setAttribute("id", $row->id);
			$newItem->setAttribute("name", $row->name);
						
			$field->appendChild($newItem);
		}
		return true;
	}

	protected function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		/**
		 * @todo Перенести создание атрибутов
		 * - list_id
		 * - list_title
		 * из CreateContent()
		 */
		return true;
	}
}

?>