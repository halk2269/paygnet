<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
/**
 * Класс проверки набора строк (разделенных через \n)
 * 
 * @todo Необходимо учесть, что этот класс возвращает false значение для GetValue()
 * 
 */
class StrlistField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return StrlistField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _CheckConstraints() {
		$value = preg_replace("/\r\n/", "\n", $this->query->GetParam($this->fieldName));
		$value = preg_replace("/\r/", "\n", $value);
		$value = preg_replace("/\n/", "\r\n", $value);
		// Удаляем поседний перенос
		$value = preg_replace("/\r\n$/", "", $value);
		
		$this->documentValidator->SetStrList($this->fieldName, $value);
	}
}
?>