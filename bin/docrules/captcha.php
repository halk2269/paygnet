<?php
require_once(CMSPATH_BIN . "docrules/abstractdocumentrule.php");

class CaptchaDocumentRule extends AbstractDocumentRule  {
	
	public function __construct($documentValidator, $query) {
		parent::__construct($documentValidator, $query);
	}

	public function ExecuteRule() {
		$auth = AuthClass::GetInstance();
		if (!$this->query->GetParam("captcha") or $auth->session->GetParam("captcha") != $this->query->GetParam("captcha")) {
			$this->error = "BadCaptcha";
		}
		$auth->session->DeleteParam("captcha");
	}
}

?>