<?php

/**
 * Модуль выдаёт содержимое корзины (развёрнуто, со всеми дочерними документами).
 */

class CartFullReadClass extends ReadModuleBaseClass {
	
	private $eshop;

	public function CreateXML() {
		require_once CMSPATH_PLIB . $this->conf->Param("EshopCommonPath");
		
		$xml = $this->xml;
		$parentNode = $this->parentNode;
		
		$commonClassName = $this->conf->Param('EshopCommonClassName');
		$this->eshop = new $commonClassName();
				
		$purchaseID = $this->eshop->GetPurchaseID();
		if (!$purchaseID) {
			return true;
		}
		
		$this->eshop->GetFullInfo($xml, $parentNode);
		
		eval($this->params);
		
		if (!isset($inDTName) || $inDTName != "purchases") {
			return false;
		}
		
		// Нода, сообщающая о том, что не надо выводить количество символов в вводимых строках 
		if (isset($disableStringConstr) && $disableStringConstr) {
			X_CreateNode($xml, $parentNode, "disableStringConstr", "1");
		}
		
		if ($this->_GetSimpleParam("order") !== false) {
			$this->dt->GetFieldList($xml, $parentNode, $inDTName);
		}
		
		foreach ($this->dtconf->dts as $index => $value) {
			if (isset($this->dtconf->dts[$index])) {
				$this->dt->GetFieldList($xml, $parentNode, $index);
			}
			
			$stmt = $this->db->SQL("
				SELECT 
					id, dt, doc_id, title, price, number, subdoc_id, subdoc_title 
				FROM 
					shop_items 
				WHERE 
					purchase_id = {$purchaseID}
			");
			
			while ($row = $stmt->fetchObject()) {
				if ($index != $row->dt) {
					continue;
				}
				
				if ($row->subdoc_id) {
					// shop_id - id товара в таблице shop_items	
					$SQLResult = $this->dt->FormatSelectQuery(
						$row->dt, $xml, $parentNode, $dtFields = "*", 
						$auxFields = "{$row->number} as count, {$row->price}*{$row->number} as amount, {$row->id} as shop_id, {$row->subdoc_id} as subdocID, {$row->subdoc_title} as subdocTitle", 
						"", $where = "dt.id = {$row->doc_id}", "", "", 0, 0, 
						$stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime"
					);   
				} else {
					$SQLResult = $this->dt->FormatSelectQuery(
						$row->dt, $xml, $parentNode, $dtFields = "*", 
						$auxFields = "{$row->number} as count, {$row->price}*{$row->number} as amount, {$row->id} as shop_id", 
						"", $where = "dt.id = {$row->doc_id}", "", "", 0, 0, 
						$stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime
					");  
				}
					
				$this->dt->Select2XML_V2(
					$SQLResult, $xml, $parentNode, $row->dt, "", 0, false, false, "", false, null, $rowNodeName = "document", $showURL = true
				); 
			}
		}
		
		return true;				
	}
	
} 

?>