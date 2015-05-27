<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
require_once(CMSPATH_LIB . "tables/base_tables.php");
/**
 * Класс проверки поля с файлом
 * @author fred
 */
class FileField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return FileField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	function _CheckConstraints() {
		if ($this->_IsFileForTable() && !$this->_FileMustBeSaved()) {
			return;
		}

		if ($this->_IsFileForDelete()) {
			$this->documentValidator->SetFile($this->fieldName, "Delete");
			return;
		}

		if ($this->_IsFileNotPassed()) {
			return;
		}

		if ($this->_IsBadExtension()) {
			$this->error = "BadFileExt";
			return;
		}

		if ($this->_IsTooBig()) {
			$this->error = "TooLargeFile";
			return;
		}

		$this->documentValidator->SetFile($this->fieldName, "Insert");
	}

	function _IsBlank() {
		$importance = (isset($this->conf["impt"]) and $this->conf["impt"]);

		return (
			$importance
			&& "Create" == $this->documentValidator->GetAct()
			&& (!isset($_FILES[$this->fieldName]) or !$_FILES[$this->fieldName]["name"])
		);
	}
	
	function _IsFileForDelete() {
		$importance = (isset($this->conf["impt"]) and $this->conf["impt"]);

		return (
			(!isset($_FILES[$this->fieldName]) or !$_FILES[$this->fieldName]["name"])
			&& !$importance
			&& "Edit" == $this->documentValidator->GetAct()
			&& $this->query->GetParam($this->fieldName . "_delete")
		);
	}

	function _IsBadExtension() {
		$fName = $_FILES[$this->fieldName]["name"];

		$fExt = (preg_match("/\.([0-9a-zA-Z$#()_]{1,10})$/", $fName, $matches) > 0) ? $matches[1] : "";
		if (!$fExt) {
			return true;
		}

		$fExt = strtolower($fExt);

		// Смотрим, разрешённое ли расширение у файла. Если нет, выдаём ошибку пользователю
		$fExtQuoted = preg_quote($fExt);
		return (!preg_match("~{$fExtQuoted}~", $this->conf["exts"]));
	}

	function _IsTooBig() {
		return ($_FILES[$this->fieldName]["size"] > $this->conf["maxs"]);
	}
	
	function _IsFileNotPassed() {
		return (!isset($_FILES[$this->fieldName]) or !$_FILES[$this->fieldName]["name"]);
	}

	private function _IsFileForTable() {
		foreach ($this->documentValidator->GetDocType() as $fieldName => $description) {
			if (isset($description["file"]) && $this->fieldName == $description["file"]) {
				return true;
			}
		}
		return false;
	}
	
	private function _FileMustBeSaved() {
		return (isset($this->conf['save']) && $this->conf['save']);
	}
}
?>