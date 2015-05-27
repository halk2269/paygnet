<?php
require_once(CMSPATH_LIB . "docwriting/datefield.php");
/**
 * Класс проверки даты и времени
 * @author fred
 */
class DateTimeField extends DateField {
	
	var $hour;
	var $minute;
	var $second;

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return DateTimeField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _SetDateFromSelects() {
		$this->year = $this->query->GetParam($this->fieldName . "_years");
		$this->month = $this->query->GetParam($this->fieldName . "_months");
		$this->day = $this->query->GetParam($this->fieldName . "_dates");
		$this->hour = $this->query->GetParam($this->fieldName . "_hours");
		$this->minute = $this->query->GetParam($this->fieldName . "_minutes");
		$this->second = "00";
	}

	function _SetDateFromSingleString() {
		if (!preg_match("~^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$~", trim($this->query->GetParam($this->fieldName)), $result)) return false;
		$this->year = $result[1];
		$this->month = $result[2];
		$this->day = $result[3];
		$this->hour = $result[4];
		$this->minute = $result[5];
		$this->second = $result[6];
	}

	function _IsDateValid() {
		if (!IsGoodId($this->year) or 1970 > $this->year or $this->year > 2038) return false;
		if (!IsGoodId($this->month) or 1 > $this->month or $this->month > 12) return false;
		if (!IsGoodId($this->day) or 1 > $this->day or $this->day > 31) return false;
		if (!IsGoodNum($this->hour) or 0 > $this->hour or $this->hour > 23) return false;
		if (!IsGoodNum($this->minute) or 0 > $this->minute or $this->minute > 59) return false;
		if (!IsGoodNum($this->second) or 0 > $this->second or $this->second > 59) return false;
		return true;
	}

	function _SetValue() {
		$this->value = date("Y-m-d H:i:s", mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year));
	}
}
?>