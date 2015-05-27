<?php

/**
 * Модуль чтения документов для RSS.
 * RSS отвечает спецификации R 2.0
 * @todo it must be refactored
 */

class RSSReadClass extends ReadModuleBaseClass {

	public function CreateXML() {

		$inDTName = "";
		$inSelectRef = "";
		$inFieldDate = "";
		$inFieldTitle = ""; 	// при выводе применяется XMLEntities() из модуля auxil.php
		// Optional parameters
		$inConstraints = "no constraints"; // WHERE part of the query
		$inLimit = 10; // max count of the selected items
		$inOrder = "default order"; // ORDER BY part of the query. If strtolower($inOrder) == "RAND()", it'll be replaced with "default order" in admin mode
		$inSiteAddress = "";
		$inSiteDescription = "";
		$inNewsText = "News from our site";
		$inFieldDescr = "descr"; // Применяется XMLEntities()
		/* Local vars */
		$dateFormatString = "D, d M Y H:i:s O"; // такой формат даты предусмотрен спецификацией RSS 2.0

		eval($this->params);

		if (
			$inDTName == "" 
			|| $inSelectRef == "" 
			|| $inFieldDate == "" 
			|| $inFieldTitle == ""
		) {
			$this->_SetBadParamsDescr("Blank \$inDTName or \$inTargetSection or \$inSelectRef or \$inFieldDate or \$inFieldTitle");
			return false;
		}

		$xml = $this->xml;
		$parentNode = $this->parentNode;

		$rssMainNode = $xml->create_element("rss");
		$rssMainNode->set_attribute("version", "2.0");
		$rssNode = $xml->create_element("channel");
		/* required */
		$this->_AddTextNode($xml, $rssNode, "title", XMLEntities($inNewsText));
		$this->_AddTextNode($xml, $rssNode, "link", $inSiteAddress);
		$this->_AddTextNode($xml, $rssNode, "description", XMLEntities($inSiteDescription));
		/* optional */
		$this->_AddTextNode($xml, $rssNode, "language", "ru-RU");
		$this->_AddTextNode($xml, $rssNode, "copyright", "");
		$this->_AddTextNode($xml, $rssNode, "pubDate", date($dateFormatString, time()));
			
		$this->_AddTextNode($xml, $rssNode, "generator", "");
		$this->_AddTextNode($xml, $rssNode, "docs", "http://blogs.law.harvard.edu/tech/rss");

		$targetSection = "";

		// Выборка идёт не из родной связи
		if ($inSelectRef != "own") {
			// Целевая
			$stmt = $this->db->SQL("
				SELECT 
					s.name AS sectionname 
				FROM 
					sys_references r 
				LEFT JOIN 
					sys_sections s ON r.ref = s.id 
				WHERE 
					r.id = {$inSelectRef} and r.enabled = 1
			");
						
			if (!$stmt->rowCount()) {
				$this->_SetBadParamsDescr(
					"There is no target reference width id = '{$inSelectRef}' or this reference is not enabled"
				);
				return false;
			}
			
			$targetSection = $stmt->fetchColumn();			
		}

		// Выбираем документы, относящиеся к этой связи
		$selectRefID = ($inSelectRef == "own") ? $this->thisID : $inSelectRef;
		$enab = "and enabled = 1";
		$query = "SELECT id, {$inFieldDate}, {$inFieldTitle}, {$inFieldDescr} FROM dt_{$inDTName}";
		// WHERE expression
		$where = ($inConstraints != "no constraints") ? " WHERE ref = '{$selectRefID}' {$enab} and {$inConstraints}" : " WHERE ref = '{$selectRefID}' {$enab}";
		// LIMIT expression
		if ($inLimit != "no limit" and !IsGoodNum($inLimit)) {
			$this->_SetBadParamsDescr("Bad parameter: \$inLimit. It must be INT value");
			return false;
		}
		$limit = ($inLimit != "no limit") ? " LIMIT {$inLimit}" : "";
		// ORDER BY expression
		$order = ($inOrder != "default order") ? " ORDER BY {$inOrder}" : "";
		// Добавляем к запросу WHERE
		$query .= $where;
		$query .= $order;
		$query .= $limit;
		
		$stmt = $this->db->SQL($query);
		while ($row = $stmt->fetchObject()) {
			$itemNode = $xml->create_element("item");
			$this->_AddTextNode($xml, $itemNode, "title", XMLEntitiesWithoutAmp($row[$inFieldTitle]));
			$this->_AddTextNode(
				$xml, 
				$itemNode, 
				"link", 
				"http://" . $_SERVER["HTTP_HOST"] . $this->conf->Prefix . $targetSection . "/?r" . $selectRefID . "_id=" . $row["id"]
			);
			
			$this->_AddTextNode($xml, $itemNode, "description", XMLEntitiesWithoutAmp($row[$inFieldDescr]));
			$this->_AddTextNode($xml, $itemNode, "pubDate", date($dateFormatString, strtotime($row[$inFieldDate])));
			
			$rssNode->appendChild($itemNode);						
		}

		$rssMainNode->appendChild($rssNode);
		$parentNode->appendChild($rssMainNode);
			
		return true;
	}

	private function _AddTextNode(&$xml, &$parentNode, $name, $text) {
		$newNode = $xml->createElement($name, $text);
		$parentNode->appendChild($newNode);
	}

}

?>