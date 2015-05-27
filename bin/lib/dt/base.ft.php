<?php
/**
 * Класс создатель полей в document и docType
 * 
 * публичные функции:
 * MakeFieldForDocument
 * MakeFieldForDocType
 *
 * @author IDM
 *
 **/
class BaseFTClass {
	
	/**
	 * @var DOMDocument
	 */
	protected $xml;
	
	protected $type;

	/**
	 * Класс, отвечающий за работу с ТД (типами документов)
	 * @var DTClass
	 */
	protected $dt;
	
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
	 * Авторизация пользователя
	 * @var AuthClass
	 */
	protected $auth;

	/**
	 * @param object $xml
	 * @param object $dt (ссылка на объект DTClass)
	 * @return BaseFTClass
	 */
	public function __construct($xml, $dt) {
		$this->xml = $xml;
		$this->dt = $dt;
		$type = "base";

		$this->conf = $dt->conf;
		$this->dtconf = $dt->dtconf;
		$this->db = $dt->db;
		$this->globalvars = $dt->globalvars;
		$this->auth = $dt->auth;
	}

	public function SetXML($xml) {
		$this->xml = $xml;
	}
	
	/**
	 * Возвращает поле document
	 *
	 * @param mixed $row (результаты запроса к БД)
	 * @param string $name (имя поля)
	 * @param mixed $params (параметры)
	 * @return mixed domdocument (null / domelement / array of domelement)
	 */
	public function MakeFieldForDocument($row, $fieldName, $params) {
		$field = $this->CreateField($fieldName, $params["dtName"]);
		
		$this->CreateContent($field, $row, $fieldName, $params);
		
		return $field;
	}
	
	/**
	 * Возвращает поле doctype
	 *
	 * @param string $name
	 * @param string $dtName
	 * @param bool $showHidden 
	 * @return object (domdocument)
	 */
	public function MakeFieldForDocType($name, $dtName, $showHidden) {
		$field = $this->CreateField($name, $dtName);
		
		$this->MainAttributes($field, $name, $dtName, $showHidden);
		$this->AdditionalAttributes($field, $name, $dtName, $showHidden);
		$this->AdditionalDeftContent($field, $name, $dtName, $showHidden);
		
		return $field;
	}

	protected function CreateField($name, $dtName) {
		$field = $this->xml->createElement("field");
		$field->setAttribute("name", $name);

		$field->setAttribute("type", $this->type);
		if (isset($this->dtconf->dtf[$dtName][$name]["desc"])) {
			$field->setAttribute("description", $this->dtconf->dtf[$dtName][$name]["desc"]);
		}
		return $field;
	}

	protected function CreateContent($field, $row, $name, $params) {
		return true;
	}

	private function MainAttributes($field, $name, $dtName, $showHidden) {
		$importance = (isset($this->dtconf->dtf[$dtName][$name]["impt"])) ? (int)$this->dtconf->dtf[$dtName][$name]["impt"] : 0;
		$field->setAttribute("importance", $importance);
		
		foreach ($this->dtconf->dtf[$dtName][$name] as $attrIdx => $attrVal) {
			if (strlen($attrIdx) != 4) {
				if (is_bool($attrVal)) {
					$attrVal = ($attrVal) ? "1" : "0";
				}
				$field->setAttribute($attrIdx, $attrVal);
			}
		}
		
		return true;
	}

	protected function AdditionalAttributes($field, $name, $dtName, $showHidden) {
		return true;
	}

	protected function AdditionalDeftContent($field, $name, $dtName, $showHidden) {
		if (isset($this->dtconf->dtf[$dtName][$name]["deft"]) && $this->dtconf->dtf[$dtName][$name]["deft"]) {
			$txtTemp = $this->xml->createTextNode($this->dtconf->dtf[$dtName][$name]["deft"]);
			$field->appendChild($txtTemp);
		}
		return true;
	}

}
?>