<?php
require_once(CMSPATH_LIB . "doc/paging.php");
/**
 * Поиск
 * @author IDM
 */
class SearchReadClass extends ReadModuleBaseClass {

	private $srchStr;
	private $hash;
	private $cachedRows = 0;

	private $insertedRows = 0;
	
	public function CreateXML() {
		// Строка поиска
		$this->srchStr = $this->_GetSimpleParam("s");
		// Если строка поиска не задана
		if (!$this->srchStr) return true;

		// подготовка поисковой строки для выполнения запроса
		$this->PrepareSearchString();

		// Хэш запроса
		$this->hash = md5(mb_strtolower($this->srchStr));

		// Количество документов в кэше
		$this->cachedRows = $this->GetCachedNumRows();

		// Если для данного запроса нет кэша результатов, то сначало создаем этот кэш
		if (0 == $this->cachedRows) {
			$this->CacheSearchResults();
		}

		// выводим результаты
		return ($this->ShowResultsFromCache()) ? true : false;
	}

	/**
	 * Добавляем в кэш, что по данному запросу в базе ничего нет
	 */
	function WriteNotFoundToCache() {
		$this->db->SQL("
			INSERT INTO sys_search_cache VALUES
			('{$this->hash}', 0, '', 0, '', '', '0000-00-00 00:00:00', 'XXX', NOW(), '')
		");
	}

	/**
	 * Количество кэшированных записей
	 *
	 * @return int
	 */
	function GetCachedNumRows() {
		return $this->db->GetValue("SELECT COUNT(*) AS cnt FROM sys_search_cache WHERE hash = '{$this->hash}' AND url != 'XXX'");
	}
	
	/**
	 * Кэшируем результаты поиска
	 */
	function CacheSearchResults() {
		foreach ($this->dtconf->dtf as $dtName => $dtFields) {
			$this->CacheDT($dtName);
		}
		$this->CacheSearchInSections();

		if (0 == $this->insertedRows) {
			$this->WriteNotFoundToCache();
		}

		$this->cachedRows = $this->insertedRows;
	}

	/**
	 * Кэширование ссылок на секции, чьи названия попадают под поисковый запрос
	 */
	function CacheSearchInSections() {
		$stmt = $this->db->SQL("
			INSERT INTO 
				sys_search_cache
			SELECT 
				'{$this->hash}' AS hash,
				MATCH (title) AGAINST ('{$this->srchStr}' IN BOOLEAN MODE) AS rank,
				s.title AS sec_title,
				s.id AS sec_id,
				'' AS doc_title,
				'' AS doc_type,
				'0000-00-00 00:00:00' AS doc_addtime,
				CONCAT(s.name, '/') AS url,
				NOW() AS addtime,
				s.path AS sec_path
			FROM 
				sys_sections s
			WHERE 
				MATCH (title) AGAINST ('{$this->srchStr}' IN BOOLEAN MODE) 
				AND s.enabled = 1 
				AND s.auth = 'no'
    	");
		$this->insertedRows += $stmt->rowCount();
	}

	/**
	 * Кэширование ссылок на документы, в которых встречается поисковое слово
	 *
	 * @param string $dtName
	 */
	function CacheDT($dtName) {
		// Если у документа нет такого поля - то дальше даже не смотрим (для текста исключения)
		if ("text" != $dtName && !isset($this->dtconf->dtf[$dtName]["title"])) {
			return;
		}

		$strFields = $this->GetSeachableFieldsInString($dtName);
		// поля типа документа, по которым идет поиск
		if (!$strFields) {
			return;
		}

		/**
		 * Для ТД "Текст" у нас исключение - ссылка сразу ведет на документ
		 * @todo учесть staticUrls
		 */
		$url = ("text" == $dtName) 
			? "CONCAT(s.name, '/')" 
			: "CONCAT(s.name, '/?r', dt.ref, '_id=', dt.id)";
		$docTitle = ("text" == $dtName) ? "''" : "dt.title";

		$stmt = $this->db->SQL("
			INSERT INTO sys_search_cache
			SELECT 
				'{$this->hash}' AS hash,
				MATCH ({$strFields}) AGAINST ('{$this->srchStr}' IN BOOLEAN MODE) AS rank,
				s.title AS sec_title,
				s.id AS sec_id,
				{$docTitle} AS doc_title,
				'{$dtName}' AS doc_type,
				dt.addtime AS doc_addtime,
				{$url} AS url,
				NOW() AS addtime, s.path AS sec_path
			FROM 
				dt_{$dtName} dt
				INNER JOIN sys_references r ON r.id = dt.ref
				INNER JOIN sys_sections s ON s.id = r.ref
			WHERE 
				MATCH ({$strFields}) AGAINST ('{$this->srchStr}' IN BOOLEAN MODE) 
				AND dt.enabled = 1 
				AND r.enabled = 1 
				AND s.enabled = 1
        ");	

		$this->insertedRows += $stmt->rowCount();
	}

	/**
	 * Генерация результатов в xml
	 */
	function ShowResultsFromCache() {
		$inPerPage = 15;
		eval($this->params);

		$page = $this->_GetParam("page");
		
		// Разбивка по страницам
		$pagingClass = new PagingClass();
		$result = $pagingClass->Pages2XML(
			$this->xml, 
			$this->parentNode, 
			$this->cachedRows, 
			($page) ? $page : 1, 
			$inPerPage, 
			$this->query, 
			"r" . $this->thisID . "_page", 
			$limit
		);
		if (!$result) {
			return false;
		}
		
		// Выборка из кэша
		$stmt = $this->db->SQL("
			SELECT 
				* 
			FROM 
				sys_search_cache 
			WHERE 
				hash = '{$this->hash}' AND url != 'XXX' 
			ORDER BY 
				rank DESC, doc_addtime DESC 
			LIMIT 
				{$limit}
		");
		
		// Если ничего не найдено (например, все найденные документы
		// запрещены для просмотра данным пользователем, или строка в
		// кэше содержит URL "XXX" (флаг, что ничего не найдено),
		// пишем, что ничего не найдено
		if ($stmt->rowCount() < 1) {
			$this->_WriteSearchResultsInfo("NoResult");
		} else {
			// Всё ОК, выдаём пользователю результаты
			$this->dt->Select2XML_V2($stmt, $this->xml, $this->parentNode, "blank", "", 0, false, false, "", false, null, "row");
			$this->_WriteSearchResultsInfo("SearchCompleted");
		}
		
		return true;
	}

	/**
	 * Создаёт в XML ноду с нужной нам информацией.
	 *
	 * @todo Когда это писалось, _WriteInfo() и _WriteError() ещё не было :)
	 * @param string $info
	 */
	function _WriteSearchResultsInfo($info) {
		X_CreateNode($this->xml, $this->parentNode, "search", $info);
	}

	/**
	 * Подготавливаем поисковую строку для запроса
	 */
	function PrepareSearchString() {
		// Удаляем из запроса все символы, которым там быть не положено
		$this->srchStr = preg_replace("~[\!\@\#\$\%\^\&\*\(\)\-\=\+\|\{\}\:\;\?\/\\\"\<\>\.\,\~]~", " ", $this->srchStr);
		
		// Добавляем после каждого слова "*" (см. документацию по FULLTEXT SEARCH)
		$words = preg_split("~[\s]~", $this->srchStr, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($words as $idx => $val) {
			$words[$idx] .= "*";
		}
		
		// Получаем новую ("правильную") строку запроса
		$this->srchStr = $this->db->quote(implode(" ", $words));
	}

	/**
	 * Список полей, которые входят в FULLTEXT INDEX
	 *
	 * @param string $dtName
	 */
	function GetSeachableFieldsInString($dtName) {
		$extra = array();
		
		foreach ($this->dtconf->dtf[$dtName] as $fName => $fAttrs) {
			if (isset($fAttrs["srch"]) and ($fAttrs["srch"] == true)) {
				$extra[] = "dt." . $fName;
			}
		}
		
		return $this->db->quote(implode(", ", $extra));
	}
}

?>