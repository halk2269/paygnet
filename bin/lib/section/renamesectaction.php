<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Переименование секции
 * @author fred
 */
class RenameSectAction extends AbstractSectAction {

	var $right = "Edit";
	var $info = "TitleWasChanged";
	
	var $newTitle;
	
	function _MakeChanges() {
		$this->_SetNewTitle();
		
		if (!$this->newTitle) {
			$this->error = "BlankString";
			return;
		}
		
		$this->db->SQL("UPDATE sys_sections SET title = '{$this->newTitle}' WHERE id = '{$this->sectionId}'");
	}
	
	function _SetNewTitle() {
		$this->newTitle = trim($this->query->GetParam("title"));
		$this->newTitle = mb_substr($this->newTitle, 0, 255);
	}
	
	function GetAdditionalInfo() {
		return $this->newTitle;
	}
}
?>