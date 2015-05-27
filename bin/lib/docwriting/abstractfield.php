<?php
/**
 * Базовый класс проверки поля. От него
 * наследуются конекретные классы для типов полей
 * 
 * @abstract 
 * @author fred
 */
abstract class AbstractField {
	/**
	 * @var QueryClass
	 */
	protected $query;
	/**
	 * @var DocumentValidator
	 */
	protected $documentValidator;

	protected $conf;
	protected $fieldName;
	protected $value = false;
	protected $error = false;

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return AbstractField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		$this->query = $query;
		$this->documentValidator = $documentValidator;
		$this->conf = $conf;
		$this->fieldName = $fieldName;

		if ($this->_IsBlank()) {
			$this->error = "BlankField";
			return;
		}
		
		if ($this->_IsHidden()) {
			return;
		}
		
		$this->_CheckConstraints();
	}

	/**
	 * Возвращает код ошибки, если она произошла
	 *
	 * @return string/false
	 */
	public function GetError() {
		return $this->error;
	}

	/**
	 * Возвращает значение поля
	 *
	 * @return mixed
	 */
	public function GetValue() {
		return $this->value;
	}

	/**
	 * Метод проверки, переопереляемый в наследниках
	 */
	protected function _CheckConstraints() {
	}

	/**
	 * Проверка поля на пустоту.
	 * 
	 * Переопределена в:
	 * + "bool" 
	 * + "file"
	 * - "image"
	 * + "password"
	 * + "datetime"
	 * + "date"
	 * + "table"
	 * чтобы отключить эту проверку !!!
	 *
	 * @return bool
	 */
	protected function _IsBlank() {
		$value = $this->query->GetParam($this->fieldName);
		$importance = (isset($this->conf["impt"]) && $this->conf["impt"]);
		$hidden = (isset($this->conf["hide"]) && $this->conf["hide"]) ? 1 : 0;

		return ($importance && !$hidden && (($value === false) or trim($value) == ""));
	}
	
	/**
	 * Является ли поле скрытым
	 * 
	 * Переопределено в 
	 * + date
	 * + datetime
	 *
	 * @return bool
	 */
	protected function _IsHidden() {
		return (false === $this->query->GetParam($this->fieldName)
		&& (isset($this->conf["hide"]) && $this->conf["hide"])
		&& "Edit" == $this->documentValidator->GetAct());
	}
}
?>