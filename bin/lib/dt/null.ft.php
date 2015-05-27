<?php

require_once(CMSPATH_LIB . "dt/base.ft.php");

class NullFTClass extends BaseFTClass {

	function GetField($row, $name, $params) {
		$field = null;
		return $field;
	}
}

?>