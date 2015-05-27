<?php

/**
 * Общий класс интернет–магазина для модулей чтения и записи
 */

abstract class EshopCommonClass {

	protected $userReg;

	protected $allPurchases = array();

	protected $purchaseID = 0;
	protected $minPrice = 0;
	protected $canSubmit = 0;

	/**
	 * Тип цены
	 */
	protected $price;
	/**
	 * Название строки наименования товара в БД
	 */
	protected $title;
	
	protected $db;
	protected $auth;
	protected $conf;
	protected $query;
			
	public function __construct() {
		$this->db = DBClass::GetInstance();
		$this->auth = AuthClass::GetInstance();
		$this->conf = GlobalConfClass::GetInstance();
		$this->query = new QueryClass();
		
		$this->SetTitleColumnName();
		$this->SetPriceColumnName();
		$this->SetUserReg();

		$this->Initialize();
	}
	
	public function GetPurchaseID() {
		return $this->purchaseID;
	}
	
	public function GetCanSubmit() {
		return $this->canSubmit;
	}

	public function GetAllPurchases() {
		$stmt = $this->db->SQL("SELECT id FROM shop_purchases WHERE state = 'raw' ORDER BY id ASC");

		while ($row = $stmt->fetchColumn()) {
			$this->allPurchases[] = intval($row->id);
		}

		return $this->allPurchases;
	}

	/**
	 * Создание нового заказа
	 */
	public function CreatePurchase() {
		if ($this->GetUserReg() == 'reg') {
			$userID = $this->auth->GetUserID();
			$this->db->SQL("INSERT INTO shop_purchases SET user_reg = '{$this->userReg}', user_id = {$userID}, sum = 0, total = 0, state = 'raw', time = NOW()");
		} else {
			$sid = $this->auth->GetSID();
			$this->db->SQL("INSERT INTO shop_purchases SET user_reg = '{$this->userReg}', session_id = '{$sid}', sum = 0, total = 0, state = 'raw', time = NOW()");
		}

		return $this->db->GetLastID();
	}

	/**
	 * Определяем, зарегистрирован ли пользователь?
	 */
	public function GetUserReg() {
		return $this->userReg;
	}
	
	/**
	 * Добавление товара в корзину
	 */
	public function AddItem($dt, $docID, $title, $price, $number, $subdocID = 0, $subdocTitle = '') {
		// Отсекаем опредёлённых пользователей
		if (!$this->CheckUser($dt, $docID)) {
			return false;
		}

		if (!$this->purchaseID) {
			$this->purchaseID = $this->CreatePurchase();
		}

		$this->db->Begin();
		if ($subdocID == 0) {
			// Есть такая единица продукции в корзине?
			$ret = $this->db->GetValue("
				SELECT number FROM shop_items WHERE doc_id = {$docID} AND purchase_id = {$this->purchaseID}
			");
			if ($ret) {
				$result = $this->db->SQL("
					UPDATE
						shop_items
					SET
						number = {$number} + {$ret}
					WHERE
						purchase_id = {$this->purchaseID} AND dt = '{$dt}' AND doc_id = {$docID}
				");
			} else {
				$result = $this->db->SQL("
					INSERT INTO
						shop_items
					SET
						purchase_id = {$this->purchaseID}, dt = '{$dt}', doc_id = {$docID},
						title = '{$title}',	price = {$price}, number = {$number}
				");
			}
		} else {
			$result = $this->db->SQL("
				INSERT INTO
					shop_items
				SET
					purchase_id = {$this->purchaseID}, dt = '{$dt}',
					doc_id = {$docID}, title = '{$title}',
					price = {$price}, number = {$number},
					subdoc_id = {$subdocID}, subdoc_title = {$subdocTitle}
			");
		}
		if (!$result) {
		    $this->db->Rollback();
		    return false;
		}

		$this->UpdatePrice();
		$this->db->Commit();
		return true;
	}

	public function UpdatePrice() {
		$this->db->SQL("
			SELECT
				@sum := SUM(price*number) AS sum, @number := SUM(number) AS number
			FROM
				shop_items 
			WHERE 
				purchase_id = {$this->GetPurchaseID()}
		");

		$set = $this->GetUpdatePriceParams();

		$this->db->SQL("
			UPDATE
				shop_purchases
			SET
				{$set}
			WHERE
				id = {$this->GetPurchaseID()} AND state = 'raw'
		");

		return;
	}

	/**
	 * Создание ноды, содержащей краткую информацию
	 * о корзине.
	 * 
	 * @param int $confirmed
	 * для заказа, находящегося в неподтверждённом состоянии $confirmed = 0
	 * 
	 * @return DOMDocument
	 */
	public function CreateSummaryNode($xml, $parentNode, $confirmed = 0) {
		$sql = "SELECT s.id AS 'id', s.sum AS 'sum', s.total AS 'total', s.number AS 'number' FROM shop_purchases s";

		$cartItems = $this->GetInfoSummary($sql, $confirmed);

		$cartNode = X_CreateNode($xml, $parentNode, "cart");
		if ($cartItems) {
			$cartNode->setAttribute('id', $cartItems->id);
			$sum = ($this->conf->Param("numberFormatInCartSummary")) ? number_format($cartItems->sum, 0, '.', '') : $cartItems->sum;
			$cartNode->setAttribute('sum', $sum);
			$total = ($this->conf->Param("numberFormatInCartSummary")) ? number_format($cartItems->total, 0, '.', '') : $cartItems->total;
			$cartNode->setAttribute('total', $total);
			$cartNode->setAttribute('number', $cartItems->number);
		}
		return $cartNode;
	}

	public function DeleteAll() {
		return $this->db->SQL("DELETE FROM shop_purchases WHERE id = {$this->purchaseID}");
	}

	/**
	 * Данный метод изменяет состояние корзины покупателя с 'unreg'
	 * на 'reg' при авторизации покупателя в интернет-магазине.
	 */
	public function Merge($userId) {
		$userSID = $this->auth->GetSID();

		if (!$newOrder = $this->GetPurchaseID()) {
			return true;
		}

		$oldOrder = $this->db->GetValue("SELECT p.id FROM shop_purchases p WHERE p.user_id = '{$userId}' AND p.state = 'raw'");

		if ($oldOrder) {
			$this->db->Begin();
			
			$ret1 = $this->db->SQL("
				UPDATE 
					shop_purchases 
				SET 
					session_id = '', user_reg = 'reg', user_id = {$userId} 
				WHERE 
					session_id = '{$userSID}'
			");
			$ret2 = $this->db->SQL("UPDATE shop_items SET purchase_id = {$newOrder} WHERE purchase_id = {$oldOrder}");
			$ret3 = $this->db->SQL("DELETE FROM shop_purchases WHERE id = '{$oldOrder}'");
			
			if (!$ret1 || !$ret2 || !$ret3) {
				$this->db->Rollback();
				return false;
			}
			
			$this->UpdatePrice();
			
			$this->db->Commit();
		} else {
			$this->db->SQL("
				UPDATE
					shop_purchases 
				SET 
					session_id = '', user_reg = 'reg', user_id = {$userId} 
				WHERE 
					session_id = '{$userSID}'
			");
		}

		return true;
	}

	/**
	 * Данный метод осуществляет проверку возможности
	 * слияния заказов в один, в случае, если пользователь первоначально
	 * находился на сайте в неавторизованном состоянии, а затем авторизовался.
	 * При этом у него существовал неоформленный заказ.
	 */
	public function MergeAllow($userId) {
		return true;
	}
	
	/**
	 * Формирование .xls файла, в котором отражается содержание заказа
	 */
	public function CreateExcelFile() {
	    return;
	}

	/**
	 * Запись подробной информации о заказе
	 */
	public function WriteFullInfo() {
		return true;
	}

	/**
	 * Определение типа цены (в случае необходимости)
	 *
	 * @param $dt – тип документа
	 * По умолчанию равен false
	 * 
	 * @param bool $all
	 * По умолчанию равен false, т.е. определение типа цены для одного пользователя (пользователь является
	 * покупателем). В противном случае (например, для администратора, осуществляющего
	 * обновление каталога) – определение типов цен для всех пользователей, осуществивших заказ.
	 */
	public function GetPriceColumnName($dt = false, $all = false) {
		return $this->price;
	}

	public function GetTitleColumnName() {
		return $this->title;
	}

	public function GetMail() {
		return $this->auth->GetUserParam("email");
	}
	
	/**
	 * Обновление информации о заказах.
	 * Потребуется, например, в случае изменения цен в каталоге.
	 */
	abstract public function UpdatePriceAll();

	abstract public function CheckUser($dt, $docID);

	/**
	 * Нужна ли выдача подробной информации о заказе в корзине,
	 * например, в случае отправки её на e-mail администратора
	 * интернет-магазина
	 */
	abstract public function IsNeededFullInfo();
	
	// Выдача подробной информации о заказе в корзине
	abstract public function GetFullInfo($xml, $parentNode, $purchase = 0);
	
	abstract public function ParamsExist();
	
	protected function SetUserReg() {
		$this->userReg = ($this->auth->GetUserID() > 0) ? 'reg' : 'unreg';
	}
	
	/**
	 * @param string $sql
	 * строка запроса, содержащая, выбираемые поля и таблицу, из которой осуществляется выборка.
	 * @param int $confirmed
	 * 
	 * @return object
	 */
	protected function GetInfoSummary($sql, $confirmed) {
		if ($confirmed == 0) {
			if ($this->GetUserReg() == 'reg') {
				$userID = $this->auth->GetUserID();
				$infoItems = $this->db->GetRow($sql . " WHERE s.user_id = '{$userID}' AND s.state = 'raw'");
			} else {
				$sid = $this->auth->GetSID();
				$infoItems = $this->db->GetRow($sql . " WHERE s.session_id = '{$sid}' AND s.state = 'raw'");
			}
		} else {
			if ($this->GetUserReg() == 'reg') {
				$userID = $this->auth->GetUserID();
				$infoItems = $this->db->GetRow($sql . " WHERE s.user_id = '{$userID}' AND s.id = '{$confirmed}'");
			} else {
				$sid = $this->auth->GetSID();
				$infoItems = $this->db->GetRow($sql . " WHERE s.session_id = '{$sid}' AND s.id = '{$confirmed}'");
			}
		}

		return $infoItems;
	}
	
	/**
	 * Определение параметров запроса для обновления стоимости
	 * заказа
	 */
	protected function GetUpdatePriceParams() {
		return "sum = @sum, total = @sum, number = @number";
	}
	
	protected function GetPurchaseParams() {
		return array();
	}
	
	protected function SetPriceColumnName() {
		$this->price = "price";
	}

	protected function SetTitleColumnName() {
		$this->title = "title";
	}
	
	private function Initialize() {
		if ($this->GetUserReg() == 'reg') {
			$userID = $this->auth->GetUserID();
			$row = $this->db->GetRow("SELECT id, canSubmit, minPrice FROM shop_purchases WHERE user_id = {$userID} AND state = 'raw'");
		} else {
			$sid = $this->auth->GetSID();
			$row = $this->db->GetRow("SELECT id, canSubmit, minPrice FROM shop_purchases WHERE session_id = '{$sid}' AND state = 'raw'");
		}

		if ($row) {
			$this->purchaseID = $row->id;
			$this->minPrice = $row->minPrice;
			$this->canSubmit = $row->canSubmit;
		}
	}
	
	/**
	 * Определение пути для изображения в каталоге продукции
	abstract public function GetImagePath($type, $name);
	 */

}

?>