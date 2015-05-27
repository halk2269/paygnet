<?php 
require_once(CMSPATH_LIB . "section/abstractsectaction.php");

/**
 * Включение секции
 */

class CreateSectAction extends AbstractSectAction {

	protected $right = "Create";
	protected $info = "SectionWasCreated";

	protected  $lastSort = 0;
	
	private $createTypeId;
	private $createType = array();
	
	private $newSectionId;
	private $newSectionTitle = "";
	private $newSectionName = "";
	private $newSectionPath = "";

	private $newRefencesIds = array();

	function GetNewSectionId() {
		return (int)$this->newSectionId;
	}

	protected function _MakeChanges() {
		if (!$this->query->GetParam("title") || !mb_strlen($this->query->GetParam("title"))) {
			$this->error = "BlankString";
			return;
		}
		
		$this->newSectionTitle = mb_substr($this->query->GetParam("title"), 0, 255);
		
		if (!$this->_SetCreateType()) {
			$this->error = "BadCreateType";
			return;
		}

		$this->_SetNewSectionName();
		$this->_SetNewSectionPath();

		$this->db->Begin();

		$this->_RebuildSort();

		$this->_CreateNewSection();
		$this->_CreateSectionRights();

		foreach ($this->createType as $id => $value) {
			$this->_CreateReference($id);
			
			if ($this->createType[$id]->create_dt) {
				$this->_CreateDocument($id);
			}
			
			$this->_CreateReferenceRights($id);
		}

		$this->db->Commit();
	}

	function _SetCreateType() {
		if (
			!$this->query->GetParam("createtype") 
			|| !IsGoodId($this->query->GetParam("createtype"))
		) {
			return false;
		}
		
		$this->createTypeId = (int)$this->query->GetParam("createtype");

		$stmt = $this->db->SQL("
			SELECT
				r.id, 
				r.class, 
				r.filename, 
				r.xslt, 
				r.params, 
				r.create_dt, 
				r.priority, 
				r.loadinfo, 
				r.inherited
			FROM 
				sys_createsec_types t 
			JOIN 
				sys_createsec_refs r ON t.id = r.ref
			WHERE 
				t.id = {$this->createTypeId}");

		while ($row = $stmt->fetchObject()) {
			$this->createType[] = $row;
		}

		return ($stmt->rowCount() > 0);
	}

	function _SetNewSectionName() {
		$nameBase = $this->db->GetValue("
			SELECT 
				name AS name
			FROM 
				sys_sections 
			WHERE 
				id = {$this->sectionId}
			UNION
				SELECT 'section' AS name
			LIMIT 1
		");
		
		$i = 0;
		do {
			$this->newSectionName = $nameBase . ++$i;
		} while (
			$this->db->GetValue(
				"SELECT id FROM sys_sections WHERE name = ?",
				array($this->newSectionName)
			)
		);
	}

	function _SetNewSectionPath() {
		$this->newSectionPath = $this->db->GetValue("
				SELECT 
					CONCAT(path, ',', {$this->sectionId}) AS path 
				FROM 
					sys_sections 
				WHERE 
					id = {$this->sectionId}
			UNION
				SELECT 
					'0' AS path 
				LIMIT 1
		");
	}

	function _CreateNewSection() {
		$globalVars = GlobalVarsClass::GetInstance();
		$xslt = $globalVars->GetStr("DefaultSectionTpl");

		$this->db->SQL(
			"INSERT INTO 
				sys_sections (parent_id, name, title, enabled, hidden, onmap, sort, path, xslt) 
			VALUES
				(?, ?, ?, ?, ?, ?, ?, ?, ?)",
			array(
				$this->sectionId, 
				$this->newSectionName, 
				$this->newSectionTitle, 
				1, 
				0, 
				1, 
				$this->lastSort, 
				$this->newSectionPath, 
				$xslt
			)	
		);

		$this->newSectionId = (int)$this->db->GetLastID();
	}

	function _CreateSectionRights() {
		$this->db->SQL("
			INSERT INTO 
				sys_section_rights (section_id, role_id, rights) 
			SELECT 
				{$this->newSectionId} AS section_id,
				role_id AS role_id,
				rights AS rights
			FROM 
				sys_createsec_secrights sr
			WHERE 
				sr.ref = {$this->createTypeId}	
		");
	}

	function _CreateReference($i) {
		$this->db->SQL("
			INSERT INTO 
				sys_references (enabled, ref, class, filename, xslt, params, priority, loadinfo, inherited, comment) 
			VALUES 
				(1, {$this->newSectionId}, '{$this->createType[$i]->class}', '{$this->createType[$i]->filename}', 
			 	'{$this->createType[$i]->xslt}', '{$this->createType[$i]->params}','{$this->createType[$i]->priority}', 
			 	{$this->createType[$i]->loadinfo}, {$this->createType[$i]->inherited}, '{$this->newSectionTitle}')
		");
		
		$this->newRefencesIds[] = (int)$this->db->GetLastID();
	}

	function _CreateDocument($i) {
		$this->db->SQL("INSERT INTO dt_{$this->createType[$i]->create_dt} (enabled, ref, addtime, chtime) VALUES (1, {$this->newRefencesIds[$i]}, NOW(), NOW())");
	}

	function _CreateReferenceRights($i) {
		$this->db->SQL("
			INSERT INTO 
				sys_ref_rights (ref_id, role_id, rights) 
			SELECT 
				{$this->newRefencesIds[$i]} AS ref_id,
				role AS role_id,
				rights AS rights
			FROM 
				sys_createsec_refrights rr
			WHERE 
				rr.ref = {$this->createType[$i]->id}	
		");
	}
	
}
?>