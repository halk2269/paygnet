<?php
require_once(CMSPATH_LIB . "logging/useractionlogging.php");
require_once(CMSPATH_CORE . "sysconf/sectionactions.conf.php");
/*

Работа с секциями

Автор: IDM

Возможные варианты выдаваемых ошибок
BadRights (проблемы с правами - теоретически, ошибки появиться не должно - модуль чтения это отслеживает)
BadIDField
BlankString
NeedParam (description - param name)
==== Возможные варианты для description:
==== - "title" - название секции;
==== - "newname" - новое имя секции;
==== - "from" - id секции назначения.
IDNotFound (description - id)
BadNameChars (разрешено: [-_a-zA-Z0-9])
NameExists (description - new name)
CantMoveTopmost
CantMoveBottommost
BadFROMField
BadMoveNode

Возвожные варианты выдаваемой информации
SectionWasRenamed (description - new title)
NameWasChanged (description - new name)
SectionWasShown
SectionWasHidden
SectionWasShownOnMap
SectionWasHiddenOnMap
SectionWasEnabled
SectionWasDisabled
SectionWasDeleted
SectionWasCreated (description - title)
SectionMovedUp (description - title)
SectionMovedDown (description - title)
SectionMovedToTop (description - title)
SectionMovedToBottom (description - title)
SectionMoved (description - title)
MetaWasCreated
MetaWasDeleted
MetaWasChanged
*/

class SectWriteClass extends WriteModuleBaseClass {

	/**
	 * @var SectionActionsConf
	 */
	private $sectionActionsConf;

	public function MakeChanges() {
		$act = $this->_GetParam("act");
		$id = $this->_GetParam("id");
		
		if (!$act || !$id) {
			$this->inputError = true;
			return false;
		}
		
		if (!IsGoodNum($id)) {
			$this->_WriteError("BadIDField");
			return false;
		}
		
		$rights = $this->auth->GetSectionRights($id);
		$this->sectionActionsConf = new SectionActionsConf($this->query, $rights);
		
		if ($rights["Edit"] || $rights["Create"] || $rights["Delete"]) {
			$this->cache->ClearSectionTree();
		}

		if ($this->conf->Param("LogUserActions")) {
			$sectionTitle = $this->db->GetValue("SELECT title FROM sys_sections WHERE id = {$id}");
			$logger = new UserActionLogging();

			if ($this->_GetParam("from") && IsGoodId($this->_GetParam("from"))) {
				$loggedId = $this->_GetParam("from");
			} else {
				$loggedId = $id;
			}
			
			$logger->AddSectionAction($loggedId, $sectionTitle, $act);
		}

		if (
			$act == "rename" 
			|| $act == "chname" 
			|| $act == "enable" 
			|| $act == "disable" 
			|| $act == "create" 
			|| $act == "show" 
			|| $act == "hide" 
			|| $act == "showonmap" 
			|| $act == "hideonmap" 
			|| $act == "delete" 
			|| $act == "gotochild" 
			|| $act == "move"
		) {
			$sectionAction = $this->sectionActionsConf->GetSectionActionClass($act);
			if (is_null($sectionAction)) {
				return false;
			}

			if ($sectionAction->GetError()) {
				$this->_WriteError($sectionAction->GetError(), $sectionAction->GetErrorDesc());
				return false;
			}
			
			if ($sectionAction->GetRetPath()) {
				$this->retPath = $sectionAction->GetRetPath();
			}

			$this->_WriteInfo($sectionAction->GetInfo(), $sectionAction->GetAdditionalInfo());
			return true;
		} else if ($act == "moveup" || $act == "movedown" || $act == "movetotop" || $act == "movetobottom") {
			// Вверх-вниз
			$value = $this->db->GetValue("SELECT parent_id FROM sys_sections WHERE id = {$id}");
			$parentID = ($value) ? $value : -1;
			if ($parentID == -1) {
				$this->inputError = true;
				return false;
			}
			
			$parentRights = $this->auth->GetSectionRights($parentID);
			if (!$parentRights["Edit"] && $parentID || !$rights["Edit"] && !$parentID) {
				$this->_WriteError("BadRights");
				return false;
			}
			
			$this->_RebuildSort($parentID, $lastSortIndex);
			
			$ownStmt = $this->db->SQL("SELECT title, sort FROM sys_sections WHERE id = {$id}");
			if ($ownrow = $ownStmt->fetchObject()) {
				if ($act == "movetotop") {
					$this->db->SQL("UPDATE sys_sections SET sort = 0 WHERE id = {$id}");
					$this->_WriteInfo("SectionMovedToTop", $ownrow->title);
					
					return true;
				} else if ($act == "movetobottom") {
					$this->db->SQL("UPDATE sys_sections SET sort = 999998 WHERE id = {$id}");
					$this->_WriteInfo("SectionMovedToBottom", $ownrow->title);
					
					return true;
				} else if ($act == "moveup") {
					if ($ownrow->sort == 1) {
						$this->_WriteError("CantMoveTopmost", $ownrow->title);
						return false;
					} else {
						$sort = $ownrow->sort - 1;
						$changeStmt = $this->db->SQL("SELECT id FROM sys_sections WHERE sort = {$sort} and parent_id = {$parentID}");
						if ($changeRow = $changeStmt->fetchObject()) {
							$this->db->SQL("UPDATE sys_sections SET sort = {$sort} WHERE id = {$id}");
							$this->db->SQL("UPDATE sys_sections SET sort = {$ownrow->sort} WHERE id = {$changeRow->id}");
						}
						
						$this->_WriteInfo("SectionMovedUp", $ownrow->title);
						return true;
					}
				} else if ($act == "movedown") {
					if ($ownrow->sort == $lastSortIndex) {
						// This row is a bottommost one in the list
						$this->_WriteError("CantMoveBottommost", $ownrow->title);
						return false;
					} else {
						$sort = $ownrow->sort + 1;
						$changeStmt = $this->db->SQL("SELECT id FROM sys_sections WHERE sort = {$sort} AND parent_id = {$parentID}");
						if ($changeRow = $changeStmt->fetchObject()) {
							$this->db->SQL("UPDATE sys_sections SET sort = {$sort} WHERE id = {$id}");
							$this->db->SQL("UPDATE sys_sections SET sort = {$ownrow->sort} WHERE id = {$changeRow->id}");
						}
						
						$this->_WriteInfo("SectionMovedDown", $ownrow->title);
						return true;
					}
				}
			} else {
				$this->_WriteError("IDNotFound", $id);
				return false;
			}
		} else if ($act == "chredirect") {
			// изменение URL переадресации

			// проверяем права на изменение URL`а переадресации
			if (!$rights["Edit"]) {
				$this->_WriteError("BadRights");
				return false;
			}
			
			// получаем и проверяем параметр newname
			$newname = $this->_GetParam("newname");
			if (!$newname) {
				$this->_WriteError("NeedParam", "newname");
				return false;
			}
			
			$newname = mb_substr(trim($newname), 0, 255);
			if ($newname && !preg_match("~^([a-zA-Z0-9\.\/\=\?\&\-\_\#\~\%\;\,\:\']+)$~", $newname)) {
				$this->_WriteError("BadRedirectChars");
				return false;
			}
						
			// если новое имя прошло проверку - записываем его в базу
			$stmt = $this->db->SQL("SELECT redirect_url FROM sys_sections WHERE id = '{$id}'");
			if (($row = $stmt->fetchObject()) && $newname == $row->redirect_url) {
				$this->_WriteInfo("NameWasChanged", $newname);
				return true;
			} else {
				$stmtUpdate = $this->db->SQL(
					"UPDATE sys_sections SET redirect_url = ? WHERE id = ?",
					array($newname, $id)
				);
				if (!$stmtUpdate->rowCount()) {
					$this->_WriteError("IDNotFound", $id);
					return false;
				}
				
				$this->_WriteInfo("RedirectWasChanged", $newname);
				return true;
			}
		} elseif (($act == "metaedit") or ($act == "metadelete")) {
			// МЕТА ТЕГИ
			// добавление, редактирование (metaedit) и удаление (act = metadelete) мета-тегов
			// должен быть передан параметр metaid - id мета-тега в таблице мета-тегов
			// если редактирование или создание нового мета-тега, то также должен быть передан
			// параметр text. он содержит в себе строку вида name:content - соответственно имя
			// и содержание мета тега

			// проверяем права на изменение мета-тегов
			if (!$rights["Edit"]) {
				$this->_WriteError("BadRights");
				return false;
			}
			// получаем и проверяем параметр metaid
			$metaID = $this->_GetParam("metaid");
			if (false === $metaID) {
				$this->_WriteError("NeedParam", "metaid");
				return false;
			}
			
			if (!IsGoodNum($metaID)) {
				$this->_WriteError("BadMetaIDField");
				return false;
			}
			
			if ($act == "metaedit") {
				// получаем параметр text и разбраем его на куски
				$newMeta = $this->_GetParam("text");
				if (!$newMeta) {
					$this->_WriteError("NeedParam", "text");
					return false;
				}
				
				$newMeta = trim($newMeta);
				$res = preg_match("~^([a-zA-Z-_]+)\s*:\s*(.*)$~i", $newMeta, $out);
				// если нет соответствия шаблону регулярного выражения, то выдаем ошибку
				if (!$res) {
					$this->_WriteError("BadMetaTextField");
					return false;
				}
				
				// ошибки не было - работаем дальше
				$metaName = $out[1];
				$metaContent = $out[2];

				// если редактирование или создание нового мета-тега
				if ($metaID) {
					// редактирование уже существующего мета-тега
					$this->db->SQL(
						"UPDATE 
							sys_section_meta 
						SET 
							name = ?, content = ? 
						WHERE 
							id = ?",
						array($metaName, $metaContent, $metaID)
					);
					
					$this->_WriteInfo("MetaWasChanged", $id);
					return true;
				} else {
					// создание нового мета-тега
					$stmt = $this->db->SQL(
						"INSERT 
							sys_section_meta (ref, name, content) 
						VALUES 
							(?, ?, ?)",
						array($id, $metaName, $metaContent)
					);
					
					if (!$stmt->rowCount()) {
						$this->_WriteError("DBError");
						return false;
					}
					
					$this->_WriteInfo("MetaWasCreated", $id);
					return true;
				}
			} else if ($act == "metadelete") {
				$stmt = $this->db->SQL("DELETE FROM sys_section_meta WHERE id = {$metaID} LIMIT 1");

				if ($stmt->rowCount()) {
					$this->_WriteInfo("MetaWasDeleted", $id);	
				}
				
				return true;
			}
		} else {
			$this->inputError = true;
			return false;
		}
	}

	function _RebuildSort($parentID, &$lastSortIndex) {
		$listStmt = $this->db->SQL(
			"SELECT id FROM sys_sections WHERE parent_id = {$parentID} ORDER BY sort"
		);
		
		$i = 0;
		while ($listRow = $listStmt->fetchObject()) {
			$i++;
			$this->db->SQL(
				"UPDATE sys_sections SET sort = {$i} WHERE id = {$listRow->id}"
			);
		}
		
		$lastSortIndex = $i;
	}

	function _UpdateChilderenPath($id) {
		$pStmt = $this->db->SQL("SELECT path FROM sys_sections WHERE id = '{$id}'");
		if (!$pStmt->rowCount()) {
			return false;
		}
		
		$prow = $pStmt->fetchObject();
		
		$stmt = $this->db->SQL("SELECT id FROM sys_sections WHERE parent_id = '{$id}'");
		while ($row = $stmt->fetchObject()) {
			$this->db->SQL("UPDATE sys_sections SET path = '{$prow->path},{$id}' WHERE id = {$row->id}");
			$result = $this->_UpdateChilderenPath($row->id);
			if (!$result) {
				return false;
			}
		}
		
		return true;
	}


}
?>