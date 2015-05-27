<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Отображение секции
 * @author fred
 */
class ShowOnMapSectAction extends AbstractSectAction {

	var $right = "Edit";
	var $info = "SectionWasShownOnMap";
	
	function _MakeChanges() {
		$this->db->SQL("UPDATE sys_sections SET onmap = 1 WHERE id = {$this->sectionId}");
	}
}
?>