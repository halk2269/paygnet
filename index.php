<?php

define("PATH_TO_ROOT", "");

function GetErrors() {
	global $phpErrorOut;
	global $phpError;
	
	$ret = $phpErrorOut;
	$phpErrorOut = "";
	$phpError = false;
	
	return $ret;
}

function finalize($buffer) {
	global $phpError;
	if ($phpError) {
		global $phpErrorOut;
		return $phpErrorOut;
	} else {
		return $buffer;
	}
}

// true - разработка сайта, false - сайт на рабочем сервере
$DEV = true;

try {
	require_once(PATH_TO_ROOT . "cmspath.php");

	date_default_timezone_set('Europe/Moscow');
	setlocale(LC_ALL, 'ru_RU.UTF8');
	
	/* Отлов ошибок и их отображение в нормальном виде */
	$phpError = false;
	$phpErrorOut = "";
	
	// Отлов ошибок
	require_once(CMSPATH_BIN . "errhandler.php");
	 
	if ($DEV) {
		set_error_handler("errhandler");
		error_reporting(E_ALL);
	} else {
		error_reporting(0);
	}
	
	require_once(CMSPATH_BIN . "main.php");
	
	ob_start("finalize");
	
	$main = new MainClass();
	$main->Run();	
} 

catch (Exception $exception) {
	if ($DEV) {
		var_dump($exception);
		exit();
	}
}

?>