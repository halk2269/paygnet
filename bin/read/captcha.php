<?php

require_once(CMSPATH_LIB . "captcha/kcaptcha.php");

class CaptchaReadClass extends ReadModuleBaseClass {
	
	function CreateXML() {
		$kcapthca = new KCAPTCHA();

		$this->auth->session->SetParam("captcha", $kcapthca->getKeyString());
		$kcapthca->drawImage();
		
		return true;
	}	
}

?>