#!/usr/bin/php
<?php

// Сорри, это заглушка. Ну слишком уж меня ломает это делать по-человечески.
// Нужно для того, чтобы вызов ResponseClass::SetHeaders("html"); в error.php
// не подкашивал скрипт
class ResponseClass {
	function SetHeaders($a) {}
}

set_time_limit(600);

function finalize($buffer) {
	global $cron;
	$cron->Clean();
	return $buffer;
}

// true - разработка сайта, false - сайт на рабочем сервере
$DEV = true;

define("PATH_TO_ROOT", "");
require_once(PATH_TO_ROOT."cmspath.php"); // Пути

if ($DEV) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

require_once(CMSPATH_BIN . "cron.php"); // Главный класс

$cron = new CronClass();
/* Перед завершением работы скрипта вызовется функция finalize().
Если, конечно, никому не придёт в голову вызвать ob_end_flush() раньше. */
ob_start("finalize");

$cron->Run();

?>