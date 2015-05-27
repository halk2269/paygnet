<?php
/**
 * Вывод все заявок
 *
 */
class RequestsReadClass extends ReadModuleBaseClass {

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
		
		if ($docID = $this->_GetParam("id")) {
			if (!IsGoodNum($docID)) return false;
			$this->parentNode->set_attribute('documentID', $docID);
			
			$infoQuery = $this->db->SQL("
								SELECT * FROM 
									dt_request dt 
								WHERE 
									dt.id = '{$docID}'
							");
			$this->dt->Select2XML_V2($infoQuery, $this->xml, $this->parentNode, "request");
		} else {
			$inLimit = "";
			$paging = new PagingClass();
			$paging->Pages2XML($this->xml, $this->parentNode, $this->_GetDocCount(), $this->_GetCurrentPage(), $this->_GetPerPage(), $this->queryClass->GetQuery(), "r{$this->thisID}_page", $inLimit);
			
			if ($inLimit) {
				$inLimit = " LIMIT " . $inLimit;
			}
			
			$ordersQuery = $this->db->SQL("
				SELECT 
					dt.organization, dt.contact, dt.phone, dt.email, dt.forconnect, dt.info, dt.id
				FROM 
					dt_request dt 
				ORDER BY dt.id DESC 
				{$inLimit}
			");
			
			$this->dt->Select2XML_V2($ordersQuery, $this->xml, $this->parentNode, "blank");
		}

		return true;
	}



	/**
	 * Возвращает количество документов на странице
	 *
	 * @return int
	 */
	function _GetPerPage() {
		eval($this->params);
		return isset($inPerPage) ? (int)$inPerPage : 20;
	}
	
	/**
	 * Возвращает номер текущей страницы
	 *
	 * @return int
	 */
	function _GetCurrentPage() {
		return ($this->_GetParam("page") and IsGoodId($this->_GetParam("page"))) ? $this->_GetParam("page") : 1;
	}
	
	/**
	 * Возвращает число заявок
	 * 
	 * @return int
	 */
	function _GetDocCount() {
		return $this->db->GetValue("SELECT COUNT(*) FROM dt_request");
	}
}
?>