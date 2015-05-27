<?php
require_once(CMSPATH_LIB . "docwriting/intfield.php");
/**
 * Класс проверки числа с десятичной частью
 * @author fred
 */
class FloatField extends IntField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return FloatField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	/**
	 * Проверка на соответствие типу поля
	 *
	 * @return bool
	 */
	function _IsValidType() {
		return !preg_match("/[^0-9.,\-]/", $this->value);
	}
}
?>