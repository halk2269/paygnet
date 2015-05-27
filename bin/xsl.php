<?php
/**
 * Класс, ответственный за генерацию XSL
 */
class XSLClass {
	/**
	 * Класс работы с кэшем
	 * @var CacheClass
	 */
	var $cache;
	
	private $xslList;

	public function __construct($xslList) {
		$this->xslList = $xslList;
		$this->cache = CacheClass::GetInstance();
	}

	public function GetXSL() {
		$result = '';
		$i = 0;
				
		while ($i < count($this->xslList)) {
			$isFirst = ($i == 0);
			$result .= $this->cache->GetXSLFile(
				GenPath($this->xslList[$i]["filename"], CMSPATH_XSLT, CMSPATH_PXSLT), 
				$this->xslList[$i]["match"], 
				$isFirst
			);
			$i++;
		}
		$result .= '</xsl:stylesheet>';
		
		return $result;
	}

}

?>