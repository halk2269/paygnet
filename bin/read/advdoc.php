<?php

/**
 * Модуль для чтения документов с заданными типами
 * @author IDM
 */

class AdvDocReadClass extends ReadModuleBaseClass {
	
	function CreateXML() {
		// Обязательные параметры
		$inDTName = ""; // Тип документа (важно, чтобы знать, из какой таблицы пойдёт запрос)
		
		// Необязательные параметры
		$inDTFields = "*-"; // Список полей ТД. Поля обязаны присутствовать в объявлении ТД.
		$inAuxFields = ""; // Дополнительные поля для селекта из БД
		$inJoins = ""; // Дополнительные JOIN-ы
		$inWhere = ""; // WHERE-часть запроса (без слова WHERE)
		$inOrder = ""; // ORDER-часть запроса (без слова ORDER)
		$inLimit = ""; // LIMIT-часть запроса (без слова LIMIT)
		$inPerPage = 0; // Число документов на страницу. Ноль означает отсуствие постраничной разбивки
		$inSelectRef = "own"; // Тип запроса по модулю выборки
		/*
		$inSelectRef:
		1) "own" - выборка из своей секции, запрос конкретных документов запрещён
		2) "owndeep" - выборка из своей секции, запрос конкретных документов разрешён
		3) "define" - выборка по всем документам, независимо от привязки.
		Каждому документу в XML сопоставляется целевая секция и модуль
		4) Целое число больше нуля - выборка из документов указанного модуля (по id)
		*/
		$inShowInOwnXSL = false; // Отображение документов в их собственном XSL-правиле
		$inEnabledCheck = true; // Выполнять ли проверку enabled = 1 для обычных пользователей (AdminMode - всегда проверка отключена)
		$inStdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime"; // Список стандартных полей ТД для выборки в XML
		$inDisableDTPrefix = false; // Из какой таблицы пойдёт запрос.
		/*
		$inDisableDTPrefix:
		Если true - в начало названия ТД не подставляется префикс "dt_", чтобы получить название таблицы
		*/
		$inShowDocType = false; // Выдавать в XML описание ТД (будет в выдаче над данными)
		$alwaysLimit = false;
		/** 
		 * Осуществлять редирект на конкретный документ, если в указанном модуле
		 * существует только один документ.
		 * $inSelectRef = "owndeep" - естественно.
		 */
		$directlyDeep = false;
		$noSubSelectFields = "";
				
		// Выбираем параметры из БД
		eval($this->params);

		// Тип документа должен быть определён обязательно!
		if ($inDTName == "") {
			$this->_SetBadParamsDescr("Blank \$inDTName");
			return false;
		}

		// Добавляем список полей документа, если надо
		if ($inShowDocType) {
			$this->dt->GetFieldList($this->xml, $this->parentNode, $inDTName, false);
		}

		// Проверяем целевую секцию и выводим информацию о ней в XML
		$rv = $this->_CheckTargetSection($inSelectRef, $targetSectionName);
		if (!$rv) {
			return false;
		}
		$enabledCheck = $inEnabledCheck and !$this->adminMode;
		
		// Формируем запрос
		$sql = $this->_FormatDTQuery(
			$inDTName, $inDTFields, $inAuxFields, 
			$inJoins, $inWhere, $inOrder, $inLimit,
			$inPerPage, $inSelectRef, $inShowInOwnXSL, $enabledCheck, 
			$inStdFields, $inDisableDTPrefix, $alwaysLimit, $directlyDeep
		);

		if (!$sql) {
			return false;
		}

		// Делаем выборку в XML на базе сформированного запроса
		$this->dt->ProcessQueryResults(
			$sql, $this->xml, $this->parentNode, $inDTName,
			$canEdit = $this->rights["Edit"], $canDelete = $this->rights["Delete"],
			$ref = (!IsGoodNum($inSelectRef)) ? $this->thisID : $inSelectRef,
			$targetSectionName, true, null, $rowNodeName = "document",
			false, 0, "", $noSubSelectFields, true, $enabledCheck
		);

		if (
			($inSelectRef == "own" || $inSelectRef == "owndeep") 
			&& !(isset($this->dtconf->dtm[$inDTName]) && $this->dtconf->dtm[$inDTName])
		) {
			$this->_WriteRefsWithSameDT($inDTName);
		}

		return true;
	}

	// получение референсов с тем же ТД, что и у текущего
	protected function _WriteRefsWithSameDT($inDTName) {
		// получение прав на

		$parentid = $this->parentNode->getAttribute('id');
		$curRefRights = $this->auth->GetRefRights($parentid, $adminMode);

		if ($curRefRights["Delete"]) {
			// получение модулей с тем же Типом Документа, что и у текущего
			$stmt = $this->db->SQL("
				SELECT 
					r.id AS id, s.id AS secid
				FROM 
					sys_references r 
				JOIN
					sys_sections s ON r.ref = s.id
				WHERE 
					r.params REGEXP 'inDTName[[:space:]]*=[[:space:]]*(\"|\'){$inDTName}(\"|\')' AND
					r.params REGEXP 'inSelectRef[[:space:]]*=[[:space:]]*(\"|\')(own|owndeep)(\"|\')' AND
					r.params NOT REGEXP 'inShowInOwnXSL[[:space:]]*=[[:space:]]*true'
			");

			if (!$stmt->rowCount()) {
				return;
			}

			// создание ноды, куда будут помещаться  выбранные модули
			$refEditNode = $this->xml->createElement("refEdit");

			$this->parentNode->appendChild($refEditNode);
			$refEditNode->setAttribute('docType', $inDTName);

			// получение уровня иерархии, от которого будет писаться название в списке модулей
			$pathLevelBeginFrom = $this->globalvars->GetInt("PathLevelBeginFrom");
			$refEditNode->setAttribute('pathLevelBeginFrom', $pathLevelBeginFrom);

			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$refRights = $this->auth->GetRefRights($row['id'], $adminMode);
				$secRights = $this->auth->GetRefRights($row['secid'], $adminMode);
				
				if ($refRights["Create"] and $secRights["Read"] and ($row['id'] != $parentid)) {
					$allowedNode = $this->xml->createElement("allowedRef");
					$refEditNode->appendChild($allowedNode);
					$allowedNode->setAttribute('id', $row['id']);
					$allowedNode->setAttribute('secId', $row['secid']);
				}
			}
		}

		return;
	}

	/**
	 * $selectRef must be integer or equal to 'own'
	 */
	private function _CheckTargetSection($selectRef, &$targetSectionName) {
		// Имя целевой секции
		$targetSectionName = "";
		// Выборка идёт не из родной связи
		if ($selectRef != "own" && $selectRef != "owndeep" && $selectRef != "define") {
			// $selectRef должно быть числом - это id секции
			if (!IsGoodNum($selectRef)) {
				$this->_SetBadParamsDescr("Target reference is not a good number: '{$selectRef}'");
				return false;
			}
			
			// Целевая секция. Важно! Здесь не идёт проверки на enabled для целевой секции и связи
			$targetSectionName = $this->db->GetValue("
				SELECT 
					s.name AS sectionname 
				FROM 
					sys_references r 
				LEFT JOIN 
					sys_sections s ON r.ref = s.id 
				WHERE r.id = {$selectRef}
			");
			
			if (!$targetSectionName) {
				$this->_SetBadParamsDescr("There is no target reference with id = '{$selectRef}'");
				return false;
			}
			
			$this->_CreateTargetSection($selectRef, $targetSectionName);
		}
		return true;
	}
	
	private function _CreateTargetSection($selectRef, $targetSectionName) {
		$targetAdminMode = 0;
		
		// Парамерты целевой секции
		$targetNode = $this->xml->createElement("target");
		$targetNode->setAttribute("targetSectionName", $targetSectionName);
		$targetNode->setAttribute("targetSectionURL", $this->conf->Param("Prefix") . $targetSectionName . '/');
		
		$targetRights = $this->auth->GetRefRights($selectRef, $targetAdminMode);
		$targetNode->setAttribute("read", $targetRights["Read"] ? "1" : "0");
		$targetNode->setAttribute("create", $targetRights["Create"] ? "1" : "0");
		$targetNode->setAttribute("createEnabled", $targetRights["CreateEnabled"] ? "1" : "0");
		$targetNode->setAttribute("edit", $targetRights["Edit"] ? "1" : "0");
		$targetNode->setAttribute("delete", $targetRights["Delete"] ? "1" : "0");
		$targetNode->setAttribute("adminMode", $targetAdminMode ? "1" : "0");
		
		$this->parentNode->appendChild($targetNode);
	}

	/*
	$selectRef = (1) "own" | (2) "owndeep" | (3) "define" | (4) id (int)
	То есть
	(1) выборка идёт из своей секции (thisID),
	(2) выборка идёт из своей секции (thisID), причём разрешено отображение документа в рамках собственного шаблона,
	(3) целевая секция определяется внутри метода,
	(4) целевая секция указывается явно, по id.
	*/
	function _FormatDTQuery(
		$dtName, $dtFields = "*", $auxFields = "", $joins = "", $where = "", $order = "", $limit = "",
		$perPage = 0, $selectRef = "owndeep", $showInOwnXSL = false, $enabledCheck = true,
		$stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime", $disableDTPrefix = false,
		$alwaysLimit = false, $directlyDeep = false, $distinct = false
	) {
		// Стандартные переопределения
		$xml = $this->xml;
		$parentNode = $this->parentNode;
		$xslList = &$this->xslList;
		$rights = &$this->rights;
		$adminMode = $this->adminMode;

		// Для уверенности, что операции будут выполняться в надлежащем порядке
		if (trim($where) == "") {
			$where = "true";
		}
		$where = "($where)";

		// Проверка корректности поля $selectRef
		if ($selectRef != "own" && $selectRef != "owndeep" && $selectRef != "define" && !IsGoodNum($selectRef)) {
			return false;
		}

		$multiCategory = (isset($this->dtconf->dtm[$dtName])) ? $this->dtconf->dtm[$dtName] : false;

		switch ($selectRef) {
			case "own" :
			case "owndeep" : {
				if ($multiCategory) {
					$multiTable = "dt_" . $dtName . "_ref";
					$joins .= " JOIN {$multiTable} dtref ON dt.id = dtref.doc_id";
					$where .= " AND (dtref.cat_id = {$this->thisID})";
				} else {
					$where .= " AND (dt.ref = {$this->thisID})";
				}
				break;
			}
			
			case "define" : {
				break;
			}
			
			default : {
				if ($multiCategory) {
					$multiTable = "dt_" . $dtName . "_ref";
					$joins .= " JOIN {$multiTable} dtref ON dt.id = dtref.doc_id";
					$where .= " AND (dtref.cat_id = {$selectRef})";
				} else {
					$where .= " AND (dt.ref = {$selectRef})";
				}

				break;
			}
		}
		
		// Админский режим не может быть в не своей связи
		if ($selectRef != "own" && $selectRef != "owndeep") {
			$adminMode = false;
		}

		// Админский режим? Убираем случайную сортировку, ограничения лимитом, условия enabled = 1
		if ($adminMode) {
			if (!$alwaysLimit) {
				$limit = "";
			}
			
			$order = (strtolower(trim($order)) == "rand()") ? "" : $order;
			$enabledCheck = false; // Сделано при вызове метода _FormatDTQuery() в CreateXML()
		}

		// enabled condition
		if ($enabledCheck) {
			$where .= " AND (dt.enabled = 1)";
		}
		// Секция редактирования документов
		$editSectionName = $this->globalvars->GetStr("DocEditSectionName");

		// Есть права на создание новой секции? Добавляем ссылку на создание
		if ($rights["CreateEnabled"]) {
			$parentNode->setAttribute("createURL", $this->conf->Param("Prefix") . "{$editSectionName}/?qref={$this->thisID}&id=0&SID=" . $this->auth->GetSID());
			$parentNode->setAttribute("createDocType", $this->dtconf->dtn[$dtName]);
		}

		if ($selectRef == "owndeep" && ($docID = $this->_GetParam("id"))) {
			// Если запрошен конкретный документ из обычной сборки, показываем целиком его

			// Корректное ли принято число?
			if (!IsGoodNum($docID)) {
				return false;
			}

			// Устанавливаем атрибут в XML - идёт отображение имеено документа
			$parentNode->setAttribute("documentID", $docID);

			// Выборка всех полей ТД, независимо от ограничений
			$dtFields = "*";

			// Страницы при выборе конкретного документа не рассматриваем
			$perPage = 0;

			// Модифицируем условие выборки
			$where .= " AND (dt.id = {$docID})";
			
			if (!$showInOwnXSL && isset($this->dtconf->dtt[$dtName]) && $this->dtconf->dtt[$dtName]) {
				$xslList[] = array(
					"filename" => $this->dtconf->dtt[$dtName], 
					"match" => "document[@docTypeName = '{$dtName}']"
				);
			}
		} else if ($selectRef == "own" || $selectRef == "owndeep" || IsGoodNum($selectRef)) {
			// Move straight to the document 
			if ($directlyDeep and $selectRef == "owndeep" and !$adminMode) {
				$docCount = $this->db->GetValue("SELECT COUNT(*) AS cnt FROM dt_{$dtName} WHERE ref = {$this->thisID} AND enabled = 1");
				
				if (1 == $docCount && !isset($_GET["r{$this->thisID}_page"])) {
					$docID = $this->db->GetValue("SELECT id FROM dt_{$dtName} WHERE ref = {$this->thisID} AND enabled = 1");
					
					if ($this->conf->Param("StaticURL")) {
						$URL = $this->conf->Param("Prefix") . $this->SCName . "/r" . $this->thisID . "_id/" . $docID . "/";
					} else {
						$URL = $this->conf->Param("Prefix") . $this->SCName . "/?r" . $this->thisID . "_id=" . $docID;
					}
									
					if (!$this->_GetParam("id")) {
						$this->redirectPath = $URL;
					}
				}
			}
		} else if ($selectRef == "define") {
			// Выборка документов (группа), к выборке добавляется ссылка на целевую секцию/сборку
			$pref = $this->db->quote($this->conf->Param("Prefix"));
			
			$fields = array(
				'dt.ref AS target_ref_id', 
				'sys_sections.name AS target_section_name',
			);
			
			if ($this->conf->Param("StaticURL")) {
				$fields[] = "CONCAT('{$pref}', sys_sections.name, '/r', dt.ref, '_id/', dt.id, '/') AS realURL";
			} else {
				$fields[] = "CONCAT('{$pref}', sys_sections.name, '/?r', dt.ref, '_id=', dt.id) AS realURL"; 
			}
			
			$auxFields = $auxFields . ', ' . implode(', ', $fields);
			
			$joins = "LEFT JOIN 
						sys_references ON dt.ref = sys_references.id
					  LEFT JOIN 
					  	sys_sections ON sys_references.ref = sys_sections.id " 
				. $joins;
		}
					
		// Если используется свой шаблон
		if ($showInOwnXSL && isset($this->dtconf->dtt[$dtName]) && $this->dtconf->dtt[$dtName]) {
			$xslList[] = array(
				"filename" => $this->dtconf->dtt[$dtName], 
				"match" => "document[@docTypeName = '{$dtName}']"
			);
		}
		
		return $this->dt->FormatSelectQuery(
			$dtName, $xml, $parentNode, $dtFields, $auxFields, 
			$joins, $where, $order, $limit, 
			$perPage, $this->thisID, $stdFields, $disableDTPrefix, $distinct
		);
	}	

}

?>