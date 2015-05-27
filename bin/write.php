<?php

require_once(CMSPATH_BIN . "writebase.php");

/**
 * Класс, заведующий модулями записи. 
 * Выбирает модуль записи, запускает его, и редиректит
 * по результатам работы
 */
class WriteClass {
	
	const WRITE_CLASS_POSTFIX = 'WriteClass';
	
	/**
	 * класс, отвечающий за передачу информации от модуля записи к модулю чтения
	 * @var PassInfoClass
	 */
	private $passInfo;
	
	/**
	 * Обработчик запроса
	 * @var QueryClass
	 */
	private $query;
	
	/**
	 * Работа с базой данных
	 * @var DBClass
	 */
	private $db;
	
	/**
	 * Главный класс конфигурации
	 * @var GlobalConfClass
	 */
	private $conf;
	
	/**
	 * Адрес, куда идет редирект после модуля записи
	 */
	private $redirectPath;

	public function __construct($passInfo, $query) {
		$this->db = DBClass::GetInstance();
		$this->conf = GlobalConfClass::GetInstance();
		
		$this->passInfo = $passInfo;
		$this->query = $query;
		
		$this->redirectPath = $this->conf->Param("Prefix");
	}

	public function StartModule() {
		$class = mb_substr($this->query->GetWriteModuleName(), 0, 50) . self::WRITE_CLASS_POSTFIX;

		$writeClassRow = $this->db->GetRow(
			"SELECT id, filename FROM sys_writemodules WHERE class = ? AND enabled = ?",
			array($class, 1)
		);
		
		if (!$writeClassRow) {
			return false;
		}

		require_once(GenPath($writeClassRow->filename, CMSPATH_MOD_WRITE, CMSPATH_PMOD_WRITE));
		$module = new $class($this->passInfo, $this->query, $writeClassRow->id);
		if ($module->IsRefError()) {
			return false;
		}
		
		$success = $module->MakeChanges();
		$module->WriteStoredInfo();
		
		$this->redirectPath = $module->GetRedirectPath($success);
		return true;
	}

	public function GetRedirectPath() {
		return $this->redirectPath;
	}
}

?>