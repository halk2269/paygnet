<?php

class UserActionLogging {
	/**
	 * @var AuthClass
	 */
	private $auth;

	/**
	 * @var DBClass
	 */
	private $db;

	/**
	 * @var DTConfClass
	 */
	private $dtconf;

	/**
	 * @var array
	 */
	private $userActions = array();

	public function __construct() {
		$this->auth = AuthClass::GetInstance();
		$this->db = DBClass::GetInstance();
		$this->dtconf = DTConfClass::GetInstance();
	}

	public function AddDocumentAction($dtName, $docId, $action) {
		if (!isset($this->dtconf->dtf[$dtName])) {
			return false;
		}
		
		if (!IsGoodId($docId)) {
			return false;
		}
		
		if (!trim($action)) {
			return false;
		}
		
		$this->db->SQL(
			"INSERT INTO 
				user_actions (user_id, user_login, time, type, doc_id, doc_type, action) 
			VALUES 
				(?, ?,  NOW(), 0, ?, ?, ?)",
			array(
				$this->auth->GetUserID(), $this->auth->GetUserLogin(), $docId, $dtName, $action
			)
		);
		
		return true;
	}

	public function AddSectionAction($sectionId, $sectionTitle, $action) {
		if (!IsGoodId($sectionId)) {
			return false;
		}
		
		if (!trim($sectionTitle)) {
			return false;
		}
		
		if (!trim($action)) {
			return false;
		}
		
		$this->db->SQL(
			"INSERT INTO 
				user_actions (user_id, time, type, section_id, section_title, action) 
			VALUES 
				(?, NOW(), 1, ?, ?, ?)",
			array($this->auth->GetUserID(), $sectionId, $sectionTitle, $action)
		);
		
		return true;
	}

	public function GetActions($timeStart, $timeEnd) {
		if (0 == sizeof($this->userActions)) {
			$this->_SetUserActions($timeStart, $timeEnd);
		}
		
		return $this->userActions;
	}
	
	public function GetActionsByUser($userId, $timeStart, $timeEnd) { }

	public function GetNumActions() { }
	
	public function GetNumActionByUser($userId) { }

	protected function _SetUserActions($timeStart, $timeEnd) {
		if ($timeStart >= $timeEnd) {
			return;
		}
		
		$timeStart = date("Y-m-d H:i:s", $timeStart);
		$timeEnd = date("Y-m-d H:i:s", $timeEnd);
		
		$stmt = $this->db->SQL("
			SELECT
				IF(u.id IS NULL, 0, u.id)  AS user_id,
				IF(u.id IS NULL, ua.user_login, u.login) AS user_login,
				IF(u.id IS NULL, 0, u.role_id) AS user_role_id,
				ua.time AS time,
				ua.type AS type,
				ua.action AS action,
				ua.doc_type AS doc_type,
				ua.doc_id AS doc_id,
				ua.section_id AS section_id,
				ua.section_title AS section_title  
			FROM 
				user_actions ua 
				LEFT JOIN dt_user u ON u.id = ua.user_id
			WHERE 
				ua.time >= '{$timeStart}' 
				AND ua.time <= '{$timeEnd}'
			ORDER BY 
				ua.time DESC
		");
		
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$this->userActions[] = $row;
		}
	}
}

?>