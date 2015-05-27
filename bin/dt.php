<?php

require_once(CMSPATH_LIB . "doc/doccommon.php");
require_once(CMSPATH_LIB . "doc/paging.php");

/**
 * Класс, отвечающий за работу с ТД (типами документов)
 * @author IDM
 */
class DTClass {

	/**
	 * Главный класс конфигурации
	 * @var GlobalConfClass
	 */
	public $conf;
	/**
	 * Определение ТД
	 * @var DTConfClass
	 */
	public $dtconf;
	/**
	 * Работа с базой данных
	 * @var DBClass
	 */
	public $db;
	/**
	 * Глобальные переменные
	 * @var GlobalVarsClass
	 */
	public $globalvars;
	/**
	 * Авторизация пользователя
	 * @var AuthClass
	 */
	public $auth;
	/**
	 * Обработка ошибок
	 * @var ErrorClass
	 */
	private $error;
	
	public static function GetInstance() {
		static $instance;
		if (!is_object($instance)) {
			$instance = new DTClass();
		}
		
		return $instance;
	}
	
	public function __construct() {
		$this->db = DBClass::GetInstance();
		$this->conf = GlobalConfClass::GetInstance();
		$this->globalvars = GlobalVarsClass::GetInstance();
		$this->dtconf = DTConfClass::GetInstance();
		$this->auth = AuthClass::GetInstance();
		$this->error = ErrorClass::GetInstance();		
	}
	
	/**
	 * Возвращает список полей в XML (для записи и редактирования)
	 *
	 * @param object $xml
	 * @param object $rootNode
	 * @param string $dt
	 * @param bool $showHidden
	 * 
	 * @return DOMElement
	 **/
	public function GetFieldList($xml, $rootNode, $dt, $showHidden = true) {
		require_once(CMSPATH_LIB . "dt/fieldfactory.php");
		$newNode = $xml->createElement("doctype");
		$newNode->setAttribute("name", $dt);
		$newNode->setAttribute("title", $this->dtconf->dtn[$dt]);
		$newNode->setAttribute("enabledIsHidden", (isset($this->dtconf->dteh[$dt]) and $this->dtconf->dteh[$dt]) ? "1" : "0");
		
		foreach ($this->dtconf->dtf[$dt] as $xName => $xValue) {
			if (!$showHidden && isset($xValue["hide"]) && $xValue["hide"] == true) {
				continue;
			}
			
			if (isset($xValue["isth"]) && $xValue["isth"] !== false && isset($this->dtconf->dtf[$dt][$xValue["isth"]])) {
				continue;
			}
			
			if (isset($xValue["file"]) && $xValue["file"] !== false && isset($this->dtconf->dtf[$dt][$xValue["file"]])) {
				continue;
			}
						
			$type = $this->dtconf->dtf[$dt][$xName]["type"];
			$fieldCreator = FieldFactory::MakeFTClass($xml, $this, $type, $this->error);
			$field = $fieldCreator->MakeFieldForDocType($xName, $dt, $showHidden);
			$newNode->appendChild($field);
		}
		$rootNode->appendChild($newNode);

		$this->_CreateLinkNode($xml, $rootNode, 0, $dt, true);

		return $newNode;
	}


	/**
	 * Debug-интерфейс для _FormatSelectQueryRaw(). Может использоваться для того, чтобы получить сгенерированный SQL-запрос
	 */
	function FormatSelectQueryDebug($dtName, $xml, $parentNode, $dtFields = "*-", $auxFields = "", $joins = "", $where = "", $order = "", $limit = "", $perPage = 0, $refID = 0, $stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime", $disableDTPrefix = false) {
		$query = $this->_FormatSelectQueryRaw($dtName, $xml, $parentNode, $dtFields, $auxFields, $joins, $where, $order, $limit, $perPage, $refID, $stdFields, $disableDTPrefix);
		
		die($query);
	}

	/**
	 * Нормальный интерфейс для _FormatSelectQueryRaw() (используется в модулях чтения)
	 *
	 * @param unknown_type $dtName
	 * @param unknown_type $xml
	 * @param unknown_type $parentNode
	 * @param unknown_type $dtFields
	 * @param unknown_type $auxFields
	 * @param unknown_type $joins
	 * @param unknown_type $where
	 * @param unknown_type $order
	 * @param unknown_type $limit
	 * @param unknown_type $perPage
	 * @param unknown_type $refID
	 * @param unknown_type $stdFields
	 * @param unknown_type $disableDTPrefix
	 * 
	 * @return mixed
	 */
	public function FormatSelectQuery(
		$dtName, $xml, $parentNode, $dtFields = "*-", $auxFields = "", $joins = "", 
		$where = "", $order = "", $limit = "", $perPage = 0, 
		$refID = 0, 
		$stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime", 
		$disableDTPrefix = false, 
		$distinct = false
	) {
		$query = $this->_FormatSelectQueryRaw(
			$dtName, $xml, $parentNode, $dtFields, $auxFields, $joins, 
			$where, $order, $limit, $perPage, $refID, $stdFields, 
			$disableDTPrefix, $distinct
		);
		
		return ($query) 
			? $this->db->SQL($query) 
			: null;
	}


	/**
	 * Формирует SQL-запрос для выборки документов на основе информации о типе документа
	 *
	 * @param string $dtName
	 *		тип документа, на основании которого формируется запрос
	 * @param object $xml
	 *		ссылка на xml-дерево
	 * @param object $parentNode
	 *		родительская нода
	 * @param string $dtFields
	 *		список полей (через запятую, можно с пробелом), которые надо
	 *		выбрать из базы. Если поле сложное (требует привязки какой-то другой таблицы,
	 *		помимо dt_xxxxxx), к запросу автоматически делается LEFT JOIN. При этом
	 *		присоединяемая таблица имеет имя tbl_{$dtField}, где $dtField - название поля
	 *		ТД. Дополнительные поля, которые выбираются из присоединяемой таблицы,
	 *		именуются как join_{$dtField}_xxxxxx. Конкретно, чему равен xxxxxx для каждого
	 *		сложного типа поля, можно посмотреть ниже в функции _AddField().
	 *		Таким образом, если нам надо провести сортировку по размеру файла, который
	 *		хранится в поле fff типа "Файл", в параметр $order мы пишем "join_fff_size ASC"
	 *		(или DESC). Таблица данного типа документа всегда называется dt. Если
	 *		сортируем по строке sss, это будет выглядеть так: "dt.sss" (что то же самое,
	 *		что и "dt.sss ASC") или "dt.sss DESC".
	 *		"*" - выбираем все поля ТД без исключения.
	 *		"*-" - выбираем все поля ТД, которые не требуют для отображения дополнительных
	 *		подзапросов (например, массивы - требуют, их не выбираем). Если возможно,
	 *		рекомендуется использовать этот режим вместо "*".
	 * @param string $auxFields
	 *		дополнительные поля, которые мы хотим выбрать из таблицы. Может
	 *		пригодиться, например, в том случае, если мы делаем сами ещё какие-то JOIN-ы,
	 *		и из присоединённых таблиц нам надо получить данные. Перечисление - через
	 *		запятую (можно с пробелом).
	 * @param string $joins
	 *		дополнительные JOIN-ы в полном формате. 
	 * 		Например, "LEFT JOIN mytable ON mytable.id = dt.id"
	 * @param string $where
	 *		дополнительное условие выборки, без слова WHERE. Пример:
	 *		"dt.id = 123" или "dt.sss <> 'test'"
	 * @param string $order
	 * 		порядок выборки. Пример: "dt.id ASC" (то же самое, что "dt.id")
	 * @param string $limit
	 * 		ограничение выборки по количеству. Правила записи - те же, что в
	 *		поле LIMIT SQL-запроса. Пример: "10" (выбрать всего 10 документов),
	 *		"100, 10" (выбрать 10 документов, начиная со 101-го).
	 * @param string $perPage
	 * 		сколько документов отображаем на странице (если 0 - все).
	 * @param int $refID
	 *		ID секции, для которой идёт выборка (используется для вывода страниц
	 *		(ноды <pages>) в XML, в частности, для формирования ссылки)
	 * @param string $stdFields
	 * 		"dt.id, dt.enabled, dt.addtime, dt.chtime" - поля, которые выбираются
	 * 		по умолчанию (их не нужно указывать в $dtFields или $auxFields)
	 * @param bool $disableDTPrefix
	 * 		Если true, то таблица, из которой надо сделать выборку,
	 *		не содержит префикса "dt_", и её имя в точности совпадает с именем ТД ($dtName).
	 *		По умолчанию же выборка идёт из таблицы "dt_{$dtName}"
	 * @return mixed
	 **/
	private function _FormatSelectQueryRaw(
		$dtName, $xml, $parentNode, $dtFields = "*-", $auxFields = "", $joins = "",
		$where = "", $order = "", $limit = "", $perPage = 0, $refID = 0, 
		$stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime", 
		$disableDTPrefix = false, $distinct = false
	) {
		
		$distinct = ($distinct) ? " DISTINCT " : "";
		$allFields = array(); // Все поля, добавляемые в SELECT, - массив
		$dtJoins = ""; // DT Joins - JOINs for doctype fields
		if (trim($dtFields) == "*") {
			// Запрошена выборка всех полей
			if (isset($this->dtconf->dtf[$dtName])) {
				foreach ($this->dtconf->dtf[$dtName] as $idx => $val) {
					$this->_AddField($dtName, $idx, $allFields, $dtJoins);
				}
			}
		} elseif (trim($dtFields) == "*-") {
			// Запрошена выборка всех полей
			if (isset($this->dtconf->dtf[$dtName])) {
				foreach ($this->dtconf->dtf[$dtName] as $idx => $val) {
					if ($val["type"] == "array") {
						continue;
					}
					$this->_AddField($dtName, $idx, $allFields, $dtJoins);
				}
			}
		} else {
			// В $dtFields явно перечислены поля, которые должны быть селектированы
			$fields = explode(",", $dtFields);
			foreach ($fields as $val) {
				$val = trim($val);
				if ($val == "") {
					continue;
				}
				$this->_AddField($dtName, $val, $allFields, $dtJoins);
			}
		}
						
		// SELECT - WHAT? Окончательно собираем список селектируемых полей в $fieldList
		$fields = explode(",", $stdFields . ", " . $auxFields);
		foreach ($fields as $val) {
			$val = trim($val);
			if (!$val) {
				continue;
			}
			
			$allFields[] = $val;
		}

		$fieldList = implode(", ", $allFields);
		// Собираем поле FROM
		$from = " FROM " . DocCommonClass::GetTableName($dtName, $disableDTPrefix) . " dt ";
		// Собираем поле JOIN, добавляем пользовательские JOIN-ы к собранным функцией
		$joins = ($dtJoins . " " . $joins);
		
		// Собираем поле WHERE
		$where = (trim($where) != "") ? " WHERE " . $where : "";
		
		// Собираем поле ORDER
		$order = (trim($order) != "") ? " ORDER BY " . $order : "";
		
		// Постраничная разбивка
		if (IsGoodNum($perPage) && $perPage) {
			// Количество строк в БД
			$limitCnt = (trim($limit)) ? (" LIMIT " . $limit) : "";
			
			$count = $this->db->GetValue("SELECT COUNT(*) AS cnt {$from} {$joins} {$where} {$limitCnt}");

			// Если количество документов больше нуля
			if ($count) {
				// Запрашиваемое количество строк (в $limit)
				if (preg_match("~^([0-9]+),\s*([0-9]+)$~", trim($limit), $matches)) {
					$lim = min($matches[2], $count);
				} else if (IsGoodNum(trim($limit))) {
					$lim = min($limit, $count);
				} else {
					$lim = $count;
				}
				
				// Класс разбивки по страницам
				$pagingClass = new PagingClass();
				// Текущая страница - на основе адресной строки
				$currentPage = (isset($_GET["r{$refID}_page"]) && IsGoodNum($_GET["r{$refID}_page"])) 
					? $_GET["r{$refID}_page"] 
					: 1;
					
				// Выводим в XML страницы
				$ret = $pagingClass->Pages2XML($xml, $parentNode, $lim, $currentPage, $perPage, "", "r{$refID}_page", $limit);
				if (!$ret) {
					return false;
				}
			}
		}

		// Собираем поле LIMIT
		$limit = (trim($limit)) ? (" LIMIT " . $limit) : "";
				
		// Общий запрос, возвращаемый return-ом в конце
		return "SELECT {$distinct} {$fieldList} {$from} {$joins} {$where} {$order} {$limit}";
	}

	/**
	 * Вызывается из FormatSelectQuery()
	 *
	 * @param string $dtName
	 * @param string $dtField
	 * @param array $allFields
	 * @param array $allJoins
	 * @param string $type
	 **/
	private function _AddField($dtName, $dtField, &$allFields, &$allJoins, $type = false) {
		if (!isset($this->dtconf->dtf[$dtName][$dtField]) && !$type) {
			return;
		}
		$type = (!$type) ? $this->dtconf->dtf[$dtName][$dtField]["type"] : $type;
		$allFields[] = "dt.{$dtField} AS {$dtField}";
		
		switch ($type) {
			case "file": {
				$allJoins .= " LEFT JOIN sys_dt_files tbl_{$dtField} ON dt.{$dtField} = tbl_{$dtField}.id ";
				$allFields[] = "tbl_{$dtField}.name AS join_{$dtField}_name";
				$allFields[] = "tbl_{$dtField}.ext AS join_{$dtField}_ext";
				$allFields[] = "tbl_{$dtField}.size AS join_{$dtField}_size";
				$allFields[] = "tbl_{$dtField}.mimetype AS join_{$dtField}_mimetype";
				$allFields[] = "tbl_{$dtField}.filename AS join_{$dtField}_filename";
				if ($this->conf->Param("CalculateDownloadCount")) {
					$allFields[] = "tbl_{$dtField}.download_count AS join_{$dtField}_download_count";
				}
				break;
			}
			case "image": {
				$allJoins .= " LEFT JOIN sys_dt_images tbl_{$dtField} ON dt.{$dtField} = tbl_{$dtField}.id ";
				$allFields[] = "tbl_{$dtField}.name AS join_{$dtField}_name";
				$allFields[] = "tbl_{$dtField}.ext AS join_{$dtField}_ext";
				$allFields[] = "tbl_{$dtField}.size AS join_{$dtField}_size";
				$allFields[] = "tbl_{$dtField}.mimetype AS join_{$dtField}_mimetype";
				$allFields[] = "tbl_{$dtField}.filename AS join_{$dtField}_filename";
				if ($this->conf->Param("CalculateDownloadCount")) {
					$allFields[] = "tbl_{$dtField}.download_count AS join_{$dtField}_download_count";
				}
				$allFields[] = "tbl_{$dtField}.width AS join_{$dtField}_width";
				$allFields[] = "tbl_{$dtField}.height AS join_{$dtField}_height";
				break;
			}
			case "select":
			case 'radio': {
				$allJoins .= " LEFT JOIN sys_dt_select tbl_{$dtField} ON dt.{$dtField} = tbl_{$dtField}.id ";
				$allFields[] = "tbl_{$dtField}.list_id AS join_{$dtField}_list_id";
				$allFields[] = "tbl_{$dtField}.list_title AS join_{$dtField}_list_title";
				$allFields[] = "tbl_{$dtField}.item_id AS join_{$dtField}_item_id";
				$allFields[] = "tbl_{$dtField}.item_name AS join_{$dtField}_item_name";
				$allFields[] = "tbl_{$dtField}.item_title AS join_{$dtField}_item_title";
				break;
			}
			case "strlist": {
				$allJoins .= " LEFT JOIN sys_dt_strlist tbl_{$dtField} ON dt.{$dtField} = tbl_{$dtField}.id ";
				$allFields[] = "tbl_{$dtField}.text AS join_{$dtField}_text";
				break;
			}
		}
	}

	/**
	 	Интерфейс к функции ProcessQueryResults() (см. ниже)
		1. Удалены два внутренних deprecated-параметра (чтобы глаза не мозолили).
		2. Параметр, который добавлен в конец ProcessQueryResults, перенесён в начало.
		3. Также на основе изучения написанного кода были переставлены другие параметры.
	
		Выдача информации в XML из SQL-запроса.
		$SQLResult - результат SQL-запроса
		$xml - XML, в который пишем выдачу
		$parentNode - куда мы её пишем
		$dtName - имя ТД
		$noSubSelectFields - список полей (через запятую, пробелы игнорируются), которые
		надо представить в простом варианте (без подзапросов). Например, для массива
		не выдаются дочерние ноды, но отображается количество элементов дочернего
		массива. 
		Если равно "*", все возможные элементы отображаются в таком простом виде.
		"*-", естественно, смысла не имеет, поэтому использоваться здесь не должно.
		$ref = 0 - id модуля, которому принадлежат данные документы. Используется при
		формировании editURL, deleteURL, URL.
		$canEdit = false - имеем ли мы право на редактирование документа (нужно ли назначать
		атрибут editURL)
		$canDelete = false - имеем ли мы право на удаление документа (нужно ли назначать
		атрибут deleteURL)
		$targetSection = "" - указывается целевая секция - секция, в которой лежит документ.
		Если не указана, идёт замена параметра в текущем адресе.
		$editMode = false - режим редактирования документа. Если включен, выдача в XML идёт
		немного по-другому. Например, для nl2br-текста в этом режиме <br /> заменяются
		обратно на переносы строк.
		$aux = null - aux-поля для уточнения выборки (под-выборок, и т. п.)
		$rowNodeName = "document" - название ноды
		$showURL = true - если false - значит, нам не нужна выдача в XML ссылок на документы
		(атрибут URL)
		$enabledCheck - необходима ли проверка dt.enabled = 1 при выборке из базы. Так как для
		основного документа эта выборка к вызову данной функции уже проведена, здесь это
		необходимо для наложения соотв. условий на выбираемые поддокументы (subdoc).
	 */
	public function Select2XML_V2(
		PDOStatement $SQLResult, 
		DOMDocument $xml, 
		DOMElement $parentNode, 
		$dtName, 
		$noSubSelectFields = "", 
		$ref = 0, 
		$canEdit = false, 
		$canDelete = false, 
		$targetSection = "", 
		$editMode = false, 
		$aux = null, 
		$rowNodeName = "document", 
		$showURL = true, 
		$enabledCheck = true
	) {
		$this->ProcessQueryResults(
			$SQLResult, 
			$xml, 
			$parentNode, 
			$dtName, 
			$canEdit, 
			$canDelete, 
			$ref, 
			$targetSection, 
			$showURL, 
			$aux, 
			$rowNodeName, 
			$editMode, 
			0, 
			"", 
			$noSubSelectFields, 
			$enabledCheck
		);
	}

	/**
		ВНИМАНИЕ!
		В данный момент данная функция оставлена только в целях совместимости и запрещена
		для внешнего вызова.
		Для тех же целей на основе исследования кода (вызовов ProcessQueryResults)
		написана более удобная функция Select2XML_V2 (см. выше).
		Помимо перестановки параметров туда был добавлен параметр $noSubSelectFields.
		====================================================================================
		Выдача информации в XML из SQL-запроса.
		$SQLResult - результат SQL-запроса
		$xml - XML, в который пишем выдачу
		&$parentNode - куда мы её пишем
		$dtName - имя ТД
		$canEdit = false - имеем ли мы право на редактирование документа (нужно ли назначать
		атрибут editURL)
		$canDelete = false - имеем ли мы право на удаление документа (нужно ли назначать
		атрибут deleteURL)
		$ref = 0 - id модуля, которому принадлежат данные документы. Используется при
		формировании editURL, deleteURL, URL.
		$targetSection = "" - указывается целевая секция - секция, в которой лежит документ.
		Если не указана, идёт замена параметра в текущем адресе.
		$showURL = true - если false - значит, нам не нужна выдача в XML ссылок на документы
		(атрибут URL)
		$aux = null - aux-поля для уточнения выборки (под-выборок, и т. п.)
		$rowNodeName = "document" - название ноды
		$editMode = false - режим редактирования документа. Если включен, выдача в XML идёт
		немного по-другому. Например, для nl2br-текста в этом режиме <br /> заменяются
		обратно на переносы строк.
		$noSubSelectFields - список полей (через запятую, пробелы игнорируются), которые
		надо представить в простом варианте (без подзапросов). Например, для массива
		не выдаются дочерние ноды, но отображается количество элементов дочернего
		массива. Если равно "*", все возможные элементы отображаются в таком простом виде.
		"*-", естественно, смысла не имеет, поэтому использоваться здесь не должно.
		$createLinkNode - true (по умолчанию)/false - разрешение создавать внутри документа
		ноду со связанными с ним документами
		$enabledCheck - необходима ли проверка dt.enabled = 1 при выборке из базы. Так как для
		основного документа эта выборка к вызову данной функции уже проведена, здесь это
		необходимо для наложения соотв. условий на выбираемые поддокументы (subdoc).
		Следующие два параметра назначаться извне не должны. Используются для рекурсивного
		вызова этой функции в случае выборки массивов.
		
		$parentDoc = 0
		$thisArrayName = ""
	 */
	public function ProcessQueryResults(
		PDOStatement $SQLResult, 
		DOMDocument $xml, 
		DOMElement $parentNode, 
		$dtName, 
		$canEdit = false, 
		$canDelete = false, 
		$ref = 0, 
		$targetSection = '', 
		$showURL = true, 
		$aux = null, 
		$rowNodeName = "document", 
		$editMode = false, 
		$parentDoc = 0, 
		$thisArrayName = '', 
		$noSubSelectFields = '', 
		$createLinkNode = true, 
		$enabledCheck = true
	) {
		if (!$SQLResult) {
			return false;
		}
		
		// $noSubSelectFields modification
		// This check is made for speed if $noSubSelectFields is empty or equal to "*"
		if ($noSubSelectFields && $noSubSelectFields != "*") {
			$nssfArray = explode(",", $noSubSelectFields);
			foreach ($nssfArray as $idx => $val) {
				$nssfArray[$idx] = trim($val);
			}
			
			$noSubSelectFields = implode(",", $nssfArray);
			$noSubSelectFields = "," . $noSubSelectFields . ",";
		}
		
		// User SID
		$sid = $this->auth->GetSID();
		
		// параметры, которые мы передаем в объект поля
		$params = array();
		$params["dtName"] = $dtName;
		$params["canEdit"] = $canEdit;
		$params["canDelete"] = $canDelete;
		$params["ref"] = $ref;
		$params["targetSection"] = $targetSection;
		$params["showURL"] = $showURL;
		$params["aux"] = $aux;
		$params["rowNodeName"] = $rowNodeName;
		$params["editMode"] = $editMode;
		$params["parentDoc"] = $parentDoc;
		$params["thisArrayName"] = $thisArrayName;
		$params["noSubSelectFields"] = $noSubSelectFields;
		$params["createLinkNode"] = $createLinkNode;
		$params["enabledCheck"] = $enabledCheck;
		
		while ($row = $SQLResult->fetch(PDO::FETCH_ASSOC)) {
			$newNode = $xml->createElement($rowNodeName);
			$this->_CreateDocumentAttributes($newNode, $row, $params, $sid);
						
			// создаем ноды со связанными документами
			if ($createLinkNode and isset($row["id"])) {
				$this->_CreateLinkNode($xml, $newNode, $row["id"], $dtName, $editMode);
			}

			// перебираем поля документа и создаем для них ноды
			$this->_CreateDocumentFields($xml, $newNode, $row, $params);

			/* Обработка дополнительного запроса */
			if ($aux) {
				foreach ($aux as $auxItem) {
					if ($auxItem["type"] == "function") {
						// Переданная функция должна быть объявлена как 
						call_user_func($auxItem["func"], $xml, $parentNode, $newNode, $row);
					} else if ($auxItem["type"] == "sql") {
						// выполняем пришедший в параметре PHP-код (SQL запрос)
						$code = "\$auxSQL = \"" . preg_replace('~"~', '\"', $auxItem["desc"]) . "\";";
						eval($code);
						
						if (isset($auxSQL)) {
							// выполняем SQL запрос
							$stmt = $this->db->SQL($auxSQL);
							// создаем XML узел для результатов запроса
							$auxXMLSQL = $xml->createElement("sql");
							$auxXMLSQL->setAttribute("name", $auxItem["name"]);
							
							while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$auxXMLRow = $xml->createElement("row");
								$this->rowToXml($xml, $auxXMLRow, $result);
								
								$auxXMLSQL->appendChild($auxXMLRow);
							}
							
							$newNode->appendChild($auxXMLSQL);
						}
					} else if ($auxItem["type"] == "calc") {
						// выполняем пришедший в параметре PHP-код
						$code = "\$generated = \"" . preg_replace('~"~', '\"', $auxItem["desc"]) . "\";";
						eval($code);
						eval($generated);
						
						if (isset($calcResult)) {
							// создаем XML узел для результатов запроса
							$auxXMLCalc = $xml->createElement("calc", $calcResult);
							$auxXMLCalc->setAttribute("name", $auxItem["name"]);
							
							$newNode->appendChild($auxXMLCalc);
						}
					} else if ($auxItem["type"] == "code") {
						eval($auxItem["desc"]);
					}
					
				}
			}
			
			$parentNode->appendChild($newNode);
		}
	}
	
	/**
	 * @param DOMElement $newNode
	 * @param array $row
	 * @param array $params
	 * @return void
	 */
	private function _CreateDocumentAttributes(DOMElement $newNode, array $row, array $params, $sid) {
		// id (attr.)
		if (isset($row["id"])) {
			$newNode->setAttribute("id", $row["id"]);
		}
		
		// enabled (attr.)
		if (isset($row["enabled"])) {
			$newNode->setAttribute("enabled", $row["enabled"]);
		}
		
		// docTypeName (attr.)
		$newNode->setAttribute("docTypeName", $params['dtName']);
		// docTypeTitle (attr.)
		if (isset($this->dtconf->dtn[$params['dtName']])) {
			$newNode->setAttribute("docTypeTitle", $this->dtconf->dtn[$params['dtName']]);
		}
		
		// xslt (attr.)
		if (isset($this->dtconf->dtt[$params['dtName']])) {
			$newNode->setAttribute("xslt", $this->dtconf->dtt[$params['dtName']]);
		}
		
		// addTime (attr.)
		if (isset($row["addtime"])) {
			$newNode->setAttribute("addTime", $row["addtime"]);
		}
		
		// changeTime (attr.)
		if (isset($row["chtime"])) {
			$newNode->setAttribute("changeTime", $row["chtime"]);
		}
		
		// targetRef (attr.)
		if (isset($row["target_ref_id"])) {
			$newNode->setAttribute("targetRef", $row["target_ref_id"]);
		}
		
		// targetSection (attr.)
		if (isset($row["target_section_name"])) {
			$newNode->setAttribute("targetSection", $row["target_section_name"]);
		}
		
		// URL (attr.)
		if (isset($row["realURL"])) {
			$newNode->setAttribute("URL", $row["realURL"]);
		} elseif ($params['showURL'] && isset($row["id"]) && $params['ref'] > 0) {
			if ($params['targetSection'] == '') {
				$paramName = "r" . $params['ref'] . "_id";
				$paramValue = $row["id"];
				
				$url = URLReplaceParam($paramName, $paramValue);
			} else {
				if ($this->conf->Param("StaticURL")) {
					$url = $this->conf->Param("Prefix")
						. $params['targetSection'] 
						. "/r" . $params['ref'] . "_id/" 
						. $row["id"] . "/";
				} else {
					$url = $this->conf->Param("Prefix")
						. $params['targetSection'] 
						. "/?r" . $params['ref'] . "_id=" 
						. $row["id"];
				}
			}
			
			$newNode->setAttribute("URL", $url);
		}
		
		$qref = (isset($row["target_ref_id"])) ? $row["target_ref_id"] : $params['ref'];
					
		// Edit URLs
		if ($params['canEdit']) {
			$editSectionName = $this->globalvars->GetStr("DocEditSectionName");
			
			$editUrl = $this->conf->Param("Prefix") 
				. "{$editSectionName}/?"
				. "qref={$qref}&SID={$sid}";

			if ($params['parentDoc'] != 0) {
				$editUrl .= "&id={$params['parentDoc']}" 
					. "&subname={$params['thisArrayName']}&subid={$row["id"]}";
					
			} else {
				$editUrl .= "&id={$row["id"]}";
			}	
			
			$newNode->setAttribute('editURL', $editUrl);
		}
		
		// Delete URLs
		if ($params['canDelete']) {
			$deleteUrl = $this->conf->Param("Prefix") 
				. "?writemodule=DocWriting" 
				. "&qref={$qref}&ref={$params['ref']}&delete=1";
				
			if ($params['parentDoc'] != 0) {
				$deleteUrl .= "&id={$params['parentDoc']}" 
					. "&subname={$params['thisArrayName']}&subid={$row["id"]}";	
			} else {
				$deleteUrl .= "&id={$row["id"]}";
			}

			$newNode->setAttribute('deleteURL', $deleteUrl);
		}
		
		
	}
			
	/**
	 * @param DOMDocument $xml
	 * @param DOMElement $documentNode
	 * @param array $sqlData
	 * @param array $params
	 */
	private function _CreateDocumentFields(
		DOMDocument $xml, DOMElement $documentNode, array $sqlData, array $params
	) {
		// включаем этот файл здесь, а не в потомках - сильно повышает производительность
		require_once(CMSPATH_LIB . 'dt/fieldfactory.php');
		
		foreach ($sqlData as $atName => $atValue) {
			if ($atName == "id") {
				continue;
			}
			
			if ($atName == "enabled") {
				continue;
			}
			
			if ($atName == "addtime") {
				continue;
			}
			
			if ($atName == "chtime") {
				continue;
			}
			
			if ($atName == "realURL") {
				continue;
			}
			
			if (substr($atName, 0, 5) == "join_") {
				continue;
			}
			
			if (substr($atName, 0, 7) == "target_") {
				continue;
			}

			$type = (isset($this->dtconf->dtf[$params['dtName']][$atName]['type'])) 
				? $this->dtconf->dtf[$params['dtName']][$atName]['type'] 
				: "aux";
			
			$fieldCreator = FieldFactory::MakeFTClass($xml, $this, $type, $this->error);
			$field = $fieldCreator->MakeFieldForDocument($sqlData, $atName, $params);

			if (!$field instanceof DOMElement) {
				continue;
			} 
			
			$documentNode->appendChild($field);
		}
	}

	/**
	 * Создание ноды со связанными документами
	 *
	 * @param object $xml
	 * @param object $documentNode
	 * @param int $docID
	 * @param string $dtName
	 * @return bool
	 */
	private function _CreateLinkNode($xml, $documentNode, $docID, $dtName, $editMode) {
		if (!isset($this->dtconf->dtl[$dtName])) {
			return true;
		}

		foreach ($this->dtconf->dtl[$dtName] as $linkName => $linkParams) {
			$linkedDT = $this->dtconf->dtl[$dtName][$linkName]["doct"];
			$linksTable = "link_" . $dtName . "_" . $linkedDT;
			
			if ($editMode) {
				$linkNode = X_CreateNode($xml, $documentNode, "link");
				$linkNode->setAttribute("description", $this->dtconf->dtl[$dtName][$linkName]["desc"]);
				$linkNode->setAttribute("docTypeName", $this->dtconf->dtl[$dtName][$linkName]["doct"]);
				$linkNode->setAttribute("targetDTTitle", $this->dtconf->dtl[$dtName][$linkName]["tdtt"]);
				$linkNode->setAttribute("bothDirections", $this->dtconf->dtl[$dtName][$linkName]["both"] ? 1 : 0);
				$linkNode->setAttribute(
					"URL", 
					$this->conf->Param("Prefix") . $this->dtconf->dtl[$dtName][$linkName]["doct"] . "/"
				);

				$titleField = $this->dtconf->dtl[$dtName][$linkName]["tdtt"];

				$linkedDTTable = DocCommonClass::GetTableName($linkedDT);
				
				$sqlResult = $this->db->SQL(
					"SELECT 
						dt.id, dt.{$titleField} AS title, 
						IF (ISNULL(lt.to_id), 0, 1) AS selected, 
						dt.ref AS target_ref_id, 
						sys_sections.name AS target_section_name,  
						CASE
							WHEN " . intval($this->conf->Param("StaticURL")) . " = 1 THEN CONCAT(?, sys_sections.name, '/r', dt.ref, '_id/', dt.id, '/') 
							ELSE CONCAT(?, sys_sections.name, '/?r', dt.ref, '_id=', dt.id) 
						END AS realURL
					FROM 
						{$linkedDTTable} dt 
					LEFT JOIN 
						{$linksTable} lt ON lt.to_id = dt.id AND lt.from_id = ?
					LEFT JOIN 
						sys_references ON dt.ref = sys_references.id
					LEFT JOIN 
						sys_sections ON sys_references.ref = sys_sections.id",
					array($this->conf->Param("Prefix"), $this->conf->Param("Prefix"), $docID)	
				);

				$this->ProcessQueryResults(
					$sqlResult, $xml, $linkNode, $linkedDT, false, false, 0, $linkedDT, true, null, "document", false, 0, "", "", false
				);
			} else {
				if (0 == $docID) {
					continue;
				}
				$docCount = $this->db->GetValue("SELECT COUNT(*) FROM {$linksTable} WHERE from_id = {$docID}");
				if (0 == $docCount) {
					continue;
				}
				
				$linkNode = X_CreateNode($xml, $documentNode, "link");
				$linkNode->setAttribute("description", $this->dtconf->dtl[$dtName][$linkName]["desc"]);
				$linkNode->setAttribute("docTypeName", $this->dtconf->dtl[$dtName][$linkName]["doct"]);
				$linkNode->setAttribute("targetDTTitle", $this->dtconf->dtl[$dtName][$linkName]["tdtt"]);
				$linkNode->setAttribute("bothDirections", $this->dtconf->dtl[$dtName][$linkName]["both"] ? 1 : 0);
				$linkNode->setAttribute("URL", $this->conf->Param("Prefix") . $this->dtconf->dtl[$dtName][$linkName]["doct"] . "/");
				$linkNode->setAttribute("documentCount", $docCount);

				$pref = $this->db->quote($this->conf->Param("Prefix"));
				$auxFields = "
					dt.ref AS target_ref_id, 
					sys_sections.name AS target_section_name, 
					CASE
						WHEN " . intval($this->conf->Param("StaticURL")) . " = 1 THEN CONCAT('{$pref}', sys_sections.name, '/r', dt.ref, '_id/', dt.id, '/') 
						ELSE CONCAT('{$pref}', sys_sections.name, '/?r', dt.ref, '_id=', dt.id) 
					END AS realURL
				";
				$joins = "
					JOIN 
						{$linksTable} lt ON dt.id = lt.to_id
					LEFT JOIN 
						sys_references ON dt.ref = sys_references.id
					LEFT JOIN 
						sys_sections ON sys_references.ref = sys_sections.id
				";
				$where = "lt.from_id = {$docID}";
				$sqlResult = $this->FormatSelectQuery($linkedDT, $xml, $linkNode, "*-", $auxFields, $joins, $where);

				$this->ProcessQueryResults(
					$sqlResult,	$xml, $linkNode, $linkedDT, 
					false, false, 0, $linkedDT, true, 
					null, "document", false, 0, "", "", false
				);
			}

		}
		return true;
	}
		
	private function rowToXml(
		DOMDocument $xml, DOMElement $parentNode, array &$sqlArray, $nodeName = "field"
	) {
		foreach ($sqlArray as $name => $value) {
			$auxField = $xml->createElement($nodeName, $value);
			$auxField->setAttribute("name", $name);
						
			$parentNode->appendChild($auxField);
		}
	}
	
}

?>