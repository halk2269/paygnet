<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Изменение перехода к первой дочерней
 * включено-выключено - передается через параметр status
 * status может принимать значения - 0 или 1
 *
 * @author busta
 */
class GoToChildSectAction extends AbstractSectAction {

	var $right = "Edit";
	var $info = "GoToChildWasChanged";
	
	function _MakeChanges() {
		// получаем и проверяем параметр newname
		$status = $this->query->GetParam("status");
		if (false === $status) {
			$this->error = "NeedParam";
			$this->GetErrorDesc();
			return;
		}
		
		$status = (int)$status;
		if ($status != 0 and $status != 1) {
			$this->error = "BadGoToChildField";
			return;
		}
		
		$this->db->SQL("UPDATE sys_sections SET go_to_child = '{$status}' WHERE id = '{$this->sectionId}'");
	}
	
	function GetErrorDesc() {
		return "status";
	}
}
?>