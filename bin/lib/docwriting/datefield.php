<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
/**
 * Класс проверки даты
 * @author fred
 */
class DateField extends AbstractField {

	var $year;
	var $month;
	var $day;

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return DateField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _CheckConstraints() {
		if (isset($this->conf["show"]) && "selects" == $this->conf["show"]) {
			$this->_SetDateFromSelects();
		} elseif ($this->query->GetParam($this->fieldName)){
			$this->_SetDateFromSingleString();
		} else {
			$this->value = "";
			return;
		}
		
		if (!$this->_IsDateValid()) {
			$this->error = "BadDate";
			return;
		}

		$this->_SetValue();
	}

	function _IsBlank() {
		return (
			$this->conf["impt"]
			&& !(isset($this->conf["show"]) && "selects" == $this->conf["show"])
			&& !(isset($this->conf["hide"]) && $this->conf["hide"])
			&& !trim($this->query->GetParam($this->fieldName))
		);
	}

	function _IsHidden() {
		return (false === $this->query->GetParam($this->fieldName)
		&& (isset($this->conf["hide"]) && $this->conf["hide"]));
	}

	/**
	 * Получаем дату, пришедшую отдельными полями (год, месяц, ...)
	 *
	 * @return string/false
	 */
	function _SetDateFromSelects() {
		$this->year = $this->query->GetParam($this->fieldName . "_years");
		$this->month = $this->query->GetParam($this->fieldName . "_months");
		$this->day = $this->query->GetParam($this->fieldName . "_dates");
	}

	function _SetDateFromSingleString() {
		if (!preg_match("~^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$~", trim($this->query->GetParam($this->fieldName)), $result)) return false;
		$this->year = $result[1];
		$this->month = $result[2];
		$this->day = $result[3];
	}

	function _IsDateValid() {
		if (!IsGoodId($this->year) or 1970 > $this->year or $this->year > 2038) return false;
		if (!IsGoodId($this->month) or 1 > $this->month or $this->month > 12) return false;
		if (!IsGoodId($this->day) or 1 > $this->day or $this->day > 31) return false;
		return true;
	}

	function _SetValue() {
		$this->value = date("Y-m-d", mktime(0, 0, 0, $this->month, $this->day, $this->year));
	}
}
?>