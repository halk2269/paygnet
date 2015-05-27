<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");
/**
 * Класс проверки списка
 * 
 * !!! Необходимо учесть, что этот класс возвращает false значение для GetValue()
 * 
 * @author fred
 */
class SelectField extends AbstractField {

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
		if (!$this->query->GetParam($this->fieldName) && "Edit" == $this->documentValidator->GetAct()) return;

		if (
			!$this->query->GetParam($this->fieldName)
			&& "Create" == $this->documentValidator->GetAct()
			&& !isset($this->conf["deft"])
		) {
			$this->error = "BadSelectID";
			return;
		}

		if (!IsGoodId($this->query->GetParam($this->fieldName))) {
			$this->error = "BadSelectID";
			return;
		}

		$selectArray = $this->_GetSelectData();
		if (!$selectArray) {
			$this->error = "BadSelectID";
			return;
		}

		$this->documentValidator->SetSelect($this->fieldName, $selectArray);
	}

	private function _GetSelectData() {
		$query = "
			SELECT 
				list_id AS list_id,
				id AS item_id, 
				name AS item_name,
				title AS item_title
			FROM 
				sys_dt_select_items 
			WHERE 
				id = ? 
				&& list_id = ?
				&& sort <> -1
		";
		
		$db = DBClass::GetInstance();
		return $db->GetRow(
			$query, 
			array($this->query->GetParam($this->fieldName), $this->conf["list"])
		);
		
	}
}
?>