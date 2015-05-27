<?php

/**
 * Перемещение документов с одинаковым типом документа в другой раздел
 */

class MoveWriteClass extends WriteModuleBaseClass {

	public function MakeChanges() {
		$elementsID = $this->_GetParam("elemToMove");
		
		$origRef = $this->_GetParam("origRef");
		$newRef = $this->_GetParam("newRef");
		$goToNewRef = $this->_GetParam("goToNewRef");
		
		$leaveCopy = $this->_GetParam("leaveCope");

		// проверка на корректность идентификаторов модулей
		if (!IsGoodNum($origRef) || !IsGoodNum($newRef)) {
			return false;
		}
		
		// проверка на корректность строки с номерами документов
		if (!preg_match('/^[0-9]{1,11}(,[0-9]{1,11})*$/', $elementsID)) {
			return false;
		}

		// проверка прав на создания/удаление документов в новом/старом модулях
		$refRightsNew = $this->auth->GetRefRights($newRef, &$adminMode);
		$refRightsOrig = $this->auth->GetRefRights($origRef, &$adminMode);
		if (!$refRightsNew["Create"] || !$refRightsOrig["Delete"]) {
			return false;
		}

		// получение идентификатора старой секции и параметров модуля
		$origSec = $this->db->GetRow("
			SELECT 
				s.name AS `section`, s.id AS `id`, r.params AS `params`
			FROM 
				sys_references r 
			JOIN 
				sys_sections s ON r.ref = s.id
			WHERE 
				r.id = '{$origRef}'
				AND r.params REGEXP 'inSelectRef[[:space:]]*=[[:space:]]*(\"|\')(own|owndeep)(\"|\')'
				AND r.params not REGEXP 'inShowInOwnXSL[[:space:]]*=[[:space:]]*true'
		");
		if (!$origSec) {
			return false;
		}
		$secRightsOrig = $this->auth->GetSectionRights($origSec->id);

		// получение названия Типа Документа
		eval($origSec->params);
		if (!isset($inDTName)) {
			return false;
		}

		// получение идентификатора новой секции и параметров модуля
		$newSec = $this->db->GetRow("
			SELECT 
				s.name AS `section`, s.id AS `id`
			FROM 
				sys_references r
			JOIN 
				sys_sections s ON r.ref = s.id
			WHERE 
				r.id = '{$newRef}'
				AND r.params REGEXP 'inDTName[[:space:]]*=[[:space:]]*(\"|\'){$inDTName}(\"|\')'
				AND r.params REGEXP 'inSelectRef[[:space:]]*=[[:space:]]*(\"|\')(own|owndeep)(\"|\')'
				AND r.params not REGEXP 'inShowInOwnXSL[[:space:]]*=[[:space:]]*true'
		");
		if (!$newSec) {
			return false;
		}
		$secRightsNew = $this->auth->GetSectionRights($newSec->id);

		//проверка прав на чтение в старой и новой секциях
		if (!$secRightsNew["Read"] || !$secRightsOrig["Read"]) {
			return false;
		}

		// Выбираем, куда переходить
		$this->retPath = ($goToNewRef) ? $newSec->section : $origSec->section;
		$this->retPath .= '/';

		// перенос документов
		if ($leaveCopy) {
			$ids = explode(",", $elementsID);
		
			foreach ($ids as $value) {
				$this->_CopyRow($value, $inDTName, $newRef);
			}
		} else {
			// перенос
			$this->db->SQL(
				"UPDATE dt_{$inDTName} SET ref = '{$newRef}' WHERE id IN ({$elementsID})"
			);
		}
		
		return true;
	}

	private function _CopyRow($id, $inDTName, $newRef, $subDoc = false) {
		$modifier = time();

		// если номер документа не является целым числом, отправляемся назад
		if ($id == 0) {
			return false;
		}

		$this->db->Begin();
		$origDoc[$inDTName] = $this->db->GetRow("SELECT * FROM dt_{$inDTName} WHERE id = {$id}");
		$field[$inDTName] = array();
		
		$error = false;
		$tableName = DocCommonClass::GetTableName($inDTName);
		foreach ($this->dtconf->dtf[$inDTName] as $key => $value) {
			if ($value['type'] == 'image' and intval($origDoc[$inDTName]->$key) != 0) {
				$lastId = $this->_CopyImage($inDTName, $key, $id, $modifier);

				if ($lastId) {
					$imageId = $this->db->GetLastID();
					$field[$inDTName][] = "{$imageId} AS `{$key}`";
					$origField[$inDTName][] = "`{$key}`";
				} else {
					$error = true;
					break;
				}
			} elseif ($value['type'] == 'file' and intval($origDoc[$inDTName]->$key) != 0) {
				$origFile = $this->db->GetRow("
					SELECT 
						file.id AS 'id', 
						file.filename AS 'filename', 
						CONCAT(MD5(CONCAT(file.filename, {$modifier})), '.', file.ext) AS 'newfilename'
					FROM 
						{$tableName} dt
					JOIN 
						sys_dt_files file ON dt.{$key} = file.id
					WHERE 
						dt.id = {$id}
				");

				if (file_exists(CMSPATH_UPLOAD . $origFile->filename)) {
					if (!copy(CMSPATH_UPLOAD . $origFile->filename, CMSPATH_UPLOAD . $origFile->newfilename)) {
						$error = true;
						break;
					}
					
					$this->db->SQL("
						INSERT INTO sys_dt_files
							(name, ext, size, mimetype, filename)
						SELECT 
							name, ext, size, mimetype, 
							'{$origFile->newfilename}' AS 'filename' 
						FROM 
							sys_dt_files 
						WHERE 
							id = {$origFile->id}
					");

					$fileId = $this->db->GetLastID();
					$field[$inDTName][] = "{$fileId} AS '{$key}'";
					$origField[$inDTName][] = "`{$key}`";
				} else {
					$error = true;
					break;
				}
			} elseif ($value['type'] == 'select' and intval($origDoc[$inDTName]->$key) != 0) {
				$this->db->SQL("
					INSERT INTO sys_dt_select
						(list_id, list_title, item_id, item_name, item_title)
					SELECT 
						sel.list_id, sel.list_title, sel.item_id, sel.item_name, sel.item_title 
					FROM 
						{$tableName} dt
					JOIN
						sys_dt_select sel ON sel.id = dt.{$key}	
					WHERE 
						dt.id = {$id}
				");

				$selectId = $this->db->GetLastID();

				$field[$inDTName][] = "{$selectId} AS `{$key}`";
				$origField[$inDTName][] = "`{$key}`";
			} elseif ($value['type'] == 'strlist' and intval($origDoc[$inDTName]->$key) != 0) {
				$this->db->SQL("
					INSERT INTO sys_dt_strlist
						(text)
					SELECT 
						str.text 
					FROM
						{$tableName} dt
					JOIN
						sys_dt_strlist str ON str.id = dt.{$key}
					WHERE 
						dt.id = {$id}
				");

				$strId = $this->db->GetLastID();

				$field[$inDTName][] = "{$strId} AS `{$key}`";
				$origField[$inDTName][] = "`{$key}`";
			} else if ($value['type'] == 'array' && intval($origDoc[$inDTName]->$key) != 0) {
				$subTable = DocCommonClass::GetTableName($value['subt']);
				
				$stmt = $this->db->SQL("SELECT id FROM {$subTable} WHERE parent_id = {$id}");
				while ($childIds = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$insertedChildId = $this->_CopyRow($childIds['id'], $value['subt'], $newRef, true);	
					if (!$insertedChildId) {
						$error = true;
						break;
					}
				}
				
				$field[$inDTName][] = "{$key} AS `{$key}`";
				$origField[$inDTName][] = "`{$key}`";
			} else {
				$field[$inDTName][] = "{$key} AS `{$key}`";
				$origField[$inDTName][] = "`{$key}`";
			}
		}

		if ($error) {
			$this->db->Rollback();
			return false;
		}
		
		// Вставка копии документа, если с картинками все ок
		$fields = implode(",", $field[$inDTName]);
		$origFields = implode(",", $origField[$inDTName]);
		
		$setFields = "ref, enabled, {$origFields}, addtime";
		$selectedFields = "{$newRef} AS ref, 1 AS enabled, {$fields}, NOW() AS addtime";
		if ($subDoc) {
			$setFields .= ', parent_id, field_name, dt_name';
			$selectedFields .= ', 0 AS parent_id, field_name AS field_name, dt_name AS dt_name';	
		}
		
		$stmt = $this->db->SQL("
			INSERT INTO {$tableName} 
				($setFields)
			SELECT 
				 {$selectedFields}
			FROM 
				{$tableName}
			WHERE 
				id = {$id}
		");
				
		if ($stmt->rowCount() < 1) {
			$this->db->Rollback();
			return false;
		}
				
		$insertedDocID = $this->db->GetLastID();
		foreach ($this->dtconf->dtf[$inDTName] as $key => $value) {
			if ($value['type'] == 'array') {
				$subTable = DocCommonClass::GetTableName($value['subt']);
				$this->db->SQL("
					UPDATE
						{$subTable}
					SET
						parent_id = {$insertedDocID}
					WHERE
						ref = {$newRef} AND parent_id = 0
				");
			}
		}
		if (!$this->_CopyLinks($inDTName, $id, $insertedDocID)) {
			$this->db->Rollback();
			return false;
		}
		
		$this->db->Commit();

		return $insertedDocID;
	}

	private function _CopyImage($inDTName, $key, $id, $modifier) {
		$origImage = $this->db->GetRow("
			SELECT 
				image.id AS 'id',
				image.filename AS 'filename', 
				CONCAT(MD5(CONCAT(image.filename,{$modifier})), '.', image.ext) AS 'newfilename'
			FROM 
				dt_{$inDTName} dt 
			JOIN 
				sys_dt_images image ON dt.{$key} = image.id
			WHERE 
				dt.id = {$id}
		");

		if (!$origImage) {
			return false;
		}
		
		if (!file_exists(CMSPATH_UPLOAD . $origImage->filename)) {
			return false;
		}
		
		if (!copy(CMSPATH_UPLOAD . $origImage->filename, CMSPATH_UPLOAD . $origImage->newfilename)) {
			return false;
		}

		$this->db->SQL("
			INSERT INTO sys_dt_images
				(name, ext, size, mimetype, filename, width, height)
			SELECT 
				name, ext, size, mimetype, '{$origImage->newfilename}' AS filename,
				width, height 
			FROM 
				sys_dt_images 
			WHERE 
				id = {$origImage->id}
		");

		return $this->db->GetLastID();
	}

	/**
	 * Копируем связи документа
	 *
	 * @param string $inDTName
	 * @param int $origID
	 * @param int $newID
	 * @return bool
	 */
	private function _CopyLinks($inDTName, $origID, $newID) {
		if (!isset($this->dtconf->dtl[$inDTName])) {
			return true;
		}

		foreach ($this->dtconf->dtl[$inDTName] as $name => $value) {
			$linkedDT = $this->dtconf->dtl[$inDTName][$name]["doct"];
			$linksTable = "link_" . $inDTName . "_" . $linkedDT;

			$this->db->SQL("
				INSERT INTO {$linksTable} 
					(id_from, id_to)
				SELECT 
					{$newID} AS id_from,
					id_to
				FROM 
					{$linksTable}
				WHERE 
					id_from = {$origID}
			");
		}
		
		return true;
	}

}
?>