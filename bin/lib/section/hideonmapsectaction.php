<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Скрывает на карте сайта секцию
 * @author fred
 */
class HideOnMapSectAction extends AbstractSectAction {

	var $right = "Edit";
	var $info = "SectionWasHiddenOnMap";
	
	function _MakeChanges() {
		$this->db->SQL("UPDATE sys_sections SET onmap = 0 WHERE id = {$this->sectionId}");
	}
}
?>