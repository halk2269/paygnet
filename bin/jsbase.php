<?php

/**
 * Базовый класс JS-модуля
 * @author IDM
 * За основу для этого класса была взята back-end часть библиотеки
 * Subsys_JsHttpRequest Дмитрия Котерова, взятой по адресу
 * http://dklab.ru/lib/Subsys_JsHttpRequest/
 * Ниже - комментарий к этой библиотеке.
 */

/**
 * Subsys_JsHttpRequest_Php: PHP backend for JavaScript DHTML loader.
 * (C) 2005 Dmitry Koterov, http://forum.dklab.ru/users/DmitryKoterov/
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * See http://www.gnu.org/copyleft/lesser.html
 *
 * Do not remove this comment if you want to use the script!
 * Не удаляйте данный комментарий, если вы хотите использовать скрипт!
 *
 * This backend library also supports POST requests additionally to GET.
 *
 * @author Dmitry Koterov 
 * @version 3.25
 */

class JSBaseClass extends BaseClass {

	var $thisID;
	var $host;
	var $referer;
	
	/**
	 * Обработчик запроса
	 * @var QueryClass
	 */
	var $query;

	var $contType = "plain";
	var $jsCode;

	var $SCRIPT_ENCODING = "UTF-8";
	var $SCRIPT_DECODE_MODE = '';
	var $UNIQ_HASH;
	var $SCRIPT_ID;
	var $LOADER = null;
	var $QUOTING = null;

	var $_RESULT;
	var $_REQUEST;

	public function __construct($thisID, $query) {
		$this->query = $query;
		
		parent::__construct();

		$this->thisID = $thisID;
		$this->host = $this->query->GetHost();
		$this->referer = $this->query->GetReferer();

		$this->LOADER = "SCRIPT";
		if (preg_match('/(\d+)((?:-\w+)?)$/s', $_SERVER['QUERY_STRING'], $m)) {
			$this->SCRIPT_ID = $m[1];
			// XMLHttpRequest is used if URI ends with "&".
			if ($m[2] == '-xml') $this->LOADER = "XMLHttpRequest";
		} else {
			$this->SCRIPT_ID = 0;
		}

		// Start OB handling early.
		$this->UNIQ_HASH = md5(microtime().getmypid());
		ini_set('error_prepend_string', ini_get('error_prepend_string').$this->UNIQ_HASH);
		ini_set('error_append_string',  ini_get('error_append_string') .$this->UNIQ_HASH);

		// Set up encoding.
		$this->_correctQueryString();
	}

	// Quote string according to input decoding mode.
	// If entities is used (see setEncoding()), no '&' character is quoted,
	// only '"', '>' and '<' (we presume than '&' is already quoted by
	// input reader function).
	//
	// Use this function INSTEAD of htmlspecialchars() for $_GET data
	// in your scripts.
	function quoteInput($s)	{
		if ($this->SCRIPT_DECODE_MODE == 'entities') {
			return str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $s);
		} else {
			return htmlspecialchars($s);
		}
	}


	// Convert PHP scalar, array or hash to JS scalar/array/hash.
	function _php2js($a) {
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a)) {
			$a = addslashes($a);
			$a = str_replace("\n", '\n', $a);
			$a = str_replace("\r", '\r', $a);
			return "'$a'";
		}
		$isList = true;
		for ($i=0, reset($a); $i<count($a); $i++, next($a))
		if (key($a) !== $i) { $isList = false; break; }
		$result = array();
		if ($isList) {
			foreach ($a as $v) $result[] = $this->_php2js($v);
			return '[ ' . join(',', $result) . ' ]';
		} else {
			foreach ($a as $k=>$v) $result[] = $this->_php2js($k) . ': ' . $this->_php2js($v);
			return '{ ' . join(',', $result) . ' }';
		}
	}


	// Parse & decode QUERY_STRING.
	function _correctQueryString() {
		// ATTENTION!!!
		// HTTP_RAW_POST_DATA is only accessible when Content-Type of POST request
		// is NOT default "application/x-www-form-urlencoded"!!!
		// Library frontend sets "application/octet-stream" for that purpose,
		// see JavaScript code.
		foreach (array('_GET'=>$_SERVER['QUERY_STRING'], '_POST'=> (isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : false)) as $dst=>$src) {
			if (isset($GLOBALS[$dst])) {
				// First correct all 2-byte entities.
				$s = preg_replace('/%(?!5B)(?!5D)([0-9a-f]{2})/si', '%u00\\1', $src);
				// Now we can use standard parse_str() with no worry!
				parse_str($s, $data);
				$GLOBALS[$dst] = $this->_ucs2EntitiesDecode($data);
			}
		}
		$this->_REQUEST = (isset($_COOKIE) ? $_COOKIE : array()) + (isset($_POST) ? $_POST : array()) + (isset($_GET) ? $_GET : array());
		if (ini_get('register_globals')) {
			// TODO?
		}
	}

	// Decode all %uXXXX entities in string or array (recurrent).
	// String must not contain %XX entities - they are ignored!
	function _ucs2EntitiesDecode($data)	{
		if (is_array($data)) {
			$d = array();
			foreach ($data as $k=>$v) {
				$d[$this->_ucs2EntitiesDecode($k)] = $this->_ucs2EntitiesDecode($v);
			}
			return $d;
		} else {
			if (strpos($data, '%u') !== false) { // improve speed
			$data = preg_replace_callback('/%u([0-9A-F]{1,4})/si', array(&$this, '_ucs2EntitiesDecodeCallback'), $data);
			}
			return $data;
		}
	}

	// Decode one %uXXXX entity (RE callback).
	function _ucs2EntitiesDecodeCallback($p) {
		$hex = $p[1];
		$dec = hexdec($hex);
		if ($dec === "38" && $this->SCRIPT_DECODE_MODE == 'entities') {
			// Process "&" separately in "entities" decode mode.
			$c = "&amp;";
		} else {
			$c = pack('n', $dec);

			if (is_callable('iconv')) {
				$c = @iconv('UCS-2BE', $this->SCRIPT_ENCODING, pack('n', $dec));
			}

			if (!strlen($c)) {
				if ($this->SCRIPT_DECODE_MODE == 'entities') {
					$c = '&#'.$dec.';';
				} else {
					$c = '?';
				}
			}
		}
		return $c;
	}

	function GenerateJSCode() {	}

	function GetJSCode() {
		// Check for error.
		/*
		$text = GetErrors();
		if (preg_match('{'.$this->UNIQ_HASH.'(.*?)'.$this->UNIQ_HASH.'}sx', $text)) {
			$text = str_replace($this->UNIQ_HASH, '', $text);
			$this->WAS_ERROR = 1;
		}
		*/
		$text = '';
		
		// Content-type header.
		// In XMLHttpRequest mode we must return text/plain - damned stupid Opera 8.0. :(
		$this->contType = ($this->LOADER=="SCRIPT") ? "js" : "plain";
		// Make resulting hash.
		if (!isset($this->RESULT)) $this->RESULT = $this->_RESULT;
		$result = $this->_php2js($this->RESULT);
		$text =
		"// BEGIN Subsys_JsHttpRequest_Js\n" .
		"Subsys_JsHttpRequest_Js.dataReady(\n" .
		"  " . $this->_php2js($this->SCRIPT_ID) . ", // this ID is passed from JavaScript frontend\n" .
		"  " . $this->_php2js(trim($text)) . ",\n" .
		"  " . $result . "\n" .
		")\n" .
		"// END Subsys_JsHttpRequest_Js\n" .
		"";
		return $text;
	}

	function _SetContType($contType) {
		$this->contType = $contType;
	}

	function GetJSContType() {
		return $this->contType;
	}

}

?>