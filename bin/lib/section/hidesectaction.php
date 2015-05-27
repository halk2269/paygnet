<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Отображение секции
 * @author fred
 */
class HideSectAction extends AbstractSectAction {

	var $right = "Edit";
	var $info = "SectionWasHidden";
	
	function _MakeChanges() {
		$this->db->SQL("UPDATE sys_sections SET hidden = 1 WHERE id = {$this->sectionId}");
	}
}
?>