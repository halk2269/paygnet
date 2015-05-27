<?php

/**
 * Модуль пересчёта корзины
 * @author IDM
 */

require_once(CMSPATH_PLIB . $this->conf->Param("EshopCommonPath"));

class CartCalcWriteClass extends WriteModuleBaseClass {
	
	/**
	 * @var EshopCommonPersonalClass
	 */
	private $eshop;
	
	function MakeChanges() {
		$commonClassName = $this->conf->Param('EshopCommonClassName');
		$this->eshop = new $commonClassName();
				
		if ($this->eshop->ParamsExist() or $this->_GetParam("recount") or $this->_GetParam("adjust")) {
			foreach($_POST as $idx => $val) {
				if (preg_match("~item([0-9]{1,11})~", $idx, $matchItem) < 1) {
					continue;
				}
				if (!IsGoodNum($val) or ($val == 0)) {
					continue;
				}
				
				$this->db->SQL("
					UPDATE 
						shop_items 
					SET 
						number = '{$val}' 
					WHERE 
						id = {$matchItem[1]}
				");
			}
			
			$this->eshop->UpdatePrice();
			
			// учет скидок и доставки
			if (!$this->eshop->WriteFullInfo()) {
				return false;
			}
		}
		
		if ($this->_GetParam("delete")) {
			if (!$this->DoDelete()) return false;
		}
		
		foreach($_POST as $idx => $val) {
			if (preg_match("~deleteItem([0-9]{1,11})~", $idx, $matchItem) < 1) {
				continue;
			}
			if (!$this->DeleteItem($matchItem[1])) {
				return false;	
			}
			if (!$this->DoActionAfterDeleteItem()) {
				return false;
			}
		}
		
		if ($this->_GetParam("adjust")) {
			$this->DoAdjust();
		}
					
		return true;
	}
	
	/**
	 * Удаление заказа
	 */
	private function DoDelete() {
		$stmt = $this->db->SQL("DELETE FROM shop_purchases WHERE id = {$this->eshop->GetPurchaseID()}");
		return ($stmt->rowCount() < 1) ? false : true;
	}
	
	/**
	 * Переход к оформлению заказа
	 */
	private function DoAdjust() {
		if ($this->eshop->GetUserReg() == 'reg' and $this->eshop->GetPurchaseID()) {
			$orderPath = $this->_GetParam("orderPath");
			$this->retPath = ($orderPath) ? $orderPath : $this->prefix;
		} else {
			$enterPath = $this->_GetParam("enterPath");
			$this->retPath = ($enterPath) ? $enterPath : $this->prefix;
		}
	}
	
	private function DeleteItem($itemId) {
		$stmt = $this->db->SQL("DELETE FROM shop_items WHERE id = {$itemId}");
		return ($stmt->rowCount() < 1) ? false : true;
	}
	
	private function DoActionAfterDeleteItem() {
		$number = $this->db->GetValue("SELECT COUNT(*) FROM shop_items WHERE purchase_id = {$this->eshop->GetPurchaseID()}");
		if ($number >= 1) {
			$this->eshop->UpdatePrice();
			if ($this->eshop->ParamsExist() and !$this->eshop->WriteFullInfo()) {
				return false;
			}
		} else {
			if (!$this->DoDelete()) {
				return false;
			}
		}
		
		return true;
	}
		
}

?>