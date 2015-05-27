<?php

/**
 * Профиль пользователя. Создание и редактирование.
 * @author IDM
 */

class ProfileAReadClass extends ReadModuleBaseClass {

	public function CreateXML() {
		eval($this->params);

		$this->dt->GetFieldList($this->xml, $this->parentNode, $inDTName);

		$userID = $this->auth->GetUserID();
		if (!$userID) {
			$this->_WriteError("UserNotAuthed");
			return true;
		} 
			
		if (isset($this->dtconf->dtt[$inDTName]) && ($this->dtconf->dtt[$inDTName] != "")) {
			$this->xslList[] = array(
				"filename" => $this->dtconf->dtt[$inDTName], 
				"match" => "document[@docTypeName = '{$inDTName}']"
			);
		}
		
		$stmt = $this->dt->FormatSelectQuery($inDTName, $this->xml, $this->parentNode, "*", "", "", "dt.id={$userID}");
		if ($stmt && $stmt->rowCount()) {
			$this->dt->ProcessQueryResults(
				$stmt, $this->xml, $this->parentNode, $inDTName, false, false, 0, "", true, null, "document", true
			);
		}
		
		return true;
	}

}

?>