<?php 
require_once(CMSPATH_LIB . "logging/useractionlogging.php");

class UserActionsReadClass extends ReadModuleBaseClass {

	var $result;

	var $startInterval;
	var $endInterval;

	var $firstAction;
	
	var $usersRef;

	function CreateXML() {
		$this->_SetIntervals();
		$this->_SetFirstAction();
		
		$logger = new UserActionLogging();
		$this->result = $logger->GetActions($this->startInterval, $this->endInterval);

		$this->_CreateActionNodes();
		$this->_CreateIntervalNode();
		$this->_CreateUsersRefNode();

		return true;
	}

	function _CreateActionNodes() {
		$actions = X_CreateNode($this->xml, $this->parentNode, "actions");
		foreach ($this->result as $action) {
			$node = X_CreateNode($this->xml, $actions, "action");
			$node->setAttribute("action", $action["action"]);
			$node->setAttribute("type", $action["type"]);
			$node->setAttribute("time", $action["time"]);
			$node->setAttribute("userId", $action["user_id"]);
			$node->setAttribute("userLogin", $action["user_login"]);
			$node->setAttribute("roleId", $action["user_role_id"]);
			if (1 == $action["type"]) {
				$node->setAttribute("sectionId", $action["section_id"]);
				$node->setAttribute("sectionTitle", $action["section_title"]);
			} else {
				$node->setAttribute("documentId", $action["doc_id"]);
				$node->setAttribute("docType", $action["doc_type"]);
			}
		}
	}

	function _CreateIntervalNode() {
		$startInterval = X_CreateNode($this->xml, $this->parentNode, "startInterval");
		$startInterval->setAttribute("year", date("Y", $this->startInterval));
		$startInterval->setAttribute("month", date("m", $this->startInterval));
		$startInterval->setAttribute("day", date("d", $this->startInterval));
		$endInterval = X_CreateNode($this->xml, $this->parentNode, "endInterval");
		$endInterval->setAttribute("year", date("Y", $this->endInterval));
		$endInterval->setAttribute("month", date("m", $this->endInterval));
		$endInterval->setAttribute("day", date("d", $this->endInterval));

		if ($this->firstAction > 0) {
			$firstAction = X_CreateNode($this->xml, $this->parentNode, "firstAction");
			$firstAction->setAttribute("year", date("Y", $this->firstAction));
			$firstAction->setAttribute("month", date("m", $this->firstAction));
			$firstAction->setAttribute("day", date("d", $this->firstAction));
		}
	}
	
	function _CreateUsersRefNode() {
		$usersRef = X_CreateNode($this->xml, $this->parentNode, "usersRef");
		$usersRef->setAttribute("ref", $this->db->GetValue("SELECT ref FROM dt_user WHERE id = 2"));
	}

	function _SetIntervals() {
		if (!preg_match("~^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$~i", $this->_GetSimpleParam("start"), $start)) {
			$this->_SetDefaultIntervals();
			return;
		}

		if (!preg_match("~^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$~i", $this->_GetSimpleParam("end"), $end)) {
			$this->_SetDefaultIntervals();
			return;
		}

		SwitchErrorHandler();
		$this->startInterval = mktime(0, 0, 0, $start[2], $start[3], $start[1]);
		$this->endInterval = mktime(23, 59, 59, $end[2], $end[3], $end[1]);
		SwitchErrorHandler();

		if (-1 == $this->startInterval or -1 == $this->endInterval or $this->endInterval < $this->startInterval) {
			$this->_SetDefaultIntervals();
			return;
		}
	}

	function _SetFirstAction() {
		$this->firstAction = (int)$this->db->GetValue("SELECT UNIX_TIMESTAMP(time) AS time FROM user_actions ORDER BY time ASC LIMIT 1");
	}

	function _SetDefaultIntervals() {
		$this->startInterval = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$this->endInterval = mktime(23, 59, 59, date("m"), date("d"), date("Y"));
	}

}

?>