<?php

require_once(CMSPATH_BIN . "auxil.php");
require_once(CMSPATH_BIN . "base.php");

/**
 * Файл, запускаемый из connector.php визуального редактора
 */

class MainFCKClass extends BaseClass {

	public function __construct() {
		parent::__construct();
	}

	public function Run() {
		setlocale(LC_ALL, 'ru_RU');
		mb_internal_encoding("UTF-8");

		// Открываем соединение с БД, авторизуем пользователя, закрываем соединение с БД
		$this->db->Open();
		$this->auth->Authorize();
		$this->db->Close();

		// Если нет прав на загрузку файлов, останавливаем скрипт
		if (!$this->auth->CanUploadFiles()) {
			die('No rights!');
		}
	}
}

error_reporting(0);
$main = new MainFCKClass();
// $main->Run();

?>