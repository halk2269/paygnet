<?php

/**
 * Класс работы с MIME типами
 */

class MimeClass {

	/**
	 * MIME тип по умолчанию
	 */ 
	const DEFAULT_MIME_TYPE = 'application/octet-stream';
	
	private $db;
	
	static private $instance;

	public function __construct() {
		$this->db = DBClass::GetInstance();
	}
	
	static public function GetInstance() {
		if (!self::$instance instanceof MimeClass) {
			self::$instance = new MimeClass();
		}
		
		return self::$instance;
	}
	
	/** 
	 * Функция, возвращающая тип MIME по расширению.
	 * Обращается к базе данных и, если такое расширение есть, выдается соответсвуюшее 
	 * ему значение MIME типа. Если нет - выдаётся значение по умолчанию 
	 */
	public function GetMimeByExt($ext) {
		$type = $this->db->GetValue("SELECT type FROM sys_mimetypes WHERE ext = '{$ext}'");
		return ($type) ? $type : self::DEFAULT_MIME_TYPE;
	}

	/** 
	 * Функция, возвращающая тип MIME по полному имени файла.
	 * За основу взята функция GetMimeByExt.
	 */
	public function GetMimeByFileName($filename) {
		if (preg_match('~\.(\w{1,10})$~', $filename, $match)) {
			return $this->GetMimeByExt($match[1]);
		} else {
			return self::DEFAULT_MIME_TYPE;
		}
	}

	/** 
	 * Добавляет в базу расширение файла и соответствующий ему MIME тип.
	 * Если такого расширение нет, то добавляется новое, иначе - заменяется.
	 */
	public function SetMimeType($ext, $type) {
		$stmt = $this->db->SQL("REPLACE INTO sys_mimetypes (ext, type) VALUES ('{$ext}', '{$type}')");
		return $stmt->rowCount();
	}

}

?>