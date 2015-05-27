<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
/**
 * Класс проверки поля типа массив
 */
class ArrayField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return BoolField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _IsBlank() {
		return false;
	}
}
?>