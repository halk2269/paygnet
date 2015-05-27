<?php

require_once(CMSPATH_LIB . "excel_reader/reader.php");

/**
 * Класс релизует работу с типом документа — table.
 */
class BaseTablesClass {
	
	private $db;
	
	public function __construct() {
		$this->db = DBClass::GetInstance();
	}
	
	public function FileToXML($path, $conf) {
		$text = $this->FileToText($path);
		if (!$text) {
			return false;
		}
		
		return $this->TextToXML($text, $conf);
	}
	
	/**
	 * @param $path - путь к файлу .xls, из которого формируется таблица
	 * @access public
	 * @return string
	 */
	public function FileToText($path) {
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding("cp1251");

		if (!$data->read($path) and !isset($data->sheets[0])) {
			return false;
		}
		
		$sheet = $data->sheets[0];
		$ret = $this->_TextForming($sheet);
		
		if (!$ret) {
			return false;
		}
		
		return WinToUTF8($ret);
	}
	
	/**
	 * @param array $conf - часть файла конфигурации 
	 */ 
	public function TextToXML($dataTable, $conf) {
		if (strlen($dataTable) > 65535) {
			return false;
		}
		
		// Подготовка
		$dataTable = preg_replace("/\r\n/", "\n", $dataTable);
		$dataTable = preg_replace("/\n*$/", "", $dataTable);

		// Список таблиц
		$tableList = array();
		if (isset($conf["mult"]) and $conf["mult"]) {
			$tableList = preg_split("/\n{3,}/", $dataTable, -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$tableList[] = trim($dataTable);
		}

		return $this->_CreateTableXML($tableList, $conf);		
	}
	
	private function _TextForming($sheet) {
		$ret = "";
		
		for ($i = 1; $i <= $sheet["numRows"]; $i++) {
			if ($i > 1) {
				$ret .= "\n";
			}
			
			for ($j = 1; $j <= $sheet["numCols"]; $j++) {
				$value = (isset($sheet["cells"][$i][$j])) ? $sheet["cells"][$i][$j] : "";
				if ($j > 1) {
					$ret .= "\t";
				}
				$ret .= $value;
			}
		}
		
		return $ret;
	}

	/**
	 * @param $tableList
	 * @return string $tableXML
	 */
	private function _CreateTableXML($tableList, $conf) {
		// Сюда будет писаться XML
		$tableXML = "";
		// Проходимся по каждой таблице
		foreach ($tableList as $table) {
			$tableXML .= "<table>\n";
			$table = explode("\n", $table);
			// Определяем максимальное число столбцов в переданных данных.
			$maxTD = 1;
			foreach ($table as $line) {
				$tst = explode("\t", $line);
				if (count($tst) > $maxTD) {
					$maxTD = count($tst);
				}
			}
			
			// Составляем XML-строку
			foreach ($table as $lineIdx => $line) {
				// Возможно, это не td, а th?
				$tag = (isset($conf["hcnt"]) and $lineIdx < $conf["hcnt"]) ? "th" : "td";
				// Разбиваем строку по знакам табуляции
				$tdArray = explode("\t", $line);
				// Начинаем tr
				$tableXML .= "<tr>";
				// Для каждого td...
				for ($i = 0; $i < $maxTD; $i++) {
					if (isset($tdArray[$i])) {
						$tdArray[$i] = trim($tdArray[$i]);
					}
					
					// Если столбец
					if (
						!isset($tdArray[$i]) 
						|| (isset($conf["blnk"]) 
						&& $conf["blnk"] === true 
						&& !$tdArray[$i])
					) {
						$tagContent = (isset($conf["blnk"]) && $conf["blnk"] === true) ? ' ' : '';
					} else {
						$tagContent = htmlspecialchars($tdArray[$i]);
					}
					
					$span = 1;
					if (
						(
							isset($conf["hspn"]) 
							&& $conf["hspn"] 
							&& isset($conf["hcnt"]) 
							&& $lineIdx < $conf["hcnt"]
						)
							||
						( 
							isset($conf["bspn"]) 
							&& $conf["bspn"] 
							&& (!isset($conf["hcnt"]) || !$conf["hcnt"] || $lineIdx >= $conf["hcnt"])
						)
					) {
						while (
							(
								isset($tdArray[$i + 1]) 
								&& '' == $tdArray[$i + 1] 
								|| !isset($tdArray[$i + 1])
							) 
							&& $i < ($maxTD - 1)
						) {
							$span++;
							$i++;
						}
					}
					$colspan = ($span > 1) 
						? ' colspan="' . $span . '"' 
						: '';
					
					$tableXML .= "<{$tag}{$colspan}>" . $tagContent . "</{$tag}>";
				}
				// Закрываем строку
				$tableXML .= "</tr>\n";
			}
			// Закрываем таблицу
			$tableXML .= "</table>\n";
		}
		
		return $tableXML;
	}
}

?>