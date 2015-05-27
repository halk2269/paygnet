<?php

require_once(CMSPATH_LIB . "artlebedev/remotetypograf.php");

/**
 * Типографирование текста
 */
class TypoGraphJSClass {

	/**
	 * @var RemoteTypograf
	 */
	private $remoteTypograf;
	
	private $dataError = false;
	private $text;
	
	function GenerateJSCode() {
		if (!isset($_POST["text"]) || !$_POST["text"]) {
			$this->dataError = true;
			return false;
		}
		
		$this->text = rawurldecode($_POST["text"]);
		$this->text = preg_replace("/(<\/?)(\w+)([^>]*>)/e", "'\\1'.strtolower('\\2').'\\3'", $this->text);

		$this->remoteTypograf = new RemoteTypograf("utf-8");
		$this->remoteTypograf->htmlEntities();
		$this->remoteTypograf->br(false);
		$this->remoteTypograf->p(true);
		$this->remoteTypograf->nobr(3);
		
		return true;
	}
	
	function GetJSCode() {
		return (!$this->dataError) 
			? "text = '" . rawurlencode($this->remoteTypograf->processText($this->text)) . "';" 
			: "";
	}
	
	function GetJSContType() {
		return "plain";
	}
	
}

?>