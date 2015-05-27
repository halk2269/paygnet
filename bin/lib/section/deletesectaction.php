<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");
require_once(CMSPATH_BIN . "readbase.php");

/**
 * Удаление секции
 * @author fred
 */
class DeleteSectAction extends AbstractSectAction {

	var $right = "Delete";
	var $info = "SectionWasDeleted";

	function _MakeChanges() {
		$this->_RecursiveDelete($this->sectionId);
	}

	/**
	 * Удаление секции и вложенных подсекций
	 *
	 * @param int $id
	 */
	private function _RecursiveDelete($id) {
		$stmt = $this->db->SQL("SELECT id FROM sys_sections WHERE parent_id = {$id}");
		while ($module = $stmt->fetchObject()) {
			$this->_RecursiveDelete($module->id);
		}
		
		$this->_DeleteSection($id);
	}

	/**
	 * Удаление секции, прав на секцию, модулей и документов в этой секции
	 *
	 * @param int $sectionId
	 */
	private function _DeleteSection($sectionId) {
		$stmt = $this->db->SQL("SELECT id, class, filename, params FROM sys_references WHERE ref = '{$sectionId}'");
		while ($row = $stmt->fetchObject()) {
			if (!$row->filename) {
				break;
			}

			require_once(GenPath($row->filename, CMSPATH_MOD_READ, CMSPATH_PMOD_READ));
			call_user_func_array(
				array($row->class, "Clean"), 
				array($row->id, $row->params)
			);
		}

		// права на модули вычищаются с помощью внешнего ключа
		$this->db->SQL("DELETE FROM sys_references WHERE ref = {$sectionId};");
		$this->db->SQL("DELETE FROM sys_section_meta WHERE ref = {$sectionId};");
		$this->db->SQL("DELETE FROM sys_section_rights WHERE section_id = {$sectionId};");
		$this->db->SQL("DELETE FROM sys_sections WHERE id = {$sectionId};");
	}
}
?>