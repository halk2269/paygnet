<?php

/**
 * Модуль записи информации о заказе
 * @author IDM
 **/

require_once(CMSPATH_MOD_WRITE . "docwriting.php");
require_once(CMSPATH_PLIB . $this->conf->Param("EshopCommonPath"));

class OrderSubmitWriteClass extends DocWritingWriteClass {

	/**
	 * @var EshopCommonPersonalClass
	 **/
	protected $eshop;
	
	protected $purchase;

	public function MakeChanges() {
		$commonClassName = $this->conf->Param('EshopCommonClassName');
		$this->eshop = new $commonClassName();
		
		$id = 0;
		$qref = $this->_GetParam("qref");
		if (!IsGoodNum($qref)) {
			return false;
		}
		
		$params = $this->db->GetValue("SELECT params FROM sys_references WHERE id = '{$qref}'");
		if (!$params) {
			return false;
		}
		
		eval($params);
		if (!$this->CheckDT($inDTName)) {
			return false;
		}
			
		if ($this->OnBeforeSubmitCheck()) {
			$this->OnBeforeSubmit();
			return true;
		}
		
		if ($this->DoActionBeforeSubmit()) {
			if ($this->OnBeforeSubmitAction() != "continue_confirmation") {
				return $this->OnBeforeSubmitAction();
			}
		}
				
		// продолжаем оформление заказа
		if (0 == $this->eshop->GetCanSubmit()) {
			$this->_WriteError('CannotSubmit');
			return false;
		}
		
		$this->BeforeSubmitSetParam($inDTName);
		
		$this->purchase = $this->eshop->GetPurchaseID();
		$cnt = $this->db->GetValue("SELECT COUNT(*) FROM shop_items WHERE purchase_id = '{$this->purchase}'");
		if (!$this->purchase && $cnt == 0) {
			return false;
		}
		
		$this->db->Begin();
		
		$rv = $this->_GoWriting("Create", $qref, $id, $inDTName, false, $lastID);
		if (!$rv and $lastID == 0) {
			$this->db->Rollback();
			return false;
		}
		if (!$this->OnSuccessful($qref, "Edit", $inDTName, $id, false, $rv, $lastID)) {
			$this->db->Rollback();
			return false;
		}
		
		$this->db->Commit();
		
		$attachList = array();
		if ($this->conf->Param("UsePEAR")) {
			require_once("Spreadsheet/Excel/Writer.php");
			
			$path = $this->eshop->CreateExcelFile($lastID);
			if ($path) {
				$attachList["excel"] = array("path" => $path, "name" => "order.xls");
			}
		}
		
		$this->_SendNotify($qref, "Create", $inDTName, $lastID, $this->GetMailList(), $attachList);
		if (isset($path) && file_exists($path)) {
			unlink($path);
		}
				
		return true;
	}
	
	protected function _SendNotifyXMLModify($xml, $parentNode) {
		$summaryNode = X_CreateNode($xml, $parentNode, 'summary');

		$stmt = $this->db->SQL("SELECT DISTINCT dt FROM shop_items WHERE purchase_id = '{$this->purchase}'");
		while ($row = $stmt->fetchObject()) {
			$this->dt->GetFieldList($xml, $summaryNode, $row->dt);
		}

		$stmtItems = $this->db->SQL("
			SELECT 
				id, dt, doc_id, 
				title, price, number, 
				subdoc_id, subdoc_title 
			FROM 
				shop_items 
			WHERE 
				purchase_id = '{$this->purchase}'
		");
		
		while ($row = $stmtItems->fetchObject()) {
			if (!$row->subdoc_id) {
				// shop_id - id товара в таблице shop_items
				$result = $this->dt->FormatSelectQuery(
					$row->dt, $xml, $parentNode, $dtFields = "*", 
					$auxFields = "{$row->number} as count, {$row->price}*{$row->number} as amount, {$row->id} as shop_id", 
					"", 
					$where = "dt.id = {$row->doc_id}", 
					"",	"",	0, 0, 
					$stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime"
				); 
								
				$this->dt->Select2XML_V2($result, $xml, $summaryNode, $row->dt);
			} else {
				$result = $this->dt->FormatSelectQuery(
					$row->dt, $xml, $parentNode, $dtFields = "*", 
					$auxFields = "{$row->number} as count, {$row->price}*{$row->number} as amount, {$row->id} as shop_id, {$row->subdoc_id} as subdocID, {$row->subdoc_title} as subdocTitle", 
					"", 
					$where = "dt.id = {$row->doc_id}", 
					"", "", 0, 0, 
					$stdFields = "dt.id, dt.enabled, dt.addtime, dt.chtime"
				);
				$this->dt->Select2XML_V2($result, $xml, $summaryNode, $row->dt);
			}
		}

		$this->eshop->CreateSummaryNode($xml, $summaryNode, $this->purchase);
		
		if ($this->eshop->IsNeededFullInfo()) {
			$this->eshop->GetFullInfo($xml, $summaryNode, $this->purchase);
		}
		
		return true;
	}
	
	protected function CheckDT($dtName) {
		return (isset($dtName) && ("purchases" == $dtName));
	}
	
	protected function OnBeforeSubmitCheck() {
		return false;
	}
	
	protected function OnBeforeSubmit() {
		return;
	}
	
	protected function DoActionBeforeSubmit() {
		return false;
	}
	
	/**
	 * Набор действий, которые можно совершить до момента
	 * оформления заказа
	 */
	protected function OnBeforeSubmitAction() {
		return;
	} 
	
	protected function BeforeSubmitSetParam($dtName) {
		return;
	}
	
	/**
	 * Список e-mail адресов, на которые отсылается информация о заказе
	 */
	protected function GetMailList() {
		$email = trim($this->_GetParam("email"));
		return $email;
	}
	
	protected function OnSuccessful($qref, $act, $dtName, $id, $canCreateEnabled, $rv, $lastID) {
		return $this->db->SQL("
			UPDATE 
				shop_purchases 
			SET 
				order_detailed_id = '{$lastID}', state = 'confirmed' 
			WHERE 
				id = '{$this->purchase}'
		");
	}
	
}

?>