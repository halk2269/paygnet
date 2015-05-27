<?php

/**
 * KCAPTCHA configuration file
 */
class KCAPTCHAConfig {
	
	// do not change without changing font files!
	var $alphabet = "0123456789abcdefghijklmnopqrstuvwxyz";
	/**
	 * symbols used to draw CAPTCHA
	 * allowed symbols:
	 * #digits "0123456789" 
	 * #alphabet without similar symbols (o=0, 1=l, i=j, t=f)
	 */
	var $allowed_symbols = "23456789abcdeghkmnpqsuvxyz"; 
	// folder with fonts
	var $fontsdir = 'fonts';
	/**
	 * CAPTCHA string length
	 * random 5 or 6
	 */
	var $length; 
	// CAPTCHA image size (you do not need to change it, whis parameters is optimal)
	var $width = 120;
	var $height = 60;
	// symbol's vertical fluctuation amplitude divided by 2
	var $fluctuation_amplitude = 5;
	// increase safety by prevention of spaces between symbols
	var $no_spaces = true;
	/**
	 * show credits
	 * set to false to remove credits line. Credits adds 12 pixels to image height
	 */
	var $show_credits = false;
	// if empty, HTTP_HOST will be shown
	var $credits = 'www.captcha.ru';
	/**
	 * CAPTCHA image colors (RGB, 0-255)
	 * $foreground_color = array(0, 0, 0);
	 * $background_color = array(220, 230, 255);
	 */
	var $foreground_color;
	var $background_color;
	// JPEG quality of CAPTCHA image (bigger is better quality, but larger file size)
	var $jpeg_quality = 90;
	
	function KCAPTCHAConfig() {
		$this->length = mt_rand(5, 6);
		$this->foreground_color = array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
		$this->background_color = array(mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
	}
	
	function getParam($name) {
		return (isset($this->$name)) ? $this->$name : false;
	}
}

?>