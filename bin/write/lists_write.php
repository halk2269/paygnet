<?php

/**
 * Редактирование списков: интерфейс администратора
 * (!) Глобальная идея: пункты списка никогда не удаляются. Они отключаются
 * выставлением sort в -1. Если editBoth = false, то обновление осуществляется
 * по title. То есть мы можем в любое поле записать существующий title, и он
 * подхватится модулем записи (id останется тем же!). Если поле удаляется -
 * оно не удаляется фактически, а блокируется (sort = -1). Если editBoth = true, то
 * обновление идёт по name, а title просто ставится в соответствие name'у.
 * Если идёт добавление нового поля при editBoth = false, то name 
 * автоматически выставляется в строку concat('id', $id), где $id - уникальный 
 * id строки в таблице sys_dt_select_items (что предотвращает дублирование 
 * name'ов). Если редактирование списков идёт в режиме editBoth, дублирование 
 * name'ов пока не предотвращается.
 *
 */

class ListsWriteClass extends WriteModuleBaseClass {

	public function MakeChanges() {
		// Если данный пользователь не имеет прав на редактирование списков
		if (!$this->auth->CanEditLists()) {
			return false;
		}
		
		// Какой список редактируем?
		$listID = $this->_GetParam("list");
		// Передан некорректный ID - в сад
		if (!IsGoodNum($listID)) {
			return false;
		}
		
		// Проверяем, передан ли корректный id списка и заодно проверяем, редактируем ли мы и name и title, или только title
		$editBoth = $this->db->GetValue("SELECT editboth FROM sys_dt_select_lists WHERE id = {$listID} and canedit = 1");
		// ID некорректный - в сад
		if (!$editBoth) {
			return false;
		}
		
		// Сбрасываем все sort-поля данного списка в -1 (что озачает "не доступен для выбора")
		$this->db->SQL("UPDATE sys_dt_select_items SET sort = -1 WHERE list_id = {$listID}");

		$i = 1;
		$title = $this->_GetParam("ItemTitle" . $i);
		// Перебираем все пришедшие items
		while ($title !== false) {
			$title = trim($title);
			// Если редактиируются и name и title
			if ($editBoth) {
				// Смотрим, чему равен name
				$name = $this->_GetParam("ItemName" . $i);
				if (!$name) {
					break;
				}
				
				$name = trim($name);
				// Если строки name и title пустые - просто оставляем sort = -1 (ничего не трогаем)
				if ($name != "" && $title != "") {
					// Проверяем, существует ли уже пункт с таким name
					$listNumber = $this->db->GetValue(
						"SELECT id FROM sys_dt_select_items WHERE list_id = ? AND name = ?",
						array($listID, $name)		
					);
					// Существует?
					if ($listNumber) {
						// Существует - обновляем его
						$this->db->SQL(
							"UPDATE sys_dt_select_items SET sort = ?, title = ? WHERE id = ?",
							array($i, $title, $listNumber)
						);
					} else {
						// Не существует - добавляем новый пункт
						$this->db->SQL(
							"INSERT INTO 
								sys_dt_select_items (list_id, name, title, sort) 
							VALUES 
								(?, ?, ?, ?)",
							array($listID, $name, $title, $i)	
						);
					}
				}
			} else {
				// Если строка title пустая - просто оставляем sort = -1 (ничего не трогаем)
				if ($title != "") {
					// Проверяем, существует ли уже пункт с таким title
					$listNumber = $this->db->GetValue(
						"SELECT id FROM sys_dt_select_items WHERE list_id = ? and title = ?",
						array($listID, $title)
					);
					// Существует?
					if ($listNumber) {
						// Существует - обновляем его
						$this->db->SQL("UPDATE sys_dt_select_items SET sort = {$i} WHERE id = {$listNumber}");
					} else {
						// Не существует - добавляем новый пункт
						$this->db->SQL(
							"INSERT INTO 
								sys_dt_select_items (list_id, name, title, sort) 
							VALUES 
								(?, '', ?, ?)",
							array($listID, $title, $i)	
						);
						
						$lastID = $this->db->GetLastID();
						// Обновляем - в поле name должен быть id строки
						$this->db->SQL(
							"UPDATE 
								sys_dt_select_items 
							SET 
								name = ?
							WHERE 
								id = ?",
							array('id' . $lastID, $lastID)	
						);
					}
				}
			}
			
			$i++;
			$title = $this->_GetParam("ItemTitle" . $i);
		}
		
		// Изменения были сохранены...
		$this->_WriteInfo("ChangesWereSaved");
		return true;
	}

}

?>