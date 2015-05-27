<?

class SearchCacheClass {

	var $srchStr;
	var $hash;
	var $insertedRows = 0;
	
	var $numrows;
	
	/**
	 * @var DTConfClass
	 */
	var $dtconf;
	/**
	 * @var DBClass
	 */
	var $db;

	public function __construct($searchString) {
		$this->dtconf = DTConfClass::GetInstance();
		$this->db = DBClass::GetInstance();
		
		$this->srchStr = $searchString;

		// подготовка поисковой строки для выполнения запроса
		$this->_PrepareSearchString();
		$this->hash = md5(mb_strtolower($this->srchStr));
		
		$this->_SetNumRows();
		
		if (0 == $this->numrows) {
			$this->_CacheSearchResults();
		}
		
	}
	
	public function GetHash() {
		return $this->hash;
	}
	
	public function GetNumRows() {
		return $this->numrows;
	}
	
	public function GetNumRowsWithCondition($condition) {
		return $this->db->GetValue(
			"SELECT 
				COUNT(*) AS cnt 
			FROM 
				sys_search_cache 
			WHERE 
				hash = '{$this->hash}' 
				AND url != 'XXX' 
				AND " 
			. $condition
		);
	}
	
	private function _SetNumRows() {
		$this->numrows = (int)$this->db->GetValue(
			"SELECT COUNT(*) AS cnt FROM sys_search_cache WHERE hash = '{$this->hash}' AND url != 'XXX'"
		);
	}
	
	/**
	 * Кэшируем результаты поиска
	 */
	private function _CacheSearchResults() {
		foreach ($this->dtconf->dtf as $dtName => $dtFields) {
			$this->_CacheDT($dtName);
		}
		
		$this->_CacheSearchInSections();

		if (0 == $this->insertedRows) {
			$this->_WriteNotFoundToCache();
		}

		$this->numrows = (int)$this->insertedRows;
	}


	/**
	 * Кэширование ссылок на секции, чьи названия попадают под поисковый запрос
	 */
	private function _CacheSearchInSections() {
		$stmt = $this->db->SQL("
			INSERT INTO sys_search_cache
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
	 * @param string $dtName
	 */
	private function _CacheDT($dtName) {
		// Если у документа нет такого поля - то дальше даже не смотрим (для текста исключения)
		if ("text" != $dtName && !isset($this->dtconf->dtf[$dtName]["title"])) {
			return;
		}

		// поля типа документа, по которым идет поиск
		$strFields = $this->_GetSeachableFieldsInString($dtName);
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
	 * Добавляем в кэш, что по данному запросу в базе ничего нет
	 */
	private function _WriteNotFoundToCache() {
		$this->db->SQL("
			INSERT INTO 
				sys_search_cache 
			VALUES 
				('{$this->hash}', 0, '', 0, '', '', '0000-00-00 00:00:00', 'XXX', NOW(), '')
		");
	}

	/**
	 * Список полей, которые входят в FULLTEXT INDEX
	 *
	 * @param string $dtName
	 */
	private function _GetSeachableFieldsInString($dtName) {
		$extra = array();
		
		foreach ($this->dtconf->dtf[$dtName] as $fName => $fAttrs) {
			if (isset($fAttrs["srch"]) && ($fAttrs["srch"] == true)) {
				$extra[] = "dt." . $fName;
			}
		}
		
		return implode(", ", $extra);
	}

	/**
	 * Подготавливаем поисковую строку для запроса
	 */
	private function _PrepareSearchString() {
		// Удаляем из запроса все символы, которым там быть не положено
		$this->srchStr = preg_replace(
			"~[\!\@\#\$\%\^\&\*\(\)\-\=\+\|\{\}\:\;\?\/\\\"\<\>\.\,\~]~", 
			' ', 
			$this->srchStr
		);
		
		// Добавляем после каждого слова "*" (см. документацию по FULLTEXT SEARCH)
		$words = preg_split("/[\s]/", $this->srchStr, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($words as $idx => $val) {
			$words[$idx] .= "*";
		}
		
		// Получаем новую ("правильную") строку запроса
		$this->srchStr = implode(" ", $words);
	}

}

?>