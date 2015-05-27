<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class LinkFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "link";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$dtName = $params["dtName"];
		$editMode = $params["editMode"];
		
		if (!isset($this->dtconf->dtf[$dtName][$name]["doct"])) {
			return false;
		}
		$linkedDT = $this->dtconf->dtf[$dtName][$name]["doct"];
		
		if (!isset($this->dtconf->dtf[$dtName][$name]["tdtt"])) {
			return false;
		}
		
		$titleField = $this->dtconf->dtf[$dtName][$name]["tdtt"];
		
		if (!isset($this->dtconf->dtf[$dtName][$name]["desc"])) {
			return false;
		}
		
		$linkDesc = $this->dtconf->dtf[$dtName][$name]["desc"];
		
		$field->setAttribute("description", $linkDesc);
		$field->setAttribute("docTypeName", $linkedDT);
		$field->setAttribute("targetDTTitle", $titleField);
				
		if (!isset($row["id"])) {
			return false;
		}
		
		$docID = $row["id"];

		$pref = $this->db->quote($this->conf->Param("Prefix"));
		if ($editMode) {
			$field->setAttribute("selectedDocumentID", $row[$name]);
			$sort = (isset($this->dtconf->dtf[$dtName][$name]['sort']) && $this->dtconf->dtf[$dtName][$name]['sort']) 
				? $this->dtconf->dtf[$dtName][$name]['sort']
				: '';
			
			$sqlResult = $this->db->SQL($this->getQuery($titleField, $linkedDT, $sort));
		} else {
			$auxFields = "dt.ref AS target_ref_id, sys_sections.name AS target_section_name, CONCAT('{$pref}', sys_sections.name, '/?r', dt.ref, '_id=', dt.id) AS realURL";
			$joins = "
				JOIN 
					dt_{$dtName} dtmain ON dt.id = dtmain.{$name}
				LEFT JOIN 
					sys_references ON dt.ref = sys_references.id
				LEFT JOIN 
					sys_sections ON sys_references.ref = sys_sections.id
			";
			$where = "dtmain.id = {$docID}";
			$sqlResult = $this->dt->FormatSelectQuery($linkedDT, $this->xml, $field, "*-", $auxFields, $joins, $where);
		}

		$this->dt->ProcessQueryResults($sqlResult, $this->xml, $field, $linkedDT, false, false, 0, $linkedDT, true, null, "document", false, 0, "", "", false);
		return true;
	}

	function AdditionalDeftContent($field, $name, $dtName, $showHidden) {
		$this->CreateLinkedDTList($field, $name, $dtName);
		return true;
	}
	
	/**
	 * @param object $field
	 * @param string $name
	 * @param string $dtName
	 * @return unknown
	 */
	private function CreateLinkedDTList($field, $name, $dtName) {
		if (!isset($this->dtconf->dtf[$dtName][$name]["doct"])) {
			return false;
		}
		$linkedDT = $this->dtconf->dtf[$dtName][$name]["doct"];
		
		if (!isset($this->dtconf->dtf[$dtName][$name]["tdtt"])) {
			return false;
		}
		
		$titleField = $this->dtconf->dtf[$dtName][$name]["tdtt"];
		
		if (!isset($this->dtconf->dtf[$dtName][$name]["desc"])) {
			return false;
		}
		
		$linkDesc = $this->dtconf->dtf[$dtName][$name]["desc"];
		
		$field->setAttribute("description", $linkDesc);
		$field->setAttribute("docTypeName", $linkedDT);
		$field->setAttribute("targetDTTitle", $titleField);
		
		$sort = (isset($this->dtconf->dtf[$dtName][$name]['sort']) && $this->dtconf->dtf[$dtName][$name]['sort']) 
			? $this->dtconf->dtf[$dtName][$name]['sort']
			: '';
		
		$sqlResult = $this->db->SQL($this->getQuery($titleField, $linkedDT, $sort));
		$this->dt->ProcessQueryResults($sqlResult, $this->xml, $field, $linkedDT, false, false, 0, $linkedDT, true, null, "document", false, 0, "", "", false);
	
		return true;
	}
	
	private function getQuery($titleField, $linkedDT, $sort) {
		$prefix = $this->db->quote($this->conf->Param("Prefix"));
			
		$query = "
			SELECT 
				dt.id, 
				dt.{$titleField} AS `{$titleField}`, 
				dt.ref AS target_ref_id, 
				sys_sections.name AS target_section_name, 
				CONCAT('{$prefix}', sys_sections.name, '/?r', dt.ref, '_id=', dt.id) AS realURL
			FROM 
				dt_{$linkedDT} dt 
				LEFT JOIN 
					sys_references ON dt.ref = sys_references.id
				LEFT JOIN 
					sys_sections ON sys_references.ref = sys_sections.id
			WHERE
				dt.enabled = 1
		";	
					
		if (trim($sort)) {	
			$query .= 'ORDER BY ' . $sort;	
		}
			
		return $query;
	}
	
}

?>