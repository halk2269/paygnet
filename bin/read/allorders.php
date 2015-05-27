<?php

/**
 * Отображение общей информации о корзине.
 */

require_once(CMSPATH_MOD_READ . "advdoc.php");

class AllOrdersReadClass extends ReadModuleBaseClass {
	
	public function CreateXML() {
		$roleName = $this->auth->GetRoleName();
		if ($roleName != 'admin' || $roleName != 'superadmin') {
			$this->_SetAccessDenied("You haven't got right to access this section!");
		}
		
		$docID = $this->_GetParam("id");
		$userId = $this->auth->GetUserID();		
		
		if ($docID) {
			if (!IsGoodNum($docID)) {
				return false;
			}
			
			$itemsQuery = $this->db->SQL("
				SELECT 
					i.title, i.price, i.number 
				FROM  
					shop_items i 
				JOIN 
					shop_purchases p ON i.purchase_id = p.id
				WHERE 
					p.id = {$docID} AND p.user_id = {$userID}
			");
			
			$this->dt->Select2XML_V2($itemsQuery, $this->xml, $this->parentNode, "items");
			$this->parentNode->setAttribute('documentID', $docID);
		} else {
			$ordersQuery = $this->db->SQL(
				"SELECT * FROM shop_purchases WHERE user_id = {$userID} AND state = 'confirmed'
			");
			
			$this->dt->Select2XML_V2($ordersQuery, $this->xml, $this->parentNode, "items");
		}

		return true;
	}

}

?>