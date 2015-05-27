<?php

require_once(CMSPATH_BIN . "jsbase.php"); // Базовый класс для JS-модулей

/**
 * Запуск модулей JS
 */
class JSClass {
	
	/**
	 * Обработчик запроса
	 * @var QueryClass
	 */
	protected $query;
	
	/**
	 * Работа с базой данных
	 * @var DBClass
	 */
	protected $db;
		
	/**
	 * Данный класс отвечает за ответ пользователю
	 * @var ResponseClass
	 */
	protected $response;

	public function __construct($query, $response) {
		$this->db = DBClass::GetInstance();
		
		$this->query = $query;
		$this->response = $response;
		
		$this->runModule();
	}

	private function runModule() {
		$class = mb_substr(ucfirst($this->query->GetJSModuleName()), 0, 50) . "JSClass";
		
		$row = $this->db->GetRow(
			"SELECT id, filename FROM sys_jsmodules WHERE class = ? AND enabled = ?",
			array($class, 1)
		);
		
		if (!$row) {
			$response = json_encode(array('error' => "undefined class {$class}"));
			
			$this->response->SetHeaders('js');
			$this->response->WriteJS($response);
			
			return;
		}

		require_once GenPath($row->filename, CMSPATH_MOD_JS, CMSPATH_PMOD_JS);

		$module = new $class($row->id, $this->query);
		$method = $this->query->GetParam('method');
		
		if ($method && method_exists($module, $method)) {
			$module->{$method}();			
		} else {
			$module->GenerateJSCode();	
		}
			
		// отдача заголовков и содержимого классу ответа
		$this->response->SetHeaders($module->GetJSContType());
		$this->response->WriteJS($module->GetJSCode());
	}
	
}
?>