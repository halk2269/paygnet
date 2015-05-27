<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Выключение секции
 * @author fred
 */
class DisableSectAction extends AbstractSectAction {

	var $right = "EditEnabled";
	var $info = "SectionWasDisabled";
	
	function _MakeChanges() {
		$this->db->SQL("UPDATE sys_sections SET enabled = 0 WHERE id = {$this->sectionId}");
	}
}
?>