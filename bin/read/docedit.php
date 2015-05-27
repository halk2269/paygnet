<?php

require_once(CMSPATH_LIB . "doc/doccommon.php");

/**
 * Модуль чтения для редактирования документов. 
 * Выводит типы полей документа.
 */

class DocEditReadClass extends ReadModuleBaseClass {
	
	public function __construct(
		$thisID, $queryClass, $xml, $parentNode, &$params, &$xslList, &$headers, &$rights, $adminMode
	) {
		parent::__construct(
			$thisID, $queryClass, $xml, $parentNode, $params, $xslList, $headers, $rights, $adminMode
		);
		
		$this->docCommon = new DocCommonClass();
	}
	
	public function CreateXML() {
		$qref = $this->_GetSimpleParam("qref");
		$id = $this->_GetSimpleParam("id");

		if (
			!$this->docCommon->CheckRights(
				($id) ? "Edit" : "CreateEnabled", "DocWritingWriteClass", $qref, $id
			)
		) {
			if ($this->docCommon->isBadRights() && $id != 0) {
				$this->_SetAccessDenied("CantModifyRef");
			}
			
			if ($this->docCommon->isBadRights() && $id == 0) {
				$this->_SetAccessDenied("CantCreateDoc");
			}
			
			return false;
		}

		// Получаем параметры из базы из таблицы sys_references для референса
		$params = $this->docCommon->GetParams();
		$dtName = $this->docCommon->GetDtName();
		
		$this->_CheckWriteModule($params);

		// Если мы редактируем документ
		if (!$this->_GetSimpleParam("subname")) {
			$this->_MakeMultiRefNode($dtName, $id);
			return $this->_MakeDocNode($dtName, $id, $params);
		}

		// Если мы редактируем поддокумент
		if ($this->_GetSimpleParam("id")) {
			return $this->_MakeSubDocNode($dtName, $id, $params);
		}

		return false;
	}

	private function _IsTablePrefixDisabled($params) {
		eval($params);
		return (!isset($inDisableDTPrefix)) ? false : $inDisableDTPrefix;
	}

	private function _CheckWriteModule($params) {
		eval($params);
		if (isset($inWriteModule)) {
			$this->parentNode->setAttribute("writeModule", $inWriteModule);
		}
	}
	
	/** 
	 * Отображать скрытые поля при создании
	 */
	private function _HideHiddenOnCreate($params) {
		eval($params);
		return (isset($hideHiddenOnCreate) && $hideHiddenOnCreate);
	}
	
	/**
	 * Возможность для редактирования скрытых полей.
	 * Необходмио для создания такого раздела, как "Вопрос-ответ" 
	 */
	private function _CanEditHidden($params) {
		eval($params);
		return (isset($canEditHidden) && $canEditHidden);
	}
	
	private function _MakeDocNode($dtName, $id, $params) {
		$showHidden = (
			!$id && !$this->_HideHiddenOnCreate($params) 
			|| $id && $this->_CanEditHidden($params) 
			|| $this->auth->IsDTSuperAccess()
		);
				
		$this->dt->GetFieldList($this->xml, $this->parentNode, $dtName, $showHidden);

		if ($id) {
			// редактирование существующего документа
			$stmt = $this->dt->FormatSelectQuery($dtName, $this->xml, $this->parentNode, "*", "", "", "dt.id = {$id}");
			if (!$stmt || !$stmt->rowCount()) {
				return false;
			}
			
			$this->dt->ProcessQueryResults($stmt, $this->xml, $this->parentNode, $dtName, false, false, 0, "", true, null, "document", true);
		}

		return true;
	}

	private function _MakeSubDocNode($dtName, $id, $params) {
		// Название поля-массива, элемент которого будет редактироваться/добавляться
		$subName = $this->_GetSimpleParam("subname");
		// id документа - элемента массива
		$subID = $this->_GetSimpleParam("subid");
		// $subID - нормальное число
		if ($subID === false || !IsGoodNum($subID)) {
			return false;
		}
		// Запрошенное поле существует...
		if (!isset($this->dtconf->dtf[$dtName][$subName])) {
			return false;
		}
		// ...и его тип - действительно массив...
		if (!isset($this->dtconf->dtf[$dtName][$subName]["type"])) {
			return false;
		}
		if ($this->dtconf->dtf[$dtName][$subName]["type"] != "array") {
			return false;
		}
		// ... и тип элементов массива определён
		if (!isset($this->dtconf->dtf[$dtName][$subName]["subt"])) {
			return false;
		}
		// Тип элементов массива
		$subDTName = $this->dtconf->dtf[$dtName][$subName]["subt"];
		
		// Выдача в XML, как и для нормального (не-массив) случая
		$hideHiddenOnCreate = $this->_HideHiddenOnCreate($params);
		$canEditHidden = $this->_CanEditHidden($params);
		
		$showHidden = (
			0 == $subID && !$hideHiddenOnCreate 
			|| $id && $canEditHidden 
			|| $this->auth->IsDTSuperAccess()
		);

		$this->dt->GetFieldList($this->xml, $this->parentNode, $subDTName, $showHidden);
		
		if ($subID) {
			// Редактирование, не создание элемента массива

			// $disableDTPrefix - определить
			$disableDTPrefix = $this->_IsTablePrefixDisabled($params);
			// Проверяем, наш ли это документ - существует ли он.
			$tblName = DocCommonClass::GetTableName($dtName, $disableDTPrefix);
			$subTblName = DocCommonClass::GetTableName($subDTName, $disableDTPrefix);
			
			$subdocNumber = $this->db->GetValue("
				SELECT 
					{$subName} AS 'number' 
				FROM 
					{$tblName} dt
				JOIN 
					{$subTblName} sub ON dt.id = sub.parent_id			
				WHERE 
					dt.id = {$id} AND sub.id = {$subID}
			");
		
			if (!$subdocNumber) {
				return false;
			}

			// Документ наш, выбираем его в XML
			$stmt = $this->dt->FormatSelectQuery(
				$subDTName, $this->xml, $this->parentNode, "*", "", "", "dt.id = {$subID}", 
				"", "", 0, 0, "dt.id, dt.enabled, dt.addtime, dt.chtime", $disableDTPrefix
			);
			if (!$stmt || !$stmt->rowCount()) {
				return false;
			}
			
			$this->dt->ProcessQueryResults(
				$stmt, $this->xml, $this->parentNode, $subDTName, false, false, 
				0, "", true, null, "document", true
			);
		}

		return true;
	}

	private function _MakeMultiRefNode($dtName, $id) {
		if (isset($this->dtconf->dtm[$dtName]) && $this->dtconf->dtm[$dtName]) {
			$multiCatTable = "dt_" . $dtName . "_ref";

			$stmt = $this->db->SQL("
				SELECT 
					r.id AS ref_id,
					s.name AS name,
					s.title AS title,
					IF(ISNULL(dtr.doc_id), 0, 1) AS selected
				FROM 
					sys_references r
					JOIN sys_sections s ON r.ref = s.id 
					LEFT JOIN {$multiCatTable} dtr ON r.id = dtr.cat_id AND dtr.doc_id = {$id}
				WHERE
					r.params REGEXP 'inDTName[[:space:]]*=[[:space:]]*(\"|\'){$dtName}(\"|\')'
					AND r.params REGEXP 'inSelectRef[[:space:]]*=[[:space:]]*(\"|\')(own|owndeep)(\"|\')'
					AND r.enabled = 1
					AND s.enabled = 1
			");
			if (!$stmt->rowCount()) {
				return false;
			}

			$prefix = $this->conf->Param("Prefix");
			$refsNode = X_CreateNode($this->xml, $this->parentNode, "refsNode");
						
			while ($section = $stmt->fetchObject()) {
				$refNode = X_CreateNode($this->xml, $refsNode, "ref", $section->title);
				
				$refNode->setAttribute("URL", $prefix . $section->name . "/");
				$refNode->setAttribute("id", $section->ref_id);
				$refNode->setAttribute("selected", $section->selected);
			}
		}
		
		return true;
	}
}
?>