<?php

// все отлчтно
define("MAIL_ALL_OK", "0");
// адрес $to не прошел проверку регулярным выражением 			
define("MAIL_FAIL_TO", "-1");
// один из адресов $cc не прошел проверку регулярным выражением
define("MAIL_FAIL_CC", "-2");
// один из адресов $bcc не прошел проверку регулярным выражением
define("MAIL_FAIL_BCC", "-3");
// не удалось загрузить с диска картинку-вложение			
define("MAIL_FAIL_PIC_LOAD", "-4");
// не удалось загрузить с диска файл-вложение		
define("MAIL_FAIL_FILE_LOAD", "-5");
// неудачно отработала функция mail
define("MAIL_FAIL_MAIL", "-6");

/**
 * Класс отправки сообщений с вложениями
 * @todo
 * 
 * Ввести дополнительный параметр - форсированная отправка вложений
 * Обработать ошибку - не определен один из элементов ассоциативного массива ("name" или "path")
 * Удалить обработку ошибки неправильного регулярного выражения
 * Подумать над передачей параметров вложения %%image%imageID%%
 */

class MailClass {
	
	/**
	 * Ссылка на класс работы с MIME типами
	 */
	private $mime;
	private $conf;
	private $sep = "\n";

	private $errorMsg;
	
	static private $instance;

	public function __construct() {
		$this->mime = MimeClass::GetInstance();
		$this->conf = GlobalConfClass::GetInstance();
	}
	
	static public function GetInstance() {
		if (!self::$instance instanceof MailClass) {
			self::$instance = new MailClass();
		}
		
		return self::$instance;
	}

	/**
	 * отправка пачки писем с вложениями 
	 */
	public function SendToAll($to, $from, $cc, $bcc, $subject, $html, &$attachList, $srcCharset = "UTF-8") {
		$result = array();
		
		// преобразуем строку получателей в массив
		$to = $this->_EmailStrToArray($to);
		// пробуем отослать письмо получателю
		foreach ($to as $recepient) {
			$res = $this->SendToOne($recepient, $from, $cc, $bcc, $subject, $html, $attachList, $srcCharset);
			
			if ($res != MAIL_ALL_OK) {
				$result[$recepient] = $res;
			}
		}
		
		return $result;
	}

	/**
	 * отправка письма с вложениями 
	 */
	public function SendToOne($to, $from, $cc, $bcc, $subject, $html, &$attachList, $srcCharset = "UTF-8") {
		// тестируем e-mail
		if (!$this->TestOneEmail($to)) {
			return MAIL_FAIL_TO;
		}

		if ($cc != '') {
			// преобразование строки с получателями копий в массив
			// и тестирование каждого элемента этого массива
			$ccArr = $this->_EmailStrToArray($cc);
			foreach ($ccArr as $email) {
				if (!$this->TestOneEmail($email)) {
					return MAIL_FAIL_CC;
				}
			}
			
			// обратное преобразование массива получателей копий в строку
			$cc = implode(',', $ccArr);
		}

		if ($bcc != '') {
			// преобразование строки с получателями скрытых копий в массив
			// и тестирование каждого элемента этого массива
			$bccArr = $this->_EmailStrToArray($bcc);
			foreach ($bccArr as $email) {
				if (!$this->TestOneEmail($email)) {
					return MAIL_FAIL_BCC;
				}
			}
			// обратное преобразование массива получателей скрытых копий в строку
			$bcc = implode(',', $bccArr);
		}

		$hasAttach = (isset($attachList) && count($attachList) > 0);
		$subject = stripslashes(trim($subject));
		$body = stripslashes($html);

		$charset = $this->conf->Param("EMailEncoding");
		// замена тега META с указанием кодировки
		$srcCharsetQuoted = preg_quote($srcCharset);
		$body = preg_replace("/(<meta[^>]+)({$srcCharsetQuoted})([^>]+>)/i", "\\1{$charset}\\3", $body);
		// Перекодирование заголовков
		$body = iconv($srcCharset, $charset, $body);
		$subject = $this->_PrepareSubject($srcCharset, $charset, $subject) . $this->sep;

		$boundary = md5(uniqid(time()));
		$headers = array();
		
		$this->_BuildHeaders($to, $from, $cc, $bcc, $headers);
		$this->_BuildMimeHeaders($charset, $boundary, $headers, $hasAttach);
		
		$res = $this->_BuildBodyParts($charset, $boundary, $body, $attachList, $srcCharset);
		if ($res != MAIL_ALL_OK) {
			return $res;
		}

		$headersList = implode($this->sep, $headers) . $this->sep;

		$res = mail($to, $subject, $body, $headersList);
		if (!$res) {
			return MAIL_FAIL_MAIL;
		}

		return MAIL_ALL_OK;
	}


	/** 
	 * Вывод текстового сообщения (для отладки) 
	 */
	public function ErrorMsg() {
		return (!$this->errorMsg) ? MAIL_ALL_OK : $this->errorMsg;
	}
	
	/**
	 * функция проверки почтового адреса на правильность 
	 */
	function TestOneEmail($email) {
		$regex = '^' 
			. '[_a-z0-9-]+' . '(\.[_a-z0-9-]+)*' 
			. '@' 
			. '[a-z0-9-]+' . '(\.[a-z0-9-]{2,})+' 
			. '$';

		return (preg_match("~{$regex}~i", $email, $match));
	}
	
	/**
	 * функция преобразующая строку с e-mail`ами в массив 
	 */
	private function _EmailStrToArray($str) {
		// Удаление всех пробелов
		$str = preg_replace('/\s*,\s*/', ',', $str);
		$str = preg_replace('/^\s*,\s*/', ',', $str);
		$str = preg_replace('/\s*,\s*$/', ',', $str);
		
		return explode(',', $str);		
	}
	
	private function _PrepareSubject($srcCharset, $charset, $subject) {
		$subject = iconv($srcCharset, $charset, $subject);
		$subjectEncoding = strtolower($charset); // если это, конечно, делается так... для 1251 работать будет точно
		
		return "=?{$subjectEncoding}?B?" . base64_encode($subject) . "?=";		
	}

	/** 
	 * основные заголовки письма
	 * 
	 *  @return void
	 */
	private function _BuildHeaders($to, $from, $cc, $bcc, &$headers) {
		if (!empty($from)) {
			$headers[] = "From: $from";
		}
		
		if (!empty($from)) {
			$headers[] = "Reply-to: $from";
		}
		
		if (!empty($cc)) {
			$headers[] = "Cc: $cc";
		}
		
		if (!empty($bcc)) {
			$headers[] = "Bcc: $bcc";
		}		
	}

	/**
	 * создание заголовков письма 
	 */
	private function _BuildMimeHeaders($charset, $boundary, &$headers, $hasAttach = false)	{
		$hasAttach = true;
		$headers[] = "MIME-Version: 1.0";
		
		if ($hasAttach) {
			$headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";
		}
	}

	/**
	 * создание вложений - частей письма 
	 */
	private function _BuildBodyParts($charset, $boundary, &$body, &$attachList = array(), $srcCharset = "UTF-8") {
		// если не передан массив вложений, то завершаем работу
		if (!isset($attachList)) {
			return MAIL_ALL_OK;
		}
		$bodyParts = array(0 => '');
		
		// индекс массива вложений $bodyParts
		$i = 1;

		// СОЗДАНИЕ ВЛОЖЕНИЙ - КАРТИНОК
		// считаем количество вхождений-картинок в тело письма
		preg_match_all("/%%image%([-._a-zA-Z0-9]+)%%/im", $body, $imgMatches);
		for ($k = 0; $k < count($imgMatches[1]); $k++)  {
			$attach = array();
			
			if (isset($attachList[$imgMatches[1][$k]])) {
				$attach = $attachList[$imgMatches[1][$k]];
				$fileBody = LoadFile($attach["path"]);
				
				if ($fileBody) {
					// если файл успешно загрузился, то генерируем заголовки для вложения
					$fileBody = chunk_split(base64_encode($fileBody)) . $this->sep;
					$bodyParts[$i] = "{$this->sep}--" . $boundary . "{$this->sep}";
					$file_mime_type = $this->mime->GetMimeByFileName($attach["path"]);
					$bodyParts[$i] .= "Content-Type: " . $file_mime_type . ";name=\"" . basename($attach["path"]) . "\"{$this->sep}";
					$bodyParts[$i] .= "Content-Transfer-Encoding: base64{$this->sep}";
					$bodyParts[$i] .= "Content-ID: <{$imgMatches[1][$k]}>{$this->sep}{$this->sep}";
					$bodyParts[$i] .= $fileBody . "{$this->sep}";
		
					// заменяем вхождение этой картинки в html-сообщении на ее идентификатор в письме
					$body = preg_replace("#" . preg_quote($imgMatches[0][$k]) . "#", "cid:{$imgMatches[1][$k]}", $body);
					// увеличиваем на единицу счетчик вложений
					$i++;
				} else {
					// ОШИБКА - Не удалось загрузить файл картинки-вложения
					return MAIL_FAIL_PIC_LOAD;
				}
			}
		}
			
		// СОЗДАНИЕ ВЛОЖЕНИЙ - ФАЙЛОВ
		preg_match_all("/<!--%%file%([-._a-zA-Z0-9]+)%%-->/im", $body, $fileMatches);
		for ($k = 0; $k < count($fileMatches[1]); $k++)  {
			$attach = array();
			if (isset($attachList[$fileMatches[1][$k]])) {
				$attach = $attachList[$fileMatches[1][$k]];
				$fileBody = LoadFile($attach["path"]);
				if ($fileBody) {
					$attachFileName = iconv($srcCharset, $charset, $attach["name"]);
					// если файл успешно загрузился, то генерируем заголовки для вложения
					$fileBody = chunk_split(base64_encode($fileBody)) . "{$this->sep}";
					$bodyParts[$i] = "{$this->sep}--" . $boundary . $this->sep;
					$file_mime_type = $this->mime->GetMimeByFileName($attach["path"]);
					$bodyParts[$i] .= "Content-Type: " . $file_mime_type . ";name=\"" . $attachFileName .  "\"{$this->sep}";
					$bodyParts[$i] .= "Content-Transfer-Encoding: base64{$this->sep}";
					$bodyParts[$i] .= "Content-Disposition: attachment; filename=\"" . $attachFileName . "\"{$this->sep}{$this->sep}";
					$bodyParts[$i] .= $fileBody . $this->sep;
					// увеличиваем на единицу счетчик вложений
					$i++;
				} else {
					// ОШИБКА - Не удалось загрузить файл-вложение
					return MAIL_FAIL_FILE_LOAD;
				}
			}
		}

		// записываем в нулевую часть тела письма текстовое сообщение
		$bodyParts[0] = "--" . $boundary . $this->sep;
		$bodyParts[0] .= "Content-Type: text/html; charset={$charset}{$this->sep}";
		$bodyParts[0] .= "Content-Transfer-Encoding: binary{$this->sep}{$this->sep}";
		
		$body = implode($this->sep, preg_split("~[\r\n]~", $body, -1, PREG_SPLIT_NO_EMPTY));

		$bodyParts[0] .= $body . $this->sep;
		$bodyParts[$i - 1] .= "--" . $boundary . "--";
		
		$body = implode("", $bodyParts);

		return MAIL_ALL_OK;
	}
	
}

?>