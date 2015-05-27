<?php

/**
 * Выдаёт XML для опросника
 * @author IDM
 */

class InquirerReadClass extends ReadModuleBaseClass {

	public function CreateXML() {
		// Переменные из $this->params
		$selectRefID = 0;

		eval($this->params);
		if (!$selectRefID || !IsGoodNum($selectRefID)) {
			$this->_SetBadParamsDescr("Bad \$selectRefID");
			return false;
		}
		
		$ip = $_SERVER["REMOTE_ADDR"];

		$sqlQuery = "
			SELECT 
				dt.id, dt.question, dt.answers, s.text 
			FROM 
				dt_inquirer dt 
			LEFT JOIN 
				sys_dt_strlist s ON dt.answers = s.id 
			WHERE 
				dt.enabled = 1 AND dt.ref = {$selectRefID} AND dt.closed = 0 
			ORDER BY 
				RAND() 
			LIMIT 1
		";
		$stmt = $this->db->SQL($sqlQuery);

		while ($row = $stmt->fetchObject()) {
			$newDoc = $this->xml->createElement("document");
			$newDoc->setAttribute("id", $row->id);
			$newDoc->setAttribute("docTypeName", "inquirer");

			$stmt = $this->db->SQL("
				SELECT time FROM inquirer_votes WHERE question_id = {$row->id} AND ip = '{$ip}' AND (NOW() - '00000001000000' < time)
			");
			$newDoc->setAttribute("userVoted", ($stmt->rowCount() < 1) ? 0 : 1);
			
			$newField = $this->xml->createElement("field", $row->question);
			$newField->setAttribute("name", "question");
			
			$newDoc->appendChild($newField);

			$newField = $this->xml->createElement("field");
			$newField->setAttribute("name", "answers");
			$answers = explode("\r\n", $row->text);
			
			$i = 1;
			foreach ($answers as $line) {
				$lineNode = $this->xml->createElement("line");
				$lineNode->setAttribute("num", $i);
				
				if (preg_match("~^([0-9]{1,8})\s*:\s*~", $line, $matches)) {
					$line = preg_replace("~^([0-9]{1,8})\s*:\s*~", "", $line);
					$lineNode->setAttribute("votes", $matches[1]);
				} else {
					$lineNode->setAttribute("votes", 0);
				}
				
				$txt = $this->xml->createTextNode($line);
				$lineNode->appendChild($txt);
				
				$newField->appendChild($lineNode);
				$i++;
			}
			
			$newDoc->appendChild($newField);
			$this->parentNode->appendChild($newDoc);
		}
		
		return true;
	}

	static public function Clean($thisID, $params) {
		$this->db->SQL("
			DELETE FROM 
				inquirer_votes 
			USING 
				inquirer_votes, dt_{$inDTName} 
			WHERE 
				question_id IN (SELECT id FROM dt_{$inDTName} d WHERE ref = {$thisID})
		");
		
		parent::Clean($thisID, $params);
	}

}

?>