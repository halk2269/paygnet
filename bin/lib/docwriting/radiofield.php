<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");

/**
 * Класс проверки radio
 * 
 * @author busta
 */
class RadioField extends AbstractField {

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return SelectField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	protected function _CheckConstraints() {
		if (!$this->query->GetParam($this->fieldName) && "Edit" == $this->documentValidator->GetAct()) {
			return;
		}

		if (
			!$this->query->GetParam($this->fieldName)
			&& "Create" == $this->documentValidator->GetAct()
			&& !isset($this->conf["deft"])
		) {
			$this->error = "BadRadioID";
			return;
		}

		if (!IsGoodId($this->query->GetParam($this->fieldName))) {
			$this->error = "BadRadioID";
			return;
		}

		$radioArray = $this->_GetSelectData();
		if (!$radioArray) {
			$this->error = "BadRadioID";
			return;
		}

		$this->documentValidator->SetRadio($this->fieldName, $radioArray);
	}

	private function _GetSelectData() {
		$query = "
			SELECT 
				id AS item_id,
				list_id AS list_id,
				name AS item_name,
				title AS item_title
			FROM 
				sys_dt_select_items 
			WHERE 
				id = ? 
				AND list_id = ? 
				AND sort <> -1
		";
		
		$db = DBClass::GetInstance();
		return $db->GetRow(
			$query,
			array($this->query->GetParam($this->fieldName), $this->conf["list"])
		);		
	}
}
?>