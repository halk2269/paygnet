<?php

require_once(CMSPATH_CONF . "global.conf.php");
require_once(CMSPATH_CONF . "dt.conf.php");

require_once(CMSPATH_BIN . "log.php");
require_once(CMSPATH_BIN . "error.php");
require_once(CMSPATH_BIN . "db.php");
require_once(CMSPATH_BIN . "globalvars.php");
require_once(CMSPATH_BIN . "mime.php");
require_once(CMSPATH_BIN . "auth.php");
require_once(CMSPATH_BIN . "dt.php");
require_once(CMSPATH_BIN . "mail.php");
require_once(CMSPATH_BIN . "cache.php");

/**
 * Базовый класс-агрегатор. 
 * От него наследуется множество других классов
 */
class BaseClass {
	
	/**
	 * Главный класс конфигурации
	 * @var GlobalConfClass
	 */
	protected $conf;
	
	/**
	 * Определение ТД
	 * @var DTConfClass
	 */
	protected $dtconf;
	
	/**
	 * Логи
	 * @var LogClass
	 */
	protected $log;
	
	/**
	 * Обработка ошибок
	 * @var ErrorClass
	 */
	protected $error;
	
	/**
	 * Работа с базой данных
	 * @var DBClass
	 */
	protected $db;
	
	/**
	 * Глобальные переменные
	 * @var GlobalVarsClass
	 */
	protected $globalvars;
	
	/**
	 * MIME-типы
	 * @var MimeClass
	 */
	protected $mime;
	
	/**
	 * Авторизация пользователя
	 * @var AuthClass
	 */
	protected $auth;
	
	/**
	 * Класс работы с ТД
	 * @var DTClass
	 */
	protected $dt;
	
	/**
	 * Класс отсылки сообщений
	 * @var MailClass
	 */
	protected $mail;
	
	/**
	 * Класс работы с кэшем
	 * @var CacheClass
	 */
	protected $cache;

	public function __construct() {
		$this->conf = GlobalConfClass::GetInstance();
		$this->dtconf = DTConfClass::GetInstance();
		$this->log = LogClass::GetInstance();
		$this->error = ErrorClass::GetInstance();
		$this->db = DBClass::GetInstance();
		$this->globalvars = GlobalVarsClass::GetInstance();
		$this->mime = MimeClass::GetInstance();
		$this->auth = AuthClass::GetInstance();
		$this->dt = DTClass::GetInstance();
		$this->mail = MailClass::GetInstance();
		$this->cache = CacheClass::GetInstance();
	}
	
}

?>