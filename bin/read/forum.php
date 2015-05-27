<?php

/**
 * Модуль чтения для форума
 * @author IDM
 */

require_once(CMSPATH_MOD_READ . "advdoc.php");

class ForumReadClass extends AdvDocReadClass {

	function CreateXML() {

		// Обязательные параметры
		$inDTName = "forum"; // Тип документа (важно, чтобы знать, из какой таблицы пойдёт запрос)

		// Необязательные параметры
		$inDTFields = "*"; // Список полей ТД. Поля обязаны присутствовать в объявлении ТД.
		$inAuxFields = ""; // Дополнительные поля для селекта из БД
		$inJoins = ""; // Дополнительные JOIN-ы
		$inWhere = ""; // WHERE-часть запроса (без слова WHERE)
		$inOrder = "dt.chtime DESC"; // ORDER-часть запроса (без слова ORDER)
		$inLimit = ""; // LIMIT-часть запроса (без слова LIMIT)
		$inPerPage = 30; // Число документов на страницу. Ноль означает отсуствие постраничной разбивки
		$inSelectRef = "owndeep"; // Тип запроса по модулю выборки
		
		$inShowInOwnXSL = false; // Отображение документов в их собственном XSL-правиле
		$inEnabledCheck = true; // Выполнять ли проверку enabled = 1 для обычных пользователей (AdminMode - всегда проверка отключена)
		$inStdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime"; // Список стандартных полей ТД для выборки в XML
		$inDisableDTPrefix = false; // Из какой таблицы пойдёт запрос.
		/*
		$inDisableDTPrefix:
		Если true - в начало названия ТД не подставляется префикс "dt_", чтобы получить название таблицы
		*/
		$inShowDocType = true; // Выдавать в XML описание ТД (будет в выдаче над данными)

		// Выбираем параметры из БД
		eval($this->params);

		// Добавляем список полей документа, если надо
		if ($inShowDocType) {
			$this->dt->GetFieldList($this->xml, $this->parentNode, $inDTName, false);
		}

		// Проверяем целевую секцию и выводим информацию о ней в XML
		$rv = $this->_CheckTargetSection($inSelectRef, $targetSectionName);
		if (!$rv) return false;

		// Формируем запрос
		$sql = $this->_FormatDTQuery($inDTName, $inDTFields, $inAuxFields, $inJoins, $inWhere, $inOrder, $inLimit,
		$inPerPage, $inSelectRef, $inShowInOwnXSL, $inEnabledCheck, $inStdFields, $inDisableDTPrefix);

		if (!$sql) return false;
		
		// Формируем дополнительные параметры выдачи сообщения в XML
		$aux = array();
		$aux[0]["type"] = "code";
		$aux[0]["desc"] = "
			\$sql = \$this->FormatSelectQuery('user', \$xml, \$newNode, '*-', '', '', \"dt.id = {\$row['user_id']}\");
			\$this->ProcessQueryResults(\$sql, \$xml, \$newNode, 'user', false, false, 0, '', false, null, 'user');
		";
		
		$aux[1]["type"] = "code";
		$aux[1]["desc"] = "
			\$userTable = \$this->db->GetValue(\"SELECT r.name FROM sys_roles r JOIN dt_user u ON u.role_id = r.id WHERE u.id = {\$row['user_id']}\");
			if(\$userTable) {
			\$userTable = 'user_'.\$userTable;
			\$sql = \$this->FormatSelectQuery(\$userTable, \$xml, \$newNode, '*-', '', '', \"dt.id = {\$row['user_id']}\");
			\$this->ProcessQueryResults(\$sql, \$xml, \$newNode, \$userTable, false, false, 0, '', false, null, 'userdata');
			}
		";	

		
		// Делаем выборку в XML на базе сформированного запроса
		$this->dt->Select2XML_V2(
		$sql, $this->xml, $this->parentNode, $inDTName, $this->_GetParam("id") ? "" : "*", 
		(!IsGoodNum($inSelectRef)) ? $this->thisID : $inSelectRef, $this->rights["Edit"], $this->rights["Delete"],
		$targetSectionName, false, $aux
		);
		
		// Добавляем ноду со вчерашней датой
		$yesterday = $this->xml->createElement("yesterday");
		$txt = $this->xml->createTextNode(date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))));
		$yesterday->appendChild($txt);
		$this->parentNode->appendChild($yesterday);
		
		return true;
	}
	
}

?>