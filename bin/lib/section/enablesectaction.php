<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Включение секции
 * @author fred
 */
class EnableSectAction extends AbstractSectAction {

	var $right = "EditEnabled";
	var $info = "SectionWasEnabled";
	
	function _MakeChanges() {
		$this->db->SQL("UPDATE sys_sections SET enabled = 1 WHERE id = {$this->sectionId}");
	}
}
?>