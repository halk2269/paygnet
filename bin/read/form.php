<?php

/**
 * Модуль чтения для форм. Выводит типы полей документа.
 * 
 * 1. Нужны ли здесь $inShowInOwnXSL и $inAllowWriteDocClass?
 */

class FormReadClass extends ReadModuleBaseClass {
	
	const TO_SEND_TEXT = 'Вы можете отправить нам письмо, используя приведенную ниже форму:';
	const SUCCESS_TEXT = 'Отправлено!';
	const BUTTON_TEXT = 'Отправить';

	public function CreateXML() {
		$xslList = &$this->xslList;
		$rights = &$this->rights;
		$adminMode = $this->adminMode;

		$inDTName = "";
		$inTargetRef = "own";

		eval($this->params);
		if (isset($disableStringConstr) && $disableStringConstr) {
			X_CreateNode($this->xml, $this->parentNode, "disableStringConstr", "1");
		}
		
		if (!isset($inDTName)) {
			$this->_SetBadParamsDescr("Blank \$inDTName");
			return false;
		}	
		// возвращаем типы полей документа
		$this->dt->GetFieldList($this->xml, $this->parentNode, $inDTName, false);

		// Секция редактирования документов
		$editSectionName = $this->globalvars->GetStr("DocEditSectionName");
		// Есть права на создание новой секции? Добавляем ссылку на создание
		if ($rights["CreateEnabled"]) {
			$this->parentNode->setAttribute(
				"createURL", 
				$this->conf->Param("Prefix") . "{$editSectionName}/?qref={$this->thisID}&id=0&SID=" . $this->auth->GetSID()
			);
			$this->parentNode->setAttribute("createDocType", $this->dtconf->dtn[$inDTName]);
		}
		
		// Если выборка идёт не из родной связи, то выдаем в XML параметры целевой секции
		if ($inTargetRef != "own") {
			// Целевая секция
			$sectionName = $this->db->GetValue("
				SELECT 
					s.name AS sectionname 
				FROM 
					sys_references r 
				LEFT JOIN 
					sys_sections s ON r.ref = s.id 
				WHERE 
					r.id = {$inTargetRef} AND r.enabled = 1
			");
			
			if (!$sectionName) {
				$this->_SetBadParamsDescr("There is no target reference with id = '{$inTargetRef}' or this reference is not enabled");
				return false;
			}
			
			// Парамерты целевой секции
			if ($inTargetRef != "") {
				$this->_CreateTargetSection($inTargetRef, $sectionName);
			}
		}
		
		$this->OnAfterCreate();
		
		/**
		 * Создание ноды, содержащей данные, которые отображаются
		 * в форме обратной связи.
		 */
		$this->_CreateMessageNode();
								
		if (isset($feedbackRef) && IsGoodNum($feedbackRef)) {
			$isExist = $this->db->GetValue("
				SELECT ref FROM sys_references WHERE id = {$feedbackRef} AND enabled = 1
			");
			if (!$isExist) {
				$this->_SetBadParamsDescr(
					"There is no reference with id = '{$feedbackRef}' or this reference is not enabled"
				);
				return false;
			}
			
			$feedbackNode = X_CreateNode($this->xml, $this->parentNode, 'feedbackNode');
			$feedbackNode->setAttribute("id", $feedbackRef);
		}
		
		return true;
	}
	
	protected function OnAfterCreate() {
		return;
	}
	
	private function _CreateMessageNode() {
		eval($this->params);
		 
		$messageNode = X_CreateNode($this->xml, $this->parentNode, 'messageNode');
		$messageNode->setAttribute("tosendText", isset($inFormToSendText) ? $inFormToSendText : self::TO_SEND_TEXT);
		$messageNode->setAttribute("successText", isset($inFormSuccessText) ? $inFormSuccessText : self::SUCCESS_TEXT);
		$messageNode->setAttribute("buttonText",  isset($inFormButtonText) ? $inFormButtonText : self::BUTTON_TEXT);
	}
	
	private function _CreateTargetSection($inTargetRef, $sectionName) {
		$targetAdminMode = 0;
		
		$targetNode = $this->xml->createElement("target");
		$targetNode->setAttribute("targetSectionURL", $this->conf->Param("Prefix") . $sectionName . "/");
		$targetNode->setAttribute("targetRefID", $inTargetRef);
		
		$targetRights = $this->auth->GetRefRights($inTargetRef, $targetAdminMode);
		$targetNode->setAttribute("read", $targetRights["Read"] ? "1" : "0");
		$targetNode->setAttribute("create", $targetRights["Create"] ? "1" : "0");
		$targetNode->setAttribute("createEnabled", $targetRights["CreateEnabled"] ? "1" : "0");
		$targetNode->setAttribute("edit", $targetRights["Edit"] ? "1" : "0");
		$targetNode->setAttribute("delete", $targetRights["Delete"] ? "1" : "0");
		$targetNode->setAttribute("adminMode", $targetAdminMode ? "1" : "0");
		
		$this->parentNode->appendChild($targetNode);
	}

}

?>