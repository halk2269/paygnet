<?php

// файл кэша дерева секций
define("CACHE_ST_FILENAME", CMSPATH_CACHE_XML . "tree.xml"); 

class CacheClass {
	
	/**
	 * Обработка ошибок
	 * @var ErrorClass
	 */
	private $error;
	
	/**
	 * Главный класс конфигурации
	 * @var GlobalConfClass
	 */
	private $conf;
	
	static private $instance; 

	static public function GetInstance() {
		if (!self::$instance instanceof CacheClass) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}

	public function __construct() {
		$this->error = ErrorClass::GetInstance();
		$this->conf = GlobalConfClass::GetInstance();
	}

	/** 
	 * Возвращает файл с именем $fileName из кэша. 
	 * Eсли такого файла в кэше не было, то он преобразовывается
	 * и записывается в таком виде в кэш. $isFirst - указывает, что переданные параметры отвечают главному шаблону
	 * секции.
	 */
	public function GetXSLFile($fileName, $match, $isFirst = false) {
		$genFile = false;
		$result = '';
		
		$md5FileName = CMSPATH_CACHE_XSLT . md5($fileName . $match) . ".xslt";;
		// если такой файл уже есть в кэше
		if (file_exists($md5FileName) && !$this->conf->Param("DontCacheXSLT")) {
			// сравниваем даты последней модификации файлов
			$statCachedFile = stat($md5FileName);
			$statFile = stat($fileName);
			
			// если дата модификации задаваемого файла позднее даты файла из кэша
			if ($statCachedFile["mtime"] < $statFile["mtime"]) {
				// генерируем новый файл, записываем его в кэш и возвращаем как результат работы функции
				$genFile = true;
			} else {
				// читаем файл и выдаем его
				return file_get_contents($md5FileName);
			}
		} else {
			$genFile = true;
		}

		// файла нет в кэше
		if ($genFile) {
			$result = file_get_contents($fileName);
			if (!$result) {
				$this->error->StopScript("CacheClass", "Can`t load XSLT template from $fileName");
			}
			$this->_CutXSLT($result, $match, $isFirst);

			// генерируем новый файл, записываем его в кэш и возвращаем как результат работы функции
			$fileHandle = fopen($md5FileName, 'w');
			if ($fileHandle) {
				fwrite($fileHandle, $result);
				fclose($fileHandle);
			} else {
				$this->error->StopScript("CacheClass", "Can`t create and save XSLT template to $md5FileName");
			}
		}

		return $result;
	}

	/**
	 * Удаление кэшированных XSLT-файлов
	 *
	 * @return bool 
	 */
	public function ClearXSLT() {
		$source = opendir(CMSPATH_CACHE_XSLT);
		if (!$source) {
			return false;
		}
		
		$file = readdir($source);
		while ($file !== false) {
			if ($file != "." && $file != "..") {
				unlink(CMSPATH_CACHE_XSLT . $file);
			}
		}
		
		closedir($source);
		
		return true;
	}

	/**
	 * Удаление кэшированного дерева секций
	 */
	public function ClearSectionTree() {
		if (file_exists(CACHE_ST_FILENAME)) {
			unlink(CACHE_ST_FILENAME);
		}
	}

	/**
	 * Загрузка дерева секций из кэша 
	 * Если оно есть в кэше, то функция вернет ссылку на 
	 * дерево секций, иначе null
	 *
	 * @return domelement / null
	 */
	public function GetSectionTree() {
		$null = null;

		if (!file_exists(CACHE_ST_FILENAME)) {
			return $null;
		}

		// загружаем дерево из файла и получаем ссылку на корневую ноду root
		$cachedTree = file_get_contents(CACHE_ST_FILENAME);
		if (!$cachedTree) {
			$error = "CacheClass - Can`t load SectionTree cache from " . CACHE_ST_FILENAME;
			return $null;
		}
		
		$domDocument = new DOMDocument();
		// Создаём дерево XML из загруженного файла
		$xml = $domDocument->loadXML($cachedTree);
		if (!$xml) {
			$error = "CacheClass - Error while parsing the cached tree";
			return $null;
		}
		
		return $domDocument;
	}

	/**
	 * Cохранение дерева секций в кэш 
	 * @param domelement $sectionTree
	 * @return bool
	 */
	public function SaveSectionTree($sectionTree) {
		$fileHandle = fopen(CACHE_ST_FILENAME, "w");
		if (!$fileHandle) {
			return false;
		}

		$xml = new DOMDocument();
		$xml->appendChild($xml->importNode($sectionTree, true));

		$xml->save(CACHE_ST_FILENAME);
		fclose($fileHandle);
		return true;
	}
	
	/* функция, удаляющая "ненужные" части xslt шаблона */
	private function _CutXSLT(&$xslt, $match, $isFirst = false) {
		$tmp = array();
		// удаляем определения xml namespace
		// $xslt = preg_replace('/\sxmlns\:xsl\=\"[^"]*"/', "", $xslt);
		// если это не первый шаблон из списка (базовый)
		if (!$isFirst) {
			// вырезаем директиву <?xml... >
			$xslt = preg_replace('/\<\?xml[^?>]*\?\>/', "", $xslt);
			// вырезаем объявления stylesheet`ов
			$xslt = preg_replace('/\<xsl\:stylesheet[^>]*\>/', "", $xslt);
			$xslt = preg_replace('/\<\/xsl\:stylesheet\>/', "", $xslt);
		} else {
			// вырезаем только последнее объявление </xsl:stylesheet>
			$xslt = preg_replace('/\<\/xsl\:stylesheet\>/', "", $xslt);
		}

		// ищем инклюды и вставляем их в общий шаблон
		$xslt = preg_replace("/\<xsl:include\s+href=[\"']([^'\"]+)[\"']\s*\/\>/ie", "\$this->_ParseIncludes(\"\\1\", \$match)", $xslt);

		// ищем все объявления шаблонов
		if (preg_match_all('/<xsl:template([^>]*)>/', $xslt, $tmp)) {
			// назначаем пустые шаблоны для секции параметров запроса и для дерева секций
			$defTemplates = <<<gap
<xsl:template match="/root/SectionTree" />
<xsl:template match="/root/QueryParams" />
<xsl:template match="/root/Visitor" />
gap;
			if ($isFirst) {
				$source = $tmp[0][0];
				$reg = preg_quote($source);
				$reg = "/" . preg_replace("~/~", "\\/", $reg) . "/";
				$xslt = preg_replace($reg, $defTemplates . "\n" . $source, $xslt);
			}

			$i = 0;
			$fl = true;
			$cnt = count($tmp[1]);
			while (($fl) and ($i < $cnt)) {
				// если есть параметры match или name, тогда следующая итерация
				if (strpos($tmp[1][$i], 'name="') or strpos($tmp[1][$i], 'match="')) {
					$i++;
					continue;
				}
				$source = $tmp[0][$i];
				$newDef = "<xsl:template match=\"$match\"{$tmp[1][$i]}>";
				$reg = preg_quote($source);
				$reg = "/" . preg_replace("~/~", "\\/", $reg) . "/";
				$xslt = preg_replace($reg, $newDef, $xslt);
				$fl = false;
				$i++;
			}
		}
	}
	
	private function _ParseIncludes($fileName, $match) {
		$result = file_get_contents(GenPath($fileName, CMSPATH_XSLT, CMSPATH_PXSLT));

		if (!$result) {
			$this->error->StopScript("CacheClass", "Can`t load XSLT template from $fileName");
		} else {
			$this->_CutXSLT($result, $match, false);
		}
		
		return $result;

	}
	
}

?>