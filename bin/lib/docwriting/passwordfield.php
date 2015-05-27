<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
/**
 * Класс проверки пароля
 * @author fred
 */
class PasswordField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return PasswordField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _CheckConstraints() {
		$this->value = $this->query->GetParam($this->fieldName);

		if (!$this->value and "Edit" == $this->documentValidator->GetAct()) {
			$this->value = false;
			return;	
		}
		
		if (!$this->value and "Create" == $this->documentValidator->GetAct()) {
			$this->error = "BlankPassword";
			return;
		}
		
		if (!$this->_IsConfirmed()) {
			$this->error = "PasswordsAreNotIdentical";
			return;
		}

		if (!$this->_IsRegExpOK()) {
			$this->error = "BadRegexp";
			return;
		}

		$this->value = md5($this->value);
	}

	function _IsRegExpOK() {
		if (!isset($this->conf["rexp"])) {
			return true;
		}
		
		return (!preg_match($this->conf["rexp"], $this->value) < 1);
	}

	function _IsBlank() {
		return false;
	}
	
	function _IsConfirmed() {
		return ($this->value == $this->query->GetParam($this->fieldName . "_passconfirm"));
	}
}
?>