<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class ArrayFTClass extends BaseFTClass {

	public function __construct($xml, $dt) {
		$this->type = "array";
		parent::__construct($xml, $dt);
	}

	function CreateContent($field, $row, $name, $params) {
		$atValue = $row[$name];
		$dtName = $params["dtName"];
		$editMode = $params["editMode"];
		$noSubSelectFields = $params["noSubSelectFields"];
		$canEdit = $params["canEdit"];
		$ref = $params["ref"];
		$targetSection = $params["targetSection"];
		$SID = $this->auth->GetSID();
		$editSectionName = $this->globalvars->GetStr("DocEditSectionName");

		// В editMode массивы не редактируются.
		if ($editMode) {
			return false; 
		}
		// И как мы сгенерируем ссылку, если нет id?!
		if ($canEdit and !isset($row["id"])) {
			return false; 
		}

		if (!isset($this->dtconf->dtf[$dtName][$name]["subt"])) {
			return true;
		}
		
		if ($noSubSelectFields == "*" || $noSubSelectFields && strpos(",{$name},", $noSubSelectFields) !== false) {
			if ($atValue) {
				$field->setAttribute("subDocCount", $atValue);
			} else {
				$field->setAttribute("subDocCount", "0");
			}
		} else {
			if ($canEdit) {
				$field->setAttribute("createURL", $this->conf->Param("Prefix") . "{$editSectionName}/?qref={$ref}&id={$row["id"]}&subname={$name}&subid=0&SID={$SID}");
				$field->setAttribute("createDocType", $this->dtconf->dtn[$this->dtconf->dtf[$dtName][$name]["subt"]]);
			}
			$subType = $this->dtconf->dtf[$dtName][$name]["subt"];
			if ($atValue != 0) {
				// Дополнительные данные для выборки
				$auxf = (isset($this->dtconf->dtf[$dtName][$name]["auxf"])) ? $this->dtconf->dtf[$dtName][$name]["auxf"] : null;
				$enabledCond = ($params["enabledCheck"] ? "AND (dt.enabled = 1)" : "");
				
				$arSQL = $this->dt->FormatSelectQuery(
					$subType, $this->xml, $field, "*", "", 
					"JOIN dt_{$dtName} ON dt_{$dtName}.id = dt.parent_id", 
					"dt.parent_id = {$row["id"]} AND dt.field_name = '{$name}' {$enabledCond}", 
					isset($this->dtconf->dtf[$dtName][$name]["sort"]) ? $this->dtconf->dtf[$dtName][$name]["sort"] : ""
				);
				
				$this->dt->ProcessQueryResults(
					$arSQL, $this->xml, $field, $subType, $canEdit, $canEdit,
					$ref, $targetSection, true, null, 'subdoc', $editMode,
					$row['id'], $name, $noSubSelectFields
				);
			}
		}
		
		return true;
	}

	function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		if (isset($this->dtconf->dtf[$dtName][$name]["subt"])) {
			$field->setAttribute("subType", $this->dtconf->dtf[$dtName][$name]["subt"]);
			$this->dt->GetFieldList($this->xml, $field, $this->dtconf->dtf[$dtName][$name]["subt"], $showHidden);
		}
		return true;
	}

}

?>