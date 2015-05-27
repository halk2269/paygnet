<?php
/**
 * Класс работы с сессиями
 * @author busta
 */
class SessionClass {

	private $id = "";
	private $started = false;

	public function __construct() {}

	public function Authorize() {
		$this->_Start();
		if (!$this->_IsGoodIP()) {
			$this->UnAuthorize();
		}
		
		$this->id = session_id();
	}

	public function GetId() {
		return $this->id;
	}

	public function GetParam($paramName) {
		return (isset($_SESSION[$paramName])) ? $_SESSION[$paramName] : false;
	}

	public function SetParam($paramName, $value) {
		$this->_Start();
		$_SESSION[$paramName] = $value;
		$this->Serialize();
	}

	public function DeleteParam($paramName) {
		$this->_Start();
		unset($_SESSION[$paramName]);
		$this->Serialize();
	}

	public function IsUserAuthorized() {
		return (isset($_SESSION["uid"]) and isset($_SESSION["ulogin"]) and isset($_SESSION["role_id"]) and isset($_SESSION["role_name"]));
	}

	public function ReStart() {
		if (!$this->started) {
			$this->_Start();
		}
		
		$this->_Destroy();
		$this->_Start();
	}

	public function UnAuthorize() {
		$this->ReStart();
		session_regenerate_id();
		$this->id = session_id();
		$_SESSION["uip"] = $_SERVER["REMOTE_ADDR"];
	}

	public function Destroy() {
		if (!$this->started) {
			$this->_Start();
		}

		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 42000, '/');
		}
		$this->_Destroy();
	}

	public function Serialize() {
		session_write_close();
		$this->started = false;
	}

	public function CleanUpExceptIp() {
		if (!$this->started) {
			$this->_Start();
		}

		$uip = $_SESSION["uip"];
		$_SESSION = array();
		$_SESSION["uip"] = $uip;
	}

	public function Fill($userId, $userLogin, $roleId, $roleName) {
		$_SESSION["uid"] = $userId;
		$_SESSION["ulogin"] = $userLogin;
		$_SESSION["role_id"] = $roleId;
		$_SESSION["role_name"] = $roleName;
		$_SESSION["uip"] = $_SERVER["REMOTE_ADDR"];
	}

	private function _Start() {
		if ($this->started) {
			return;
		}
		session_name("SID");
		session_start();
		$this->started = true;
	}

	private function _Destroy() {
		$_SESSION = array();
		session_destroy();
		
		$this->started = false;
	}

	private function _IsGoodIP() {
		return (isset($_SESSION["uip"]) && ($_SESSION["uip"] == $_SERVER['REMOTE_ADDR']));
	}
}
?>