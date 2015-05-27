<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
/**
 * Класс проверки строки
 */

class StringField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return StringField
	 */
	function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _CheckConstraints() {
		$this->value = $this->query->GetParam($this->fieldName);

		if (false === $this->value) {
			$this->value = "";
		}

		if (!$this->_IsLengthOK()) {
			$this->error = "TooLong";
			return;
		}
		
		if (!$this->_IsRegExpOK()) {
			$this->error = "BadRegexp";
			return;
		}
	}

	/**
	 * Проверка на ограничение по длине
	 *
	 * @return bool
	 */
	function _IsLengthOK() {
		return !(
			(isset($this->conf["leng"]) and mb_strlen($this->value) > $this->conf["leng"])
			|| (mb_strlen($this->value) > 255)
		);
	}

	function _IsRegExpOK() {
		if (!isset($this->conf["rexp"])) {
			return true;
		}
		
		if (!$this->conf["impt"] && !$this->value) {
			return true;
		}
		
		return (preg_match($this->conf["rexp"], $this->value) > 0);
	}
}
?>