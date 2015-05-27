<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
/**
 * Класс проверки целочисленного поля
 * @author fred
 */
class IntField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return IntField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _CheckConstraints() {
		$this->value = $this->query->GetParam($this->fieldName);

		if (!$this->value) {
			$this->value = 0;
			return;
		}

		if (!$this->_IsValidType()) {
			$this->error = "Bad" . ucfirst(substr(get_class($this), 0, -5));
			return;
		}

		if ($this->_IsTooBig()) {
			$this->error = "NumberTooBig";
			return;
		}

		if ($this->_IsTooSmall()) {
			$this->error = "NumberTooSmall";
			return;
		}
	}

	/**
	 * Проверка на соответствие типу поля
	 *
	 * @return bool
	 */
	function _IsValidType() {
		return IsGoodNum($this->value);
	}

	function _IsTooBig() {
		return (isset($this->conf["maxv"]) and $this->value > $this->conf["maxv"]);
	}

	function _IsTooSmall() {
		return (isset($this->conf["minv"]) and $this->value < $this->conf["minv"]);
	}
}
?>