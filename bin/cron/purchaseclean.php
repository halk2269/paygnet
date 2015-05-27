<?php

/**
 * Модуль очистки информации о заказе
 * @author IDM
 */

class PurchaseCleanCronClass extends CronBaseClass {

	function MakeChanges() {
		$this->db->SQL("DELETE FROM shop_purchases WHERE DATE_SUB(NOW(), INTERVAL 45 DAY) > time AND user_reg = 'unreg'");
		$r = $this->db->GetNumRows();
		$this->report .= "shop_purchases was cleaned. {$r} rows were deleted\n";
	}

}

?>