<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Изменение псевдонима секции
 * @author fred
 */
class ChnameSectAction extends AbstractSectAction {

	var $right = "EditName";
	var $info = "NameWasChanged";

	var $newName;

	function _MakeChanges() {
		$this->_SetNewName();
		if (!$this->newName) {
			$this->error = "BlankString";
			return;
		}

		if (!$this->_IsNewNameValid()) {
			$this->error = "BadNameChars";
			return;
		}

		if (!$this->_IsNameUnique()) {
			$this->error = "NameExists";
			return;
		}
		
		$this->db->SQL("UPDATE sys_sections SET name = '{$this->newName}' WHERE id = {$this->sectionId}");
	}

	function GetAdditionalInfo() {
		return $this->newName;
	}

	function GetErrorDesc() {
		return $this->newName;
	}

	function _SetNewName() {
		$this->newName = trim($this->query->GetParam("newname"));
		$this->newName = mb_substr($this->newName, 0, 50);
	}

	function _IsNewNameValid() {
		return !preg_match("/[^-_a-zA-Z0-9]/", $this->newName);
	}

	function _IsNameUnique() {
		return !$this->db->RowExists("SELECT id FROM sys_sections WHERE id != {$this->sectionId} AND name = '{$this->newName}'");
	}
}
?>