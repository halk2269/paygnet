<?php

/**
 * Работа с графикой - простая
 * @author IDM
 */

class SimpleGraphClass extends BaseClass {
	
	var $im;

	function __construct() {
		parent::__construct();
	}

	function LoadJPEG($imgPath) {
		$im = @imagecreatefromjpeg($imgPath);
		return $im ? $im : false;
	}

	function CheckMinSize($im, $width, $height) {
		$x = imagesx($im);
		$y = imagesy($im);
		return ($x >= $width and $y >= $height);
	}

	function CheckMaxSize($im, $width, $height) {
		$x = imagesx($im);
		$y = imagesy($im);
		return ($x <= $width and $y <= $height);
	}

	function CheckPixels($im, $min, $max) {
		$x = imagesx($im);
		$y = imagesy($im);
		$pixels = $x * $y;
		return ($pixels >= $min and $pixels <= $max);
	}

	function GetImageSizes($im, &$outWidth, &$outHeight) {
		$outWidth = imagesx($im);
		$outHeight = imagesy($im);
	}

	function ResizeJPEG($im, $savePath, $maxSize, &$outWidth, &$outHeight, $interlaced = true, $quality = 75) {
		$x = imagesx($im);
		$y = imagesy($im);

		if ($x <= $maxSize and $y <= $maxSize) {
			$save = imagecreatetruecolor($x, $y);
		} elseif (($maxSize/$maxSize) < ($x/$y)) {
			$save = imagecreatetruecolor($x/($x/$maxSize), $y/($x/$maxSize));
		} else {
			$save = imagecreatetruecolor($x/($y/$maxSize), $y/($y/$maxSize));
		}

		imagecopyresized($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);
		$outWidth = imagesx($save);
		$outHeight = imagesy($save);
		imageinterlace($save, $interlaced ? 1 : 0);
		imagejpeg($save, $savePath, $quality);
		imagedestroy($save);
	}

	function DestroyImage($im) {
		imagedestroy($im);
	}

	static public function SaveThumbnail($saveToDir, $imagePath, $imageName, $max_x, $max_y) {
		preg_match("'^(.*)\.(gif|jpe?g|png)$'i", $imageName, $ext);
		if (!isset($ext[2])) {
			return false;
		}
		switch (strtolower($ext[2])) {
			case 'jpg' :
			case 'jpeg': $im = imagecreatefromjpeg($imagePath); break;
			case 'gif' : $im = imagecreatefromgif($imagePath); break;
			case 'png' : $im = imagecreatefrompng($imagePath); break;
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

			switch (strtolower($ext[2])) {
				case 'jpg' :
				case 'jpeg': {
					imagejpeg($save, $saveToDir . $imageName, 80); 
					break;
				}
				case 'gif' : {
					imagegif($save, $saveToDir . $imageName); 
					break;
				}
				case 'png' : {
					imagepng($save, $saveToDir . $imageName); 
					break;
				}
			}
			imagedestroy($im);
			imagedestroy($save);

			return true;
		}

		return false;
	}
	
	function IsImage($imageName) {
		preg_match("~^(.*)\.(gif|jpe?g|png)$~i", $imageName, $ext);
		return (isset($ext[2]));
	}
	
	static public function exe($command) {
		$out = array();
		$ret = 0;
		
		exec($command, $out, $ret);
		$out = sizeof($out) == 0 ? "<empty>" : join("\n", $out);
		if ($ret == 0) {
			return array(0 => "ImageMagick error code {$ret}. Сommand: \"$command\". Description: {$out}");
		}
		
		return array($ret => "ImageMagick error code {$ret}. Сommand: \"$command\". Description: {$out}");
	}
		
}

?>