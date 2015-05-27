<?php
/**
 * Класс авторизованного пользователя
 * @author fred
 */
class AuthUser extends UnAuthUser {

	private $userID = 0;
	private $userLogin = "";
	private $userParams = array();

	/**
	 * Enter description here...
	 *
	 * @param int $id
	 * @param string $login
	 * @param array $params параметры пользователя
	 * @param object $roleData ролевая информация
	 * @return AuthUser
	 */
	public function __construct($id, $login, $params, $roleData) {
		parent::__construct($roleData);

		$this->userID = $id;
		$this->userLogin = $login;
		$this->userParams = $params;
	}

	public function IsAuth() {
		return true;
	}

	public function GetUserID() {
		return $this->userID;
	}

	public function GetUserLogin() {
		return $this->userLogin;
	}

	public function GetUserParam($paramName) {
		if (!isset($this->userParams[$paramName])) return false;
		return $this->userParams[$paramName];
	}
}
?>