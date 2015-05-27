<?php

require_once(CMSPATH_LIB . "auth/session.php");
require_once(CMSPATH_LIB . "auth/usermapper.php");

/**
 * Класс авторизации
 */
class AuthClass {
	
	/**
	 * @private SessionClass
	 */
	public $session;
	
	/**
	 * @private AuthUser
	 */
	public $user;
	
	/**
	 * @var DBClass
	 */
	private $db;
	
	/**
	 * @private GlobalConfClass
	 */
	private $conf;
	
	/**
	 * @private IPBSDK
	 */
	private $IPB;
	
	/**
	 * @private UserMapper
	 */
	private $mapper;
	
	private static $instance;
	
	public function __construct() {
		$this->db = DBClass::GetInstance();
		$this->conf = GlobalConfClass::GetInstance();
		$this->session = new SessionClass();
		$this->mapper = new UserMapper();
		
		$this->Authorize();
	}
	
	static public function GetInstance() {
		if (!self::$instance instanceof AuthClass) {
			self::$instance = new AuthClass();
		}
		
		return self::$instance;
	}

	/**
	 * Авторизуем пользователя по информации из сессии
	 */
	public function Authorize() {
		// Если скрипт запускается не из-под веб-сервера, то у нас всегда неавторизованный юзер
		if (!isset($_SERVER["REMOTE_ADDR"])) {
			$this->user = $this->mapper->GetUnAuthUser();
			return;
		}
		
		$this->session->Authorize();
	
		if ($this->session->IsUserAuthorized()) {
			$this->user = $this->mapper->GetUserByIdAndLoginAndRole(
				$this->session->GetParam("uid"), 
				$this->session->GetParam("ulogin"), 
				$this->session->GetParam("role_name"), 
				$this->session->GetParam("role_id")
			);

			if (is_null($this->user)) {
				$this->user = $this->mapper->GetUnAuthUser();
				$this->session->UnAuthorize();
			}
		} else {
			$this->user = $this->mapper->GetUnAuthUser();
		}

		$this->session->Serialize();
	}

	/**
	 * @param string $login
	 * @param string $pass
	 * @return int
	 */
	public function LogIn($login, $pass) {
		$this->session->CleanUpExceptIp();

		$this->user = $this->mapper->GetUserByLoginAndPass($login, $pass);
		if (is_null($this->user)) {
			return false;
		}

		$this->session->Restart();
		$this->session->Fill(
			$this->user->GetUserID(), 
			$this->user->GetUserLogin(), 
			$this->user->GetRoleID(), 
			$this->user->GetRoleName()
		);

		if ($this->conf->Param("IPBIntegration")) {
			// отключаем собственный обработчик ошибок
			SwitchErrorHandler(); 
			// пытаемся логиниться на форум
			$this->IPB->login($login, $pass);
			// включаем собственный обработчик ошибок
			SwitchErrorHandler();
		}

		return $this->GetUserID();
	}

	/**
	 * Разлогиниваем пользователя
	 */
	public function LogOff() {
		$this->session->Destroy();

		if ($this->conf->Param("IPBIntegration")) {
			SwitchErrorHandler(); // отключаем собственный обработчик ошибок
			$this->IPB->logout();
			SwitchErrorHandler(); // включаем собственный обработчик ошибок
		}
	}

	public function IsAuth() {
		return $this->user->IsAuth();
	}

	public function GetUserID() {
		return $this->user->GetUserID();
	}

	public function GetUserLogin() {
		return $this->user->GetUserLogin();
	}

	public function GetUserParam($paramName) {
		return $this->user->GetUserParam($paramName);
	}

	public function GetRoleID() {
		return $this->user->GetRoleID();
	}

	public function GetRoleName() {
		return $this->user->GetRoleName();
	}

	public function GetRoleTitle() {
		return $this->user->GetRoleTitle();
	}

	public function GetDefSectionRights() {
		return $this->user->GetDefSectionRights();
	}

	public function GetDefRights() {
		return $this->user->GetDefRights();
	}

	public function IsDTSuperAccess() {
		return $this->user->IsDTSuperAccess();
	}

	public function CanEditLists() {
		return $this->user->CanEditLists();
	}

	public function CanUploadFiles() {
		return $this->user->CanUploadFiles();
	}

	public function GetSID() {
		return $this->session->GetId();
	}

	public function GetSectionRights($secID) {
		$roleID = $this->GetRoleID();
		$rightsValue = $this->db->GetValue(
			"SELECT rights FROM sys_section_rights WHERE section_id = ? AND role_id = ?",
			array($secID, $roleID)
		);
		
		$rightsStr = ($rightsValue) ? $rightsValue : $this->GetDefSectionRights();
		if (strlen($rightsStr) != 6) {
			$rightsStr = "000000";
		}
		
		// Расчленяем строку прав на логические переменные
		$rights["Read"] = ($rightsStr[0] == 1);
		$rights["Create"] = ($rightsStr[1] == 1);
		$rights["Edit"] = ($rightsStr[2] == 1);
		$rights["Delete"] = ($rightsStr[3] == 1);
		$rights["EditName"] = ($rightsStr[4] == 1);
		$rights["EditEnabled"] = ($rightsStr[5] == 1);
		
		return $rights;
	}

	public function GetRefRights($refID, &$adminMode) {
		$roleID = $this->GetRoleID();
		$rightsValue = $this->db->GetValue(
			"SELECT rights FROM sys_ref_rights WHERE ref_id = ? AND role_id = ?",
			array($refID, $roleID)
		);
		
		$rightsStr = ($rightsValue) ? $rightsValue : $this->GetDefRights();
		if (strlen($rightsStr) != 5) {
			$rightsStr = "00000";
		}
		
		// Расчленяем строку прав на логические переменные
		$rights["Read"] = ($rightsStr[0] == 1);
		$rights["Create"] = ($rightsStr[1] == 1);
		$rights["CreateEnabled"] = ($rightsStr[2] == 1);
		$rights["Edit"] = ($rightsStr[3] == 1);
		$rights["Delete"] = ($rightsStr[4] == 1);
		
		$adminMode = ($rights["CreateEnabled"] || $rights["Edit"] || $rights["Delete"]);
		
		return $rights;
	}

	/**
	 * @param IPBSDK $IPB
	 */
	private function _SetIPBHandle($IPB) {
		$this->IPB = $IPB;
	}
}

?>