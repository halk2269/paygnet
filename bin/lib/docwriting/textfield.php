<?php

require_once(CMSPATH_LIB . "docwriting/abstractfield.php");

/**
 * Класс проверки текстового поля
 */
class TextField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return TextField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}
	
	protected function _CheckConstraints() {
		$this->value = $this->query->GetParam($this->fieldName);
		if (false === $this->value) {
			$this->value = "";
		}

		if (!$this->_IsLengthOK()) {
			$this->error = "TooLong";
			return;
		}

		if (isset($this->conf["mode"]) && $this->conf["mode"] == 'nl2br') {
			$this->value = htmlspecialchars($this->value);
			$this->value = nl2br($this->value);
		}

		if (!isset($val["mode"]) || $val["mode"] == 'wyswyg') {
			$conf = GlobalConfClass::GetInstance();
			$prefixQ = preg_quote($conf->Param("Prefix"));
			$prefixQ = preg_replace('~\/~', '\\\/', $prefixQ);
			$this->value = preg_replace("~(src|href)=\"{$prefixQ}wyswyg~i", "\\1=\"wyswyg", $this->value);
		}
	}

	/**
	 * Проверка на ограничение по длине
	 *
	 * @return bool
	 */
	protected function _IsLengthOK() {
		// Если проверка длины подразумевается без пробелов, то убираем пробелы перед проверкой длины
		$length = (isset($this->conf["nosp"]) && $this->conf["nosp"])
			? mb_strlen(mb_ereg_replace("[[:space:]]", "", $this->value))
			: mb_strlen($this->value);

		return !(isset($this->conf["leng"]) && $length > $this->conf["leng"]);
	}
}
?>