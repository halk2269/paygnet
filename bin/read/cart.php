<?php

require_once(CMSPATH_PLIB . $this->conf->Param("EshopCommonPath"));

class CartInfoReadClass extends ReadModuleBaseClass {
	
	private $eshop;
		
	public function CreateXML() {
		$this->eshop = $this->createEshop();
				
		$this->eshop->CreateSummaryNode($this->xml, $this->parentNode);
		
		return true;
	}
	
	private function createEshop() {
		$className = $this->conf->Param('EshopCommonClassName');
		
		return new $className();
	}

}

?>