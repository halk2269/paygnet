<?php

/**
 * Глобальные переменные сайта (целочисленные и строковые).
 */
class GlobalVarsClass {

	private $db;

	private $cachedStrings = array();
	private $cachedInts = array();
	
	static private $instance;

	public function __construct() {
		$this->db = DBClass::GetInstance();
		
		$this->CacheVars();
	}
	
	static public function GetInstance() {
		if (!self::$instance instanceof GlobalVarsClass) {
			self::$instance = new GlobalVarsClass();
		}
		
		return self::$instance;
	}
	
	public function CacheVars() {
		$strings = $this->db->SQL("SELECT name, value FROM sys_var_strings");
		while ($string = $strings->fetchObject()) {
			$this->cachedStrings[$string->name] = $string->value;
		}

		$ints = $this->db->SQL("SELECT name, value FROM sys_var_ints");
		while ($int = $ints->fetchObject()) {
			$this->cachedInts[$int->name] = $int->value;
		}
	}

	public function SetStr($name, $value) {
		$value = substr($value, 0, 255);
		$query = 'REPLACE INTO sys_var_strings (name, value) VALUES (?, ?)';
		
		$stmt = $this->db->SQL($query, array($name, $value));
		return $stmt->rowCount();
	}

	public function SetInt($name, $value) {
		$value = (int)$value;
		$query = 'REPLACE INTO sys_var_ints (name, value) VALUES (?, ?)';
		
		$stmt = $this->db->SQL($query, array($name, $value));
		return $stmt->rowCount();
	}

	public function GetStr($name) {
		return (isset($this->cachedStrings[$name]))	? $this->cachedStrings[$name] : false;		
	}

	public function GetInt($name) {
		return (isset($this->cachedInts[$name])) ? $this->cachedInts[$name]	: false;
	}
	
}

?>