<?php

/**
 * Класс, обеспечивающий выдачу дерева секций в XML
 */

class TreeReadClass extends ReadModuleBaseClass {

	public function CreateXML() {
		// Список на создание
		$createListNode = $this->xml->createElement("createList");
		$this->parentNode->appendChild($createListNode);

		$stmt = $this->db->SQL("SELECT id, title, ancestor FROM sys_createsec_types ORDER BY id");
		$this->dt->ProcessQueryResults(
			$stmt, $this->xml, $createListNode, "blank", false, false, 0 , "", false, null, "item"
		);
		
		// Корневая нода root
		$sectionTreeNode = $this->xml->createElement("section");
		$sectionTreeNode->setAttribute("id", 0);
		$sectionTreeNode->setAttribute("title", $this->globalvars->GetStr("RootSectionName"));

		$rights = $this->auth->GetSectionRights(0);
		$this->_AddRights($sectionTreeNode, $rights);

		$this->parentNode->appendChild($sectionTreeNode);

		// Дерево секций XML
		$this->getSectionTree($this->xml, $sectionTreeNode, 0);

		return true;		
	}

	private function getSectionTree($xml, $rootNode, $rootID) {
		// Выбираем секции, являющиеся непосредственными потомками секции с ID=$rootID
		$stmt = $this->db->SQL("SELECT id, enabled, name, title, hidden, onmap, `out`, auth, redirect_url, go_to_child FROM sys_sections WHERE parent_id='{$rootID}' ORDER BY sort");
		if (!$stmt->rowCount()) {
			return;
		}
		
		while ($row = $stmt->fetchObject()) {
			$rights = $this->auth->GetSectionRights($row->id);
						
			// Выводим права в XML
			$newNode = $xml->createElement("section");
			$this->_AddRights($newNode, $rights);
			
			$newNode->setAttribute("id", $row->id);
			$newNode->setAttribute("enabled", $row->enabled);
			$newNode->setAttribute("name", $row->name);
			$newNode->setAttribute("title", $row->title);
			$newNode->setAttribute("hidden", $row->hidden);
			$newNode->setAttribute("onMap", $row->onmap);
			$newNode->setAttribute("out", $row->out);
			$newNode->setAttribute("auth", $row->auth);

			if ($row->redirect_url) {
				// если есть URL переадресации
				$newNode->setAttribute("isRedirect", "1");
				$newNode->setAttribute("URL", $row->redirect_url);
			} else if ($row->go_to_child == 1) {
				// если включен переход к первой дочерней секции
				$newNode->setAttribute("goToChild", 1);
				// селектируем из базы первую дочернюю
				// если у первой дочерней есть URL переадресации или включен переход к первой дочерней - тогда швах
				$stmtChild = $this->db->SQL("SELECT id, name, go_to_child FROM sys_sections WHERE parent_id = {$row->id} ORDER BY sort");
				$fl = ($stmtChild->rowCount() > 0);
				if ($fl) {
					$row2 = $stmtChild->fetchObject();
					// цикл по всем дочерним секция дочерней текущей секции - пока не найдем секцию, у которой go_to_child = 0
					while ($row2->go_to_child != 0 && $fl) {
						$stmtChild = $this->db->SQL("SELECT id, name, go_to_child FROM sys_sections WHERE parent_id = {$row2->id} ORDER BY sort");
						$fl = ($stmtChild->rowCount() > 1);
						if ($fl) {
							$row2 = $stmtChild->fetchObject();
						}
					}
				}
				$tmpName = (!$fl) ? $row->name : $row2->name;
				$newNode->setAttribute("URL", $this->conf->Param("Prefix") . $tmpName . "/");
			} else {
				$newNode->setAttribute("URL", $this->conf->Param("Prefix") . $row->name . "/");
			}

			// выводим информацию о мета-тегах
			$stmtMeta = $this->db->SQL("SELECT id, name, content FROM sys_section_meta WHERE ref = {$row->id}");
			if ($stmtMeta->rowCount()) {
				while ($rowMeta = $stmtMeta->fetchObject()) {
					$newMetaNode = $xml->createElement("meta", XMLEntities($rowMeta->content));
					$newMetaNode->setAttribute("id", $rowMeta->id);
					$newMetaNode->setAttribute("name", htmlspecialchars($rowMeta->name));
					
					$newNode->appendChild($newMetaNode);
				}
			}
			
			$rootNode->appendChild($newNode);
			
			// Рекурсивно вызываем эту же функцию. Это долго. Надо подключить кэш
			$this->getSectionTree($xml, $newNode, $row->id);
		}
	}

	private function _AddRights($node, &$rights) {
		$node->setAttribute("read", $rights["Read"] ? "1" : "0");
		$node->setAttribute("create", $rights["Create"] ? "1" : "0");
		$node->setAttribute("edit", $rights["Edit"] ? "1" : "0");
		$node->setAttribute("delete", $rights["Delete"] ? "1" : "0");
		$node->setAttribute("editName", $rights["EditName"] ? "1" : "0");
		$node->setAttribute("editEnabled", $rights["EditEnabled"] ? "1" : "0");
		
		return true;
	}

}

?>