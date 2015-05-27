<?php
/**
 * Класс, генерирующий xml-ноды с информацией о страницах
 *
 * @todo читать в Pages2XMLExt(), а лучше смотри как сделано в finolymp/plib/archivepaging.php
 */
class PagingClass {

	var $xml;
	var $parentNode;

	var $pagesNode;

	var $docCount;
	var $currentPage;
	var $perPage;
	var $queryBase;
	var $URLParamName;
	var $limitStr;

	var $pageCount;
	var $nextStr;
	var	$prevStr;

	public function __construct() {}

	/**
	 * Создание ноды xml с страницами
	 * @access public
	 * 
	 * @param domelement $xml - xml дерево
	 * @param domelement $parentNode - родительская нода
	 * @param int $docCount - общее число документов
	 * @param int $currentPage - текущая страница
	 * @param int $perPage - число документов на страницу
	 * @param string $queryBase - URL строка
	 * @param string $URLParamName - название GET параметра страницы
	 * @param string $limitStr - ссылка на строку с лимитом
	 * @return bool
	 */
	function Pages2XML($xml, $parentNode, $docCount, $currentPage, $perPage, $queryBase, $URLParamName, &$limitStr) {
		return $this->Pages2XMLExt($xml, $parentNode, $docCount, $currentPage, $perPage, $queryBase, $URLParamName, $limitStr, $pageCount, $nextStr, $prevStr);
	}

	/**
	 * Создание ноды xml с страницами (расширенный интерфейс)
	 *
	 * @param domelement $xml - xml дерево
	 * @param domelement $parentNode - родительская нода
	 * @param int $docCount - общее число документов
	 * @param int $currentPage - текущая страница
	 * @param int $perPage - число документов на страницу
	 * @param string $queryBase - URL строка
	 * @param string $URLParamName - название GET параметра страницы
	 * @param string $limitStr - ссылка на строку с лимитом
	 * @param int $pageCount - количество страниц
	 * @param int $nextStr - номер следующие страницы
	 * @param int $prevStr - номер предыдущей страницы
	 * @return bool
	 */
	function Pages2XMLExt($xml, $parentNode, $docCount, $currentPage, $perPage, $queryBase, $URLParamName, &$limitStr, &$pageCount, &$nextStr, &$prevStr) {
		/**
		 * Перевод длинного списка параметров в поля класса
		 * @todo Надо бы это делать в конструкторе
		 */
		$this->xml = $xml;
		$this->parentNode = $parentNode;
		$this->docCount = $docCount;
		$this->currentPage = $currentPage;
		$this->perPage = $perPage;
		$this->queryBase = $queryBase;
		$this->URLParamName = $URLParamName;
		$this->limitStr = &$limitStr;

		// собственно само создание нод
		if (!$this->_CreatePagesXMLNodes()) return false;

		/**
		 * присвоение переменным, которые были переданы по ссылке, новых значений 
		 * (а в реальности это первое присвоение им значений)
		 * @todo надо вместо модификации переменных, передаваемых по ссылке (&$limitStr, &$pageCount, &$nextStr, &$prevStr)
		 * обращатся к ним через  GetLimitStrt(), GetPageCount().
		 * Код станет более читабельным, а список передаваемых параметров сократится. Заодно саму функцию Pages2XMLExt() можно будет 
		 * удалить, оставив только Pages2XML()
		 */
		$pageCount = $this->pageCount;
		$nextStr = $this->nextStr;
		$prevStr = $this->prevStr;

		return true;
	}

	function GetLimitStr() {
		return $this->limitStr;
	}
	
	function GetPageCount() {
		return $this->pageCount;
	}
	
	function GetNextPage() {
		return $this->nextStr;
	}
	
	function GetPrevPage() {
		return $this->prevStr;
	}
	
	/**
	 * Создание набора нод со страницами
	 * @access private
	 * @return bool
	 */
	function _CreatePagesXMLNodes() {
		$this->_SetPageCount();
		if(!$this->_IsCurrentPageValid()) return false;
		$this->_SetLimitString();

		$this->_SetNextPage();
		$this->_SetPrevPage();

		$this->_CreatePagesNode();
		$this->_CreatePageNodes();
		return true;
	}

	/**
	 * Определение количества страниц
	 * @access private
	 */
	function _SetPageCount() {
		$this->pageCount = (0 == $this->docCount % $this->perPage) ? ($this->docCount/$this->perPage) : ceil($this->docCount/$this->perPage);
		if (0 == $this->pageCount) $this->pageCount++;
	}

	/**
	 * Является ли запращиваемая страница корректной
	 * @access private
	 * @return bool
	 */
	function _IsCurrentPageValid() {
		return (IsGoodNum($this->currentPage) and $this->currentPage <= $this->pageCount);
	}

	/**
	 * Установка значения номера следующие страницы
	 * @access private
	 */
	function _SetNextPage() {
		$this->nextStr = ($this->currentPage < $this->pageCount) ? $this->currentPage + 1 : "";
	}

	/**
	 * Установка значения номера предыдущей страницы
	 * @access private
	 */
	function _SetPrevPage() {
		$this->prevStr = ($this->currentPage > 1) ? $this->currentPage - 1 : "";
	}

	/**
	 * Установка значения LIMIT части запроса
	 * @access private
	 */
	function _SetLimitString() {
		$limitStringBegin = ($this->currentPage - 1) * $this->perPage;
		if (preg_match("/^([0-9]+),\s*([0-9]+)$/", trim($this->limitStr), $matches) > 0) {
			$limitStringBegin += $matches[1];
			$limitConstr = min($this->docCount, $this->perPage); //$docCount always less than $matches[2]
		} elseif (IsGoodNum(trim($this->limitStr))) {
			$limitConstr = min($this->docCount, $this->perPage); //$docCount always less than $limitStr
		} else {
			$limitConstr = $this->perPage;
		}
		$this->limitStr = "{$limitStringBegin}, {$limitConstr}";
	}


	/**
	 * Создание ноды контейнера для нод страниц
	 * @access private
	 */
	private function _CreatePagesNode() {
		$this->pagesNode = $this->xml->createElement("pages");
		$this->pagesNode->setAttribute("pageCount", $this->pageCount);
		$this->pagesNode->setAttribute("docCount", $this->docCount);
		$this->pagesNode->setAttribute("perPage", $this->perPage);
		$this->pagesNode->setAttribute("current", $this->currentPage);
		$this->pagesNode->setAttribute("next", $this->nextStr);
		$this->pagesNode->setAttribute("prev", $this->prevStr);
		$this->parentNode->appendChild($this->pagesNode);
	}

	/**
	 * Создание нод со страницами
	 * @access private
	 **/
	function _CreatePageNodes() {
		for ($i = 1; $i <= $this->pageCount; $i++) {
			$newPage = $this->xml->createElement("page");
			$newPage->setAttribute("num", $i);
			if ($i == $this->currentPage) $newPage->setAttribute("isCurrent", "1");
			$newPage->setAttribute("URL", URLReplaceParam($this->URLParamName, $i, $this->queryBase));
			$this->pagesNode->appendChild($newPage);
		}
	}
}

?>