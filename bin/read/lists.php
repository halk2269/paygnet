<?php

/**
 * Редактирование списков: интерфейс администратора
 */

class ListsReadClass extends ReadModuleBaseClass {

	public function CreateXML() {
		// Если данный пользователь не имеет прав на редактирование списков - в сад
		if (!$this->auth->CanEditLists()) {
			$this->accessDenied = true;
			$this->accessDeniedReason = "AdminPanel";
			return false;
		}
				
		// Список листов - выдаём в XML (условие выборки - список можно редактировать)
		$stmt = $this->db->SQL(
			"SELECT id, title, editboth FROM sys_dt_select_lists WHERE canedit = 1 ORDER BY title
		");
		$this->dt->ProcessQueryResults($stmt, $this->xml, $this->parentNode, "blank", false, false, 0, "", true, null, "list");
		
		// Если запрошен конкретный лист - выдаём его пункты
		$listID = $this->_GetSimpleParam("listid");
		if ($listID !== false && IsGoodNum($listID)) {
			$stmt = $this->db->SQL("SELECT title FROM sys_dt_select_lists WHERE id = {$listID} AND canedit = 1");
			// Список действительно можно редактировать
			
			if ($stmt->rowCount()) {
				$stmt = $this->db->SQL(
					"SELECT name, title FROM sys_dt_select_items WHERE list_id = {$listID} AND sort <> -1 ORDER BY sort
				");
				
				$this->dt->ProcessQueryResults(
					$stmt, $this->xml, $this->parentNode, "blank", false, false, 0, "", true, null, "listitem"
				);
			}
		}
		
		return true;

	}

}

?>