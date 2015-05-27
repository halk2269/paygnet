<?php

require_once(CMSPATH_BIN . "uribase.php");
/**
 * Переписывание адресов
 * 
 * @author IDM
 */

class URIModifyClass extends URIModifyBaseClass {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function DoModify(&$SCName) {
		if (preg_match("/^get(file|image)(\/([0-9a-f]{32}))?\/([0-9]{1,12})\.(.*)$/", $SCName, $matches)) {
			$SCName = "getf";
			$_GET["type"] = $matches[1];
			$_GET["hash"] = $matches[3];
			$_GET["id"] = $matches[4];
			$_GET["ext"] = $matches[5];
			return true;
		}
		return false;
	}
	
}

?>