<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Отображение секции
 * @author fred
 */
class ShowSectAction extends AbstractSectAction {

	var $right = "Edit";
	var $info = "SectionWasShown";
	
	function _MakeChanges() {
		$this->db->SQL("UPDATE sys_sections SET hidden = 0 WHERE id = {$this->sectionId}");
	}
}
?>