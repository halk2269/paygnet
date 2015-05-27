<?php
/**
 * @deprecated
 */
class MagicQuotesClass {

	public function KillMagicQuotes() {
		$magicquotes = ini_get("magic_quotes_gpc");
		if ($magicquotes == 1) {
			$_GET = stripslashes_deep($_GET);
			$_POST = stripslashes_deep($_POST);
			$_COOKIE = stripslashes_deep($_COOKIE);
			
			foreach ($_FILES as $idx => $val) {
				$_FILES[$idx]["name"] = stripslashes($val["name"]);
			}
		}
	}

}

?>