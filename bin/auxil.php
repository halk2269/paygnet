<?php

/**
 * Вспомогательные функции
 * @author основы для ф-ции saveThumbnail(): FearINC@gmail.com (From: http://www.php.net/manual/en/function.imagecopyresized.php#55692)
 */

function LoadFile($filename) {
	return file_get_contents($filename);
}

/**
 * Возвращает текущее время в секундах 
 */
function GetMicrotime() {
	list($usec, $sec) = explode(" ", microtime());
	return (float)$usec + (float)$sec;
}

function GetCookie($name) {
	if (isset($_COOKIE[$name])) {
		return $_COOKIE[$name];
	} else {
		return null;
	}
}

/**
 * Перевод текста из кодировки windows-1251 в UTF-8
 * Работает только при включенном расширении php_iconv.dll
 */
function WinToUTF8($string) {
	return iconv("windows-1251", "UTF-8", $string);
}

/** 
 * Перевод текста из кодировки UTF-8 в windows-1251
 * Работает только при включенном расширении php_iconv.dll
 */
function UTF8ToWin($string) {
	return iconv("UTF-8", "windows-1251", $string);
}

/**
 * Стандартная ф-ция htmlentities() калечит символы в кодировке UTF :(  
 */
function XMLEntities($str) {
	$str = preg_replace("/&/", "&amp;", $str);
	$str = preg_replace("/</", "&lt;", $str);
	$str = preg_replace("/>/", "&gt;", $str);
	return $str;
}

function XMLEntitiesWithoutAmp($str) {
	$str = preg_replace("/</", "&lt;", $str);
	$str = preg_replace("/>/", "&gt;", $str);
	return $str;
}

function unhtmlspecialchars( $string ) {
	$string = str_replace ( '&amp;', '&', $string );
	$string = str_replace ( '&#039;', '\'', $string );
	$string = str_replace ( '&quot;', '"', $string );
	$string = str_replace ( '&lt;', '<', $string );
	$string = str_replace ( '&gt;', '>', $string );
	return $string;
}

/* Выдаёт размер файла в красивом виде */
function FormatFileSize($size) {
	if (($tsize = $size / 1024) < 1) return floor($size) . " Bytes";
	else {
		$size = $tsize;
		if (($tsize = $size / 1024) < 1) return number_format($size, 2, '.', '') . " KB";
		else {
			$size = $tsize;
			if (($tsize = $size / 1024) < 1) return number_format($size, 2, '.', '') . " MB";
			else {
				$size = $tsize;
				return number_format($size, 2, '.', ' ') . " GB";
			}
		}
	}
}

function IsGoodNum($param) {
	$param = (string)$param;
	$len = strlen($param);
	
	return ctype_digit($param) and $len <= 11 and $len >= 1;
}

function IsGoodId($param) {
	$param = (string)$param;
	$len = strlen($param);
	
	return ctype_digit($param) and $len <= 11 and $len >= 1 and intval($param);
}

function PregRealEscape($str) {
	$str = preg_quote($str);
	$str = preg_replace("/\//", '\/', $str);
	return $str;
}

/* Работа со строками запросов */

function URLReplaceParam($paramName, $paramValue, $url = "")	{
	$q = ($url == "") ? $_SERVER["REQUEST_URI"] : $url;
	$q = preg_replace("/[&?]$/", "", $q);
	$paramName = preg_quote($paramName);

	$conf = GlobalConfClass::GetInstance();
	if ($conf->Param("StaticURL")) {
		// если статический параметр стоит не последним, либо за ним нет динамических параметров
		if (preg_match("~/{$paramName}/[^/\?]+~i", $q) > 0) {
			return preg_replace("~{$paramName}/[^/]+~i", "{$paramName}/{$paramValue}", $q);
		}
		else {
			// если за значением параметра стоит  "/?" и динамические параметры
			if (preg_match("~/\?~i", $q) > 0) {
				return preg_replace("~\?~i", "{$paramName}/{$paramValue}/?", $q);
			// если за значением параметра стоит "?" и динамические параметры
			} elseif (preg_match("~\?~i", $q) > 0) {
				return preg_replace("~\?~i", "/{$paramName}/{$paramValue}/?", $q);				
			}
			else {
				return "{$q}{$paramName}/{$paramValue}/";
			}
		}
	} else {
		if (preg_match("/{$paramName}=[^&]*$/i", $q) > 0) return preg_replace("/{$paramName}=[^&]*$/i", "{$paramName}={$paramValue}", $q);
		if (preg_match("/{$paramName}=[^&]*&/i", $q) > 0) return  preg_replace("/{$paramName}=[^&]*&/i", "{$paramName}={$paramValue}&", $q);
		if (preg_match("/\?/", $q) > 0) return "{$q}&{$paramName}={$paramValue}";
		else return "{$q}?{$paramName}={$paramValue}";
	}

}

function URLDeleteParam($paramName, $url = "") {
	$path = ($url == "") ? $_SERVER["REQUEST_URI"] : $url;
	$paramName = preg_quote($paramName);

	$conf = GlobalConfClass::GetInstance();

	if ($conf->Param("StaticURL")) {
		$ret = preg_replace("~/{$paramName}\/[^/\?]*~i", "", $path);
	} else {
		$ret = preg_replace("/{$paramName}=[^&]*&/i", "", $path);
		$ret = preg_replace("/[&?]{$paramName}=[^&]*$/i", "", $ret);
	}
	return $ret;
}

function stripslashes_deep($value) {
	$value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
	return $value;
}

function X_CreateNode($xml, $parentNode, $nodeName, $text = "") {
	$newNode = $xml->createElement($nodeName);
	$parentNode->appendChild($newNode);

	if ($text) {
		$textNode = $xml->createTextNode($text);
		$newNode->appendChild($textNode);
	}
	
	return $newNode;
}

function X_AddText($xml, $parentNode, $text) {
	$textNode = $xml->createTextNode($text);
	$parentNode->appendChild($textNode);

	return;
}

function X_SETAttr($node, $attrName, $attrValue = '') {
	$node->setAttribute($attrName, $attrValue);
}

function XSL_Transformation($xsl, $xml) {
	$xslDoc = new DOMDocument();
	$xslDoc->loadXML($xsl);
	
	$xslProc = new XSLTProcessor();
	$xslProc->importStylesheet($xslDoc);
	
	return $xslProc->transformToXml($xml);
}

function GenPath($path, $globalBase, $ownBase) {
	$pathIsPersonal = ($path and $path[0] == "#");
	$pathBase = $pathIsPersonal ? $ownBase : $globalBase;
	if ($pathIsPersonal) $path = substr($path, 1);
	return ($pathBase . $path);
}

/**
 * Base of the function: FearINC@gmail.com
 * From: http://www.php.net/manual/en/function.imagecopyresized.php#55692
 **/
function SaveThumbnail($saveToDir, $imagePath, $imageName, $max_x, $max_y) {
	preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $imageName, $ext);
	if (!isset($ext[2])) return false;
	switch (strtolower($ext[2])) {
		case 'jpg' :
		case 'jpeg': $im = @imagecreatefromjpeg($imagePath);
		break;
		case 'gif' : $im = @imagecreatefromgif($imagePath);
		break;
		case 'png' : $im = @imagecreatefrompng($imagePath);
		break;
		default    : $stop = true; $im = false;
		break;
	}

	if (!$im) return false;

	if (!isset($stop)) {
		$x = imagesx($im);
		$y = imagesy($im);

		if ($x <= $max_x and $y <= $max_y) {
			$save = imagecreatetruecolor($x, $y);
		} elseif (($max_x/$max_y) < ($x/$y)) {
			$save = imagecreatetruecolor($x/($x/$max_x), $y/($x/$max_x));
		} else {
			$save = imagecreatetruecolor($x/($y/$max_y), $y/($y/$max_y));
		}

		imagecopyresized($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);

		imagegif($save, "{$saveToDir}{$imageName}.gif");
		imagedestroy($im);
		imagedestroy($save);

		return true;
	}

	return false;
}

function CheckImageSize($imagePath, $min_x, $min_y, $max_x, $max_y, $imageName = "") {
	$ppp = ($imageName == "") ? $imagePath : $imageName;
	preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $ppp, $ext);
	if (!isset($ext[2])) return -1;
	switch (strtolower($ext[2])) {
		case 'jpg' :
		case 'jpeg': $im = @imagecreatefromjpeg($imagePath);
		break;
		case 'gif' : $im = @imagecreatefromgif($imagePath);
		break;
		case 'png' : $im = @imagecreatefrompng($imagePath);
		break;
		default    : $stop = true; $im = false;
		break;
	}

	if (!$im) return -1;
	if (!isset($stop)) {
		$x = imagesx($im);
		$y = imagesy($im);
		return ((($x >= $min_x) and ($x <= $max_x or $max_x == 0) and ($y >= $min_y) and ($y <= $max_y or $max_y == 0)) ? 1 : 0);
	}

	return -1;
}

function GetImageSizes($imagePath, &$width, &$height, $imageName = "") {
	$ppp = ($imageName == "") ? $imagePath : $imageName;
	preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $ppp, $ext);
	if (!isset($ext[2])) return false;
	switch (strtolower($ext[2])) {
		case 'jpg' :
		case 'jpeg': $im = @imagecreatefromjpeg($imagePath);
		break;
		case 'gif' : $im = @imagecreatefromgif($imagePath);
		break;
		case 'png' : $im = @imagecreatefrompng($imagePath);
		break;
		default    : $stop = true; $im = false;
		break;
	}

	if (!$im) return false;

	if (!isset($stop)) {
		$width = imagesx($im);
		$height = imagesy($im);
		return true;
	}

	return false;
}

function IsImage($imageName, $imagePath, &$width, &$height) {
	preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $imageName, $ext);
	if (!isset($ext[2])) return false;
	
	switch (strtolower($ext[2])) {
		case 'jpg' :
		case 'jpeg': $im = @imagecreatefromjpeg($imagePath); break;
		case 'gif' : $im = @imagecreatefromgif($imagePath); break;
		case 'png' : $im = @imagecreatefrompng($imagePath); break;
		default    : return false;
	}
	
	if (!$im) return false;
	return true;
}

?>