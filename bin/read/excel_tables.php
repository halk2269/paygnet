<?php

/**
 * Converting Excel tables into text.
 * 
 * Return codes:
 * OK - Успех
 * ERR_UPLOAD_NO_FILE - Вы не выбрали файл для загрузки
 * ERR_UPLOAD_INI_SIZE - Файл слишком большой (также см. максимально допустимый 
 * 		размер файла в <span id="max_filesize"></span>)
 * ERR_UPLOAD_FORM_SIZE - Файл слишком большой (также см. максимально допустимый 
 * 		размер файла в <span id="max_filesize"></span>)
 * ERR_UPLOAD_PARTIAL - Файл был загружен не полностью
 * ERR_FILE_INVALID - Файл не является корректным Excel файлом
 * ERR_UNKNOWN - Неизвестная ошибка на сервере (возникнуть не должна…)
 * 
 * @author IDM
 */

require_once(CMSPATH_LIB . "tables/base_tables.php");

class ExcelTablesReadClass extends ReadModuleBaseClass {

	function CreateXML() {
		$errCode = "OK";
		$tableText = "";
		if (!isset($_FILES["excelfile"])) {
			$errCode = "ERR_UPLOAD_NO_FILE";
		} else {
			$f = $_FILES["excelfile"];
			$error = $f["error"];
			switch ($error) {
				case UPLOAD_ERR_OK : $errCode = "OK"; break;
				case UPLOAD_ERR_INI_SIZE : $errCode = "ERR_UPLOAD_INI_SIZE"; break;
				case UPLOAD_ERR_FORM_SIZE : $errCode = "ERR_UPLOAD_FORM_SIZE"; break;
				case UPLOAD_ERR_PARTIAL : $errCode = "ERR_UPLOAD_PARTIAL"; break;
				case UPLOAD_ERR_NO_FILE : $errCode = "ERR_UPLOAD_NO_FILE"; break;
				default : $errCode = "ERR_UNKNOWN"; break;
			}
			if ($errCode == "OK") {
				$excelPath = $f["tmp_name"];
				$table = new BaseTablesClass();
				$rv = $table->FileToText($excelPath);
				if ($rv !== false) {
					$tableText = $rv;
				} else {
					$errCode = "ERR_FILE_INVALID";
				}
			}
		}
		$maxFileSize = ini_get('upload_max_filesize');
		$this->headers[] = "Content-Type: text/html; charset=UTF-8";
		echo "<html>\n<head></head>\n<body>\n<div>\n";
		echo "<span id='errcode'>{$errCode}</span>\n";
		echo "<br />\n";
		echo "<span id='max_filesize'>{$maxFileSize}</span>\n";
		echo "<br />\n";
		echo "<textarea id='result'>";
		echo $tableText;
		echo "</textarea>\n";
		echo "<div>\n</body>\n</html>";
		return true;
	}
}

?>