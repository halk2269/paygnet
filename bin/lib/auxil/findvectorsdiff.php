<?php

/**
 * Класс для подготовки данных для обновления таблицы "многие-ко-многим"
 * Получает на вход 
 *  - массив id, которые в таблице были
 *  - массив id, которые в таблице должны быть после изменения
 * На выходе из:
 *  - GetItemsForDelete() получаем строку из id (через запятую), которые надо удалить из таблицы
 *	- GetItemsForInsert() получаем строку из id (через запятую), которые надо вставить в таблицу
 * 
 */
class FindVectorsDiffClass {
	
	private $origIds = array();
	private $newIds = array();

	private $itemsForDelete = array();
	private $itemsForInsert = array();

	/**
	 * @param array[int] $origIds - исходный набор id
	 * @param array[int] $newIds - измененный набор id
	 * @return FindDiffClass
	 */
	public function __construct($origIds, $newIds) {
		$this->origIds = $origIds;
		$this->newIds = $newIds;
		
		$this->_CalculateArrays();
	}

	public function GetItemsForDelete() {
		return (isset($this->itemsForDelete[0]) and $this->itemsForDelete[0]) ? $this->itemsForDelete : false;
	}
	
	public function GetItemsForInsert() {
		return (isset($this->itemsForInsert[0]) and $this->itemsForInsert[0]) ? $this->itemsForInsert : false;
	}


	private function _CalculateArrays() {
		if (!is_array($this->origIds) || !is_array($this->newIds)) {
			return false;
		}
		
		foreach ($this->origIds as $origId) {
			if (false === $this->_GetArrayIndex($this->newIds, $origId)) {
				$this->itemsForDelete[] = $origId;
			}
		}
		
		foreach ($this->newIds as $newId) {
			if (false === $this->_GetArrayIndex($this->origIds, $newId)) {
				$this->itemsForInsert[] = $newId;
			}
		}
		
		return true;
	}
		
	private function _GetArrayIndex($array, $searchVal) {
		foreach ($array as $key => $value) {
			if ($value == $searchVal) {
				return  $key;
			}
		}
		
		return false;
	}

}

?>