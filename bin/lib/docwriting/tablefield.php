<?php

require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
require_once(CMSPATH_LIB . "tables/base_tables.php");

/**
 * Класс проверки поля таблица
 * @author IDM
 */
class TableField extends AbstractField {

	private $tableConvertor = null;

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return TableField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	protected function _CheckConstraints() {
		$this->tableConvertor = new BaseTablesClass();

		if ($this->_IsFile()) {
			if (!isset($_FILES[$this->conf["file"]]["tmp_name"]) || !$_FILES[$this->conf["file"]]["tmp_name"]) {
				return;
			}
			
			$this->value = $this->tableConvertor->FileToText($_FILES[$this->conf["file"]]["tmp_name"]);
			if (!$this->value) {
				$this->error = "InvalidFile";
				return;
			}
		} else {
			$this->value = $this->query->GetParam($this->fieldName);
		}

		$this->value = $this->tableConvertor->TextToXML($this->value, $this->conf);

		if (!$this->value) {
			$this->error = "TooLong";
		}
	}

	protected function _IsBlank() {
		if (!isset($this->conf["impt"]) || !$this->conf["impt"]) {
			return false;
		}
		
		if (isset($this->conf["hide"]) and $this->conf["hide"]) {
			return false;
		}
		
		if ($this->_IsFile()) {
			if (!isset($_FILES[$this->conf["file"]]["tmp_name"]) or !$_FILES[$this->conf["file"]]["tmp_name"]) {
				return true;
			} else {
				return false;
			}
		}

		$value = $this->query->GetParam($this->fieldName);
		return (false === $value || !trim($value));
	}

	/**
	 * Таблица формируется из файла?
	 * @return true | false
	 * @access private
	 */
	private function _IsFile() {
		return (isset($this->conf["file"]) && is_array($this->documentValidator->GetDocTypeField($this->conf["file"])));
	}
	
	
}
?>