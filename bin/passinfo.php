<?php

/**
 * Класс, отвечающий за хранение информации, получаемой от модулей записи для последующего
 * её отображения в модулях чтения.
 * Ошибки, которые старше 5 (настраиваемая опция) минут, удаляются из списка.
 */

class PassInfoClass extends BaseClass {
	
	private $user = array();
	private $sort = 1;
	
	const TYPE_ERROR = 'err';
	const TYPE_INFO = 'info';
	
	public function __construct() {
		parent::__construct();
	}

	/* Методы используются в модулях записи */
	
	public function CleanPassInfoAndVars($refID) {
		$sid = $this->auth->GetSID();
		$this->db->SQL("DELETE FROM sys_passinfo WHERE ref = {$refID} AND sid = '{$sid}'");
		$this->db->SQL("DELETE FROM sys_passvars WHERE ref = {$refID} AND sid = '{$sid}'");
	}

	public function WriteInfo($refID, $name, $descr = "") {
		$sid = $this->auth->GetSID();
				
		$this->db->SQL(
			"INSERT INTO 
				sys_passinfo (ref, sid, type, name, descr, sort) 
			VALUES 
				(?, ?, 'info', ?, ?, ?)",
			array($refID, $sid, $name, $descr, $this->sort)	
		);
		
		$this->sort++;
	}

	public function WriteError($refID, $name, $descr = "") {
		$sid = $this->auth->GetSID();
		
		$this->db->SQL(
			"INSERT INTO 
				sys_passinfo (ref, sid, type, name, descr, sort) 
			VALUES 
				(?, ?, 'err', ?, ?, ?)",
			array($refID, $sid, $name, $descr, $this->sort)	
		);
		
		$this->sort++;
	}

	public function DumpVars($refID) {
		$sid = $this->auth->GetSID();
		
		$this->DumpArray($refID, $sid, $_GET, "get");
		$this->DumpArray($refID, $sid, $_POST, "post");
		$this->DumpArray($refID, $sid, $this->user, "user");
	}

	/* Методы используются в модулях чтения */

	public function ExportInfoAndErrors($refID, DOMDocument $xml, DOMElement $parentNode) {
		$sid = $this->auth->GetSID();
		$this->ExportParts(self::TYPE_ERROR, $refID, $sid, $xml, $parentNode);
		$this->ExportParts(self::TYPE_INFO, $refID, $sid, $xml, $parentNode);
		
		$this->db->SQL("DELETE FROM sys_passinfo WHERE ref = {$refID} AND sid = '{$sid}'");
	}
	
	public function ExportVars($refID, DOMDocument $xml, DOMElement $parentNode) {
		$sid = $this->auth->GetSID();
		$stmt = $this->db->SQL("
			SELECT 
				varname, varvalue, type 
			FROM 
				sys_passvars 
			WHERE 
				ref = {$refID} AND sid = '{$sid}'
		");
		
		if (!$stmt->rowCount()) {
			return;
		}

		$varNode = $xml->createElement("vars");
		$ownVarNode = $xml->createElement("own");
		$genVarNode = $xml->createElement("general");
		$usrVarNode = $xml->createElement("user");

		while ($row = $stmt->fetchObject()) {
			$newNode = $xml->createElement('var', $row->varvalue);
			$newNode->setAttribute("name", $row->varname);
									
			switch ($row->type) {
				case "own" : {
					$ownVarNode->appendChild($newNode); 
					break;
				}
				
				case "gen" : {
					$genVarNode->appendChild($newNode); 
					break;
				}
				
				case "usr" : {
					$usrVarNode->appendChild($newNode); 
					break;
				}
			}
		}

		$varNode->appendChild($ownVarNode);
		$varNode->appendChild($genVarNode);
		$varNode->appendChild($usrVarNode);
		
		$parentNode->appendChild($varNode);
		
		$this->db->SQL("DELETE FROM sys_passvars WHERE ref = {$refID} AND sid = '{$sid}'");
	}
	
	private function DumpArray($refID, $sid, array &$array, $type) {
		$sql = "
			INSERT INTO 
				sys_passvars (ref, sid, varname, varvalue, type) 
			VALUES
				(?, ?, ?, ?, ?)
		";
		
		$defined = array($refID, $sid);
		
		foreach ($array as $idx => $val) {
			if (strlen($idx) > 255) {
				continue;
			}
						
			if ($type == "user") {
				$this->db->SQL($sql, array_merge($defined, array($idx, $val, 'usr')));
			} elseif (preg_match("~^r([0-9]){1,11}_(.*)$~", $idx, $match)) {
				if ($match[1] != $refID) {
					continue;
				}
						
				$this->db->SQL($sql, array_merge($defined, array($match[2], $val, 'own')));
			} else {
				$this->db->SQL($sql, array_merge($defined, array($idx, $val, 'gen')));
			}
		}
	}

	private function ExportParts($type, $refID, $sid, DOMDocument $xml, DOMElement $parentNode) {
		$stmt = $this->db->SQL("
			SELECT 
				name, descr 
			FROM 
				sys_passinfo 
			WHERE 
				ref = {$refID} AND sid = '{$sid}' AND type = '{$type}' ORDER BY sort
		");
		
		if (!$stmt->rowCount()) {
			return;
		}
		
		switch ($type) {
			case self::TYPE_ERROR: {
				$baseNode = $xml->createElement("error"); 
				break;
			}
			
			case self::TYPE_INFO: {
				$baseNode = $xml->createElement("info"); 
				break;
			}
		}
		
		$parentNode->appendChild($baseNode);
				
		while ($row = $stmt->fetchObject()) {
			$newNode = $xml->createElement("item", $row->descr);
			$newNode->setAttribute("name", $row->name);
			
			$baseNode->appendChild($newNode);
		}
	}

}

?>