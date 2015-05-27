<?php

require_once(CMSPATH_LIB . "auxil/findvectorsdiff.php");

class LinksValidator {

	private $error = false;

	private $numLinksInDB = 0;
	private $numLinksPassed = 0;
	
	private $linksToInsert = array();
	private $linksToDelete = array();
	private $linksInDB = array();
	private $linksPassed = array();

	/**
	 * @private QueryClass
	 */
	private $query;
	/**
	 * @private DocumentValidator
	 */
	private $documentValidator;

	private $conf;
	
	/**
	 * @private DBClass
	 */
	private $db;


	public function __construct($documentValidator, $query, $conf) {
		$this->query = $query;
		$this->documentValidator = $documentValidator;
		$this->conf = $conf;
		
		$this->db = DBClass::GetInstance();
		
		$this->_SetLinksInDB();
		
		if (!$this->_IsModified()) {
			return;
		}
		
		$this->_SetPassedLinks();
		
		$vectorDiff = new FindVectorsDiffClass($this->linksInDB, $this->linksPassed);
		
		$this->linksToInsert = is_array($vectorDiff->GetItemsForInsert()) ? $vectorDiff->GetItemsForInsert() : array();
		$this->linksToDelete = is_array($vectorDiff->GetItemsForDelete()) ? $vectorDiff->GetItemsForDelete() : array();
	}

	function GetError() {
		return $this->error;
	}

	function GetNumLinksInDB() {
		return $this->numLinksInDB;
	}

	function GetNumLinksPassed() {
		return $this->numLinksPassed;
	}

	function GetNumLinksToDelete() {
		return sizeof($this->linksToDelete);
	}

	function GetNumLinksToInsert() {
		return sizeof($this->linksToInsert);
	}
	
	function GetLinksToDelete() {
		return $this->linksToDelete;
	}
	
	function GetLinksToInsert() {
		return $this->linksToInsert;
	}
	
	function GetLinksInDB() {
		return $this->linksInDB;
	}
	
	function GetLinksPassed() {
		return $this->linksPassed;
	}
	
	function GetNumResultingLinks() {
		return (int)($this->GetNumLinksInDB() + $this->GetNumLinksToInsert() - $this->GetNumLinksToDelete());
	}
		
	function _SetLinksInDB() {
		$fromId = $this->query->GetParam("id");
		if (!$fromId) {
			$this->numLinksInDB = 0;
			return;
		}
		
		$linkedTable = "link_" . $this->documentValidator->GetDocTypeName() . "_" . $this->conf["doct"];
		
		$itemsInStmt = $this->db->SQL(
			"SELECT to_id FROM {$linkedTable} WHERE from_id = ?",
			array($fromId)
		);
		
		while ($row = $itemsInStmt->fetchObject()) {
			$this->linksInDB[] = (int)$row->to_id;
		}
		
		$this->numLinksInDB = $itemsInStmt->rowCount();
	}
	
	function _IsModified() {
		return ($this->query->GetParam("links_" . $this->conf["doct"] . "_ismodified"));
	}
	
	function _SetPassedLinks() {
		$linksInArray = explode(";", $this->query->GetParam("links_" . $this->conf["doct"] . "_selectedids"));
		foreach ($linksInArray as $link) {
			$this->linksPassed[] = (int)$link;
		}
		
		$this->numLinksPassed = sizeof($this->linksPassed);
	}

}

?>