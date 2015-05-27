<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Перенос секции
 *
 * @author busta
 */
class MoveSectAction extends AbstractSectAction {

	var $right = "Create";
	var $info = "SectionMoved";
	
	var $auth;
	
	var $errordesc;
	
	var $lastSort = 0;
	
	var $addinfo;

	private $_from;
	
	function _MakeChanges() {
		$this->auth = AuthClass::GetInstance();
		$this->_from = $this->query->GetParam("from");
		
		if ($this->_from === false) {
			$this->error = "NeedParam";
			$this->errordesc = "from";
			return;
		}
		
		if (!IsGoodNum($this->_from)) {
			$this->error = "BadFromField";
			return;
		}
		
		$title = $this->db->GetValue("SELECT title FROM sys_sections WHERE id = {$this->_from}");
		if (!$title) {
			$this->error = "MoveSectionIsNotExist";
			return;
		}
		
		if (!$this->_IsRightsFromOK($this->_from)) {
			$this->error = "BadMoveRights";
			return;
		}
		
		if ($this->sectionId == 0) {
			$this->_RebuildSort();
			
			$this->db->SQL("
				SELECT 
					@sort := MAX(sort) 
				FROM 
					sys_sections 
				WHERE 
					parent_id = 0
				GROUP BY sort	
			");
			
			$this->db->SQL("
				UPDATE 
					sys_sections 
				SET 
					parent_id = 0, path = '0', sort = @sort + 1
				WHERE 
					id = {$this->_from}
			");
			
			$this->_UpdateChilderenPath($this->_from);
		} else {
			$row = $this->db->GetRow("SELECT title, path, CONCAT(',',path,',') LIKE '%,{$this->_from},%' AS `from` FROM sys_sections WHERE id = {$this->sectionId}");
			if (!$row) {
				$this->error = "IDNotFound";
				$this->errordesc = $this->sectionId;
			}
			
			if ($this->_from == $this->sectionId || $row->from == 1) {
				$this->error = "BadMoveNode";
			} else {
				$this->_RebuildSort();
				
				$this->db->SQL("
					SELECT 
						@sort := MAX(sort) 
					FROM 
						sys_sections 
					WHERE 
						parent_id = 0
					GROUP BY sort	
				");
				
				$this->db->SQL("UPDATE sys_sections SET parent_id = {$this->sectionId}, path = '{$row->path},{$this->sectionId}', sort = @sort WHERE id = {$this->_from}");
				
				$this->_UpdateChilderenPath($this->_from);
			}
		}

		$this->addinfo = $title;	
	}
	
	function GetErrorDesc() {
		return $this->errordesc;
	}
	
	function _IsRightsFromOK($secID) {
		$rightsFrom = $this->auth->GetSectionRights($secID);
		return ($rightsFrom["Edit"]);
	}
	
	function GetAdditionalInfo() {
		return $this->addinfo;
	}
	
	function GetRetPath() {
		$this->_SetRetPath();
		return $this->retpath;
	}
	
	/**
	 * @access private
	 */
	function _SetRetPath() {
		$this->retpath = ($this->query->GetReferer() != "") ? URLReplaceParam("id", $this->_from, URLDeleteParam("from", $this->query->GetReferer())) : false;
	}
		
}
?>