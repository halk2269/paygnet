<?php

/**
 * Принятие голосований с опросника
 * @author IDM
 */

class InqVotesWriteClass extends WriteModuleBaseClass {

	function MakeChanges() {
		$qid = $this->_GetParam("questionID");
		if (!$qid) {
			return false;
		}
		
		if (!IsGoodNum($qid)) {
			return false;
		}
		
		$ans = $this->_GetParam("answer");
		if (!$ans) {
			$ans = 0;
		}
		
		if (!IsGoodNum($ans)) {
			$ans = 0;
		}
		$ans--;

		$row = $this->db->GetRow("
			SELECT 
				d.answers, 
				s.text
			FROM 
				dt_inquirer d
			LEFT JOIN 
				sys_dt_strlist s ON d.answers = s.id 
			WHERE 
				d.id = '{$qid}' AND enabled = 1 AND d.closed = 0
		");
		if (!$row) {
			return false;		
		}
		
		$answers = explode("\r\n", $row->text);
		if (isset($answers[$ans])) {
			if (preg_match("/^([0-9]{1,8})\s*:\s*/", $answers[$ans], $matches) > 0) {
				$matches[1]++;
				$answers[$ans] = preg_replace("/^([0-9]{1,8})\s*:\s*/", "{$matches[1]}:", $answers[$ans]);
			} else {
				$answers[$ans] = "1:" . $answers[$ans];
			}
		}
		
		$ip = $_SERVER["REMOTE_ADDR"];
		$stmt = $this->db->SQL("
			SELECT 
				time 
			FROM 
				inquirer_votes 
			WHERE 
				question_id = {$qid} 
				AND ip = '{$ip}' 
				AND DATE_SUB(NOW(), INTERVAL 1 DAY) < time
		");
		
		if ($stmt->rowCount() < 1) {
			$answers = implode("\r\n", $answers);
		
			$this->db->SQL(
				"UPDATE 
					sys_dt_strlist 
				SET 
					text = ? 
				WHERE 
					id = ?",
				array($answers, $row->answers)
			);
			
			$this->db->SQL(
				"INSERT INTO 
					inquirer_votes (question_id, ip) 
				VALUES (?, ?)",
				array($qid, $ip)
			);
			
			return true;
		}
	}
}

?>