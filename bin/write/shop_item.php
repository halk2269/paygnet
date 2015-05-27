<?php

/**
 * Модуль записи для корзины.
 * Предусмотрена возможность добавления как документов, так и поддокументов.
 * 
 * @author IDM
 */
require_once(CMSPATH_PLIB . $this->conf->Param("EshopCommonPath"));

class ShopItemWriteClass extends WriteModuleBaseClass {
	
	/**
	 * @var EshopCommonPersonalClass
	 */
	private $eshop;
	
	function MakeChanges() {
		$commonClassName = $this->conf->Param('EshopCommonClassName');
		$this->eshop = new $commonClassName();
				
		if (!$this->ref) {
			return false;
		}
		$params = $this->db->GetValue("SELECT params FROM sys_references WHERE id = {$this->ref}");
		if (!$params) {
			return false;
		}
		eval($params);
		if (!isset($inDTName)) {
			return false;
		}
		if (!isset($this->dtconf->dts[$inDTName])) {
			return false;
		}
		
		foreach ($_POST as $idx => $val) {
			// Добавляем документы в корзину
			if (preg_match("~doc([0-9]{1,11})~", $idx, $matche) == 1) {
				$quantity = $this->_GetParam("cnt{$matche[1]}");
				if (!IsGoodNum($quantity) or ($quantity <= 0)) {
					continue;
				}
				
				$tableName = DocCommonClass::GetTableName($inDTName);
				
				// Проверяем существует ли данный документ
				if ($this->db->RowExists("SELECT id FROM {$tableName} WHERE id = {$matche[1]} and enabled = 1")) {
					$typeOfPrice = $this->eshop->GetPriceColumnName($inDTName);
					$title = $this->eshop->GetTitleColumnName();
					
					$row = $this->db->GetRow("
						SELECT 
							{$title}, {$typeOfPrice} 
						FROM 
							{$tableName} 
						WHERE 
							id = {$matche[1]} and enabled = 1
					");
					
					$this->eshop->AddItem($inDTName, $matche[1], $row->$title, $row->$typeOfPrice, $quantity);
				}	
			// Добавляем поддокументы в корзину	
			} elseif (preg_match("/doc([0-9]{1,11})subdoc([0-9]{1,11})/", $idx, $matches) == 1) {
				$quantity = $this->_GetParam("cnt{$matches[2]}");
				if (!IsGoodNum($quantity) or ($quantity == 0)) continue;
				
				$tableName = DocCommonClass::GetTableName($inDTName);
				
				$row = $this->db->GetRow("
					SELECT 
						dt.title AS 'title', dti.subdoc_title AS 'subdoc_title', dti.price AS 'price' 
					FROM 
						{$tableName}info dti
					JOIN 
						{$tableName} dt ON dt.id = dti.parent_id 	
					WHERE 
						dt.id = {$matches[1]} and dti.id = {$matches[2]} and dti.enabled = 1
				");
				
				if (!$row) {
					continue;
				}
				
				$this->eshop->AddItem($inDTName, $matches[1], $row->title, $row->price, $quantity, $matches[2], $row->subdoc_title);
			} else {
				continue;
			}
		}	
		
		return true;
	}
	
}

?>