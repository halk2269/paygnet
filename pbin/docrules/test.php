<?php

require_once(CMSPATH_BIN . "docrules/abstractdocumentrule.php");

class TestDocumentRule extends AbstractDocumentRule  {
	
	function ExecuteRule() {
		if ($this->query->GetParam("ttext1") == $this->query->GetParam("ttext2")) {
			$this->error = "TestError";
		}
	}
	
}