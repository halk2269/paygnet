<?php

/**
 * Базовый класс для модификации URI
 * 
 * Для подключения класса добавьте в таблицу sys_var_ints параметр OwnInputURIParsing, равный единице,
 * напишите класс-наследник URIModifyClass и положите его в папку pbin проекта (файл должен называться uri.php).
 * 
 * Необходимые действия проводятся в методе DoModify()
 * 
 * @author IDM
 */

abstract class URIModifyBaseClass {
	
	/**
	 * Конструктор
	 *
	 * @access public
	 * @return URIModifyBaseClass
	 */
	public function __construct() {
		// Nothing to do here
	}
	
	/**
	 * Выполняет модификацию адресной строки - меняет имя текущей секции,
	 * может напрямую поменять $_GET, $_POST
	 * 
	 * @access public
	 * @param string $SCName Имя запрошенной секции
	 * @return bool Была ли перезаписана адресная строка (true - была)
	 */
	abstract public function DoModify(&$SCName);
	
}

?>