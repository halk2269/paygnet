<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
require_once(CMSPATH_LIB . "doc/doccommon.php");
/**
 * Класс проверки ссылки на другой документ
 * 
 * @author fred
 */
class LinkField extends AbstractField {

	private $linkToId;
	
	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return LinkField
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

		if (!IsGoodId($this->value)) {
			$this->error = "BadSelectID";
			return;
		}

		if (!$this->_IsLinkExists()) {
			$this->error = "BadSelectID";
			return;
		}

		if ($this->_IsLinkToSelf()) {
			$this->error = "LinkToSelf";
			return;
		}
	}

	function _IsLinkExists() {
		$linkedDTTable = DocCommonClass::GetTableName($this->conf["doct"]);
		$db = DBClass::GetInstance();
		$this->linkToId = $db->GetValue("SELECT id FROM {$linkedDTTable} WHERE id = {$this->value}" );
		return $this->linkToId;
	}

	function _IsLinkToSelf() {
		return ($this->documentValidator->GetDocTypeName() == $this->conf["doct"]
		and $this->documentValidator->GetDocId() == $this->linkToId);
	}
	
	function _IsBlank() {
		$value = $this->query->GetParam($this->fieldName);
		$importance = (isset($this->conf["impt"]) and $this->conf["impt"]);
		
		return ($importance and ($value == 0));
	}
	
}
?>