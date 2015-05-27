<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class MultiboxFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "multibox";
		
		parent::__construct($xml, $dt);
	}

	protected function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		
		if ($atValue == 0) {
			return;
		}	
		
		$stmt = $this->db->SQL("
			SELECT
				list.id AS list_id, list.title AS list_title, 
				items.id AS item_id, items.name AS item_name, items.title AS item_title
			FROM
				sys_multibox_select multi
			JOIN
				sys_dt_select_items items ON items.id = multi.item_id
			JOIN
				sys_dt_select_lists list ON list.id = items.list_id
			WHERE
				multi.id = {$atValue}		 		
		");
	
		if (!$stmt->rowCount()) {
			return;
		}
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$itemNode = $this->xml->createElement('item', $row['item_title']);
			$itemNode->setAttribute("list_id", $row['list_id']);
			$itemNode->setAttribute("list_title", $row['list_title']);
			$itemNode->setAttribute("item_id", $row['item_id']);
			$itemNode->setAttribute("item_name", $row['item_name']);
						
			$field->appendChild($itemNode);	
		}
	}

	protected function AdditionalDeftContent($field, $name, $dtName, $showHidden) {
		$stmt = $this->db->SQL("
			SELECT 
				id, name, title 
			FROM 
				sys_dt_select_items 
			WHERE 
				list_id = {$this->dtconf->dtf[$dtName][$name]['list']} 
				AND sort <> -1 
			ORDER BY 
				sort
		");

		while ($row = $stmt->fetchObject()) {
			$newItem = $this->xml->createElement("item", $row->title);
			$newItem->setAttribute("item_id", $row->id);
			$newItem->setAttribute("name", $row->name);
			
			$field->appendChild($newItem);
		}	
	}
	
}

?>