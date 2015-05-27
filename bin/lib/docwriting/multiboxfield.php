<?php
require_once(CMSPATH_LIB . "docwriting/abstractfield.php");

/**
 * Класс проверки multibox
 * 
 * @author busta
 */
class MultiboxField extends AbstractField {
	
	private $checkedIds = array();

	/**
	 * @param DocumentValidator $documentValidator - вызывающий класс
	 * @param QueryClass $query
	 * @param array $conf - конфигурация поля (кусочек dtconf)
	 * @param string $fieldName - имя поля
	 * @return SelectField
	 */
	public function __construct($documentValidator, $query, $conf, $fieldName) {
		$this->_SetCheckedIds();
		parent::__construct($documentValidator, $query, $conf, $fieldName);
	}

	protected function _CheckConstraints() {
		if (!$this->_IsChecked() && "Edit" == $this->documentValidator->GetAct()) {
			return;
		}

		if (
			!$this->_IsChecked()
			&& "Create" == $this->documentValidator->GetAct()
		) {
			$this->error = "UncheckedMultibox";
			return;
		}

		$multiboxArray = $this->_GetMultiboxData();
		if (!is_array($multiboxArray)) {
			$this->error = "UncheckedMultibox";
			return;
		}
		
		$this->documentValidator->SetMultibox($this->fieldName, $multiboxArray);
	}
	
	protected function _IsBlank() {
		$importance = (isset($this->conf["impt"]) && $this->conf["impt"]);
		$hidden = (isset($this->conf["hide"]) && $this->conf["hide"]) ? 1 : 0;
		
		return ($importance && !$hidden && !$this->_IsChecked());
	}
	
	protected function _IsHidden() {
		return (
			!$this->_IsChecked()
			&& (isset($this->conf["hide"]) && $this->conf["hide"])
			&& "Edit" == $this->documentValidator->GetAct()
		);
	}

	private function _GetMultiboxData() {
		$db = DBClass::GetInstance();
		$iDs = implode(',', $this->_GetCheckedIds());
		
		$stmt = $db->SQL("
			SELECT 
				id AS item_id,
				list_id AS list_id
			FROM 
				sys_dt_select_items 
			WHERE 
				id IN (" . $iDs . ") 
				AND list_id = {$this->conf["list"]} 
				AND sort <> -1
		");
		
		if (!$stmt->rowCount()) {
			return false;
		}
		
		$selectedData = array();
		while ($row = $stmt->fetchObject()) {
			$selectedData[$row['item_id']] = $row['list_id'];
		}
		
		return $selectedData;		
	}
	
	private function _SetCheckedIds() { 
		foreach ($_POST as $index => $value) {
			if (preg_match('~' . $this->fieldName .  '_([0-9]{1,2})$~', $index, $matches) > 0) {
				$this->checkedIds[] = $matches[1];
			}
		}
	}
	
	private function _GetCheckedIds() {
		return $this->checkedIds;
	}
	
	private function _IsChecked() {
		return (sizeof($this->checkedIds) > 0);
	}
}

?>