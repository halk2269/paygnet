<?php

require_once(CMSPATH_LIB . "auth/unauthuser.php");
require_once(CMSPATH_LIB . "auth/authuser.php");

/**
 * Класс, умеющий на на базе DTConf и БД создать объект пользователя
 * и вернуть его клиенту
 */
class UserMapper {
	/**
	 * @var DTConfClass
	 */
	private $dtconf;
	/**
	 * @var DBClass
	 */
	private $db;

	/**
	 * @var GlobalVarsClass
	 */
	private $globalvars;

	public function __construct() {
		$this->dtconf = DTConfClass::GetInstance();
		$this->db = DBClass::GetInstance();
		$this->globalvars = GlobalVarsClass::GetInstance();
	}

	/**
	 * Возвращает неавторизованного пользователя
	 *
	 * @return UnAuthUser
	 */
	public function GetUnAuthUser() {
		$roleData = $this->_GetRoleData($this->globalvars->GetInt("DefaultRoleID"));
		$user = new UnAuthUser($roleData);
		return $user;
	}

	/**
	 * Возвращает авторизованного пользователя 
	 * по идентификатору, логину, идентификатору роли, 
	 * имени роли
	 *
	 * @param int $userId
	 * @param string $login
	 * @param string $roleName
	 * @param int $roleId
	 * 
	 * @return AuthUser
	 */
	public function GetUserByIdAndLoginAndRole($userId, $login, $roleName, $roleId) {
		$params = $this->_GetUserParams($userId, $roleName);
		if (!is_array($params)) {
			return null;
		}

		$roleData = $this->_GetRoleData($roleId);
		if (!$roleData) {
			return null;
		}

		return new AuthUser($userId, $login, $params, $roleData);		
	}

	/**
	 * Возвращает авторизованного пользователя по логину и паролю
	 *
	 * @param string $login
	 * @param string $pass
	 * @return AuthUser
	 */
	public function GetUserByLoginAndPass($login, $pass) {
		$userData = $this->_GetUserDataForSession($login, $pass);
		if (!$userData) {
			return null;
		}

		return $this->GetUserByIdAndLoginAndRole($userData->userId, $userData->userLogin, $userData->roleName, $userData->roleId);		
	}

	/**
	 * Возвращает авторизованного пользователя по идентификатору
	 *
	 * @param int $userId
	 * @return AuthUser
	 */
	public function GetUserById($userId) {
		if (!IsGoodId($userId)) {
			return null;
		}

		$userData = $this->db->GetRow("
			SELECT 
				u.id AS userId, 
				u.login AS userLogin, 
				r.id AS roleId, 
				r.name AS roleName 
			FROM 
				dt_user u 
			JOIN 
				sys_roles r ON u.role_id = r.id 
			WHERE 
				u.id = {$userId}
		");
		if (!$userData) {
			return null;
		}
		
		return $this->GetUserByIdAndLoginAndRole($userData->userId, $userData->userLogin, $userData->roleName, $userData->roleId);
	}

	/**
	 * Параметры пользователя
	 *
	 * @param int $userId
	 * @param string $roleName
	 * @return array
	 */
	private function _GetUserParams($userId, $roleName) {
		$fields = array();
		$fields[] = "u.enabled AS `enabled`";
		$fields[] = "u.ref AS `ref`";
		$fields[] = "u.addtime AS `addtime`";
		$fields[] = "u.chtime AS `chtime`";

		if (isset($this->dtconf->dtn["user"])) {
			foreach ($this->dtconf->dtf["user"] as $fieldName => $value) {
				$fields[] = "u.{$fieldName} AS `{$fieldName}`";
			}
		}

		$join = "";
		$addDT = 'user_' . $roleName;
		
		if (isset($this->dtconf->dtn[$addDT])) {
			$join .= " JOIN dt_{$addDT} ua ON u.id = ua.id ";

			foreach ($this->dtconf->dtf[$addDT] as $fieldName => $value) {
				$fields[] = "ua.{$fieldName} AS {$fieldName}";
			}
		}

		$fieldList = implode(",", $fields);

		$userData = $this->db->SQL("
			SELECT 
				{$fieldList} 
			FROM 
				dt_user u {$join} 
			WHERE 
				u.enabled = 1 AND u.id = " . $userId
		);

		return $userData->fetch(PDO::FETCH_ASSOC);
	}

	private function _GetRoleData($roleID) {
		return $this->db->GetRow(
			"SELECT
				r.id AS roleID,
				r.name AS roleName, 
				r.title AS roleTitle, 
				r.defrights AS defRights, 
				r.defsectionrights AS defSectionRights, 
				r.dtsuperaccess AS dtSuperAccess, 
				r.listedit AS listEdit,
				r.uploadallowed AS uploadAllowed 
			FROM 
				sys_roles r
			WHERE 
				r.id = ?",
			array($roleID)
		);
	}

	private function _GetUserDataForSession($login, $pass) {
		/* Преобразуем входные данные */
		$login = mb_substr($login, 0, 40);
		$pass = mb_substr($pass, 0, 40);

		/* Проверяем корректность данных */
		return $this->db->GetRow(
			"SELECT 
				u.id AS userId, 
				u.login AS userLogin,
				r.id AS roleId,
				r.name AS roleName
			FROM 
				dt_user u
				JOIN sys_roles r ON r.id = u.role_id
			WHERE 
				u.login = ?
				AND u.pass = ? 
				AND u.enabled = 1",
			array($login, md5($pass))
		);
	}
}
?>