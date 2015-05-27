<?php
/**
 * Суперкласс для "ограничений на документ".
 * Накладывает ограничения не на отдельное поле, а на целый документ.
 * Например, у документа одно из двух полей должно быть заполнено
 * (обязательность на каждое поле поставить невозможно)
 * Имеет доступ к переменным запроса через поле query и к конфигурации
 * типа документа через поле documentValidator.
 * Реальные наследники переопределяют только метод ExecuteRule()
 * 
 * @abstract 
 * @author fred
 */
abstract class AbstractDocumentRule {
	/**
	 * @var QueryClass
	 */
	protected $query;
	/**
	 * @var DocumentValidator
	 */
	protected $documentValidator;

	protected $error = false;

	/**
	 * @param DocumentValidator $documentValidator
	 * @param QueryClass $query
	 * @return AbstractDocumentRule
	 */
	public function __construct($documentValidator, $query) {
		$this->documentValidator = $documentValidator;
		$this->query = $query;
	}

	/**
	 * Возвращает код ошибки, которая произошла при невыполнении 
	 * ограничения, наложенного на документ
	 *
	 * @return string
	 */
	public function GetError() {
		return $this->error;
	}
	
	/**
	 * Метод, проверяющий выполнение ограничений, наложенных на документ.
	 * ОБЯЗАТЕЛЬНО должен быть переопределен в наследниках
	 * @abstract 
	 */
	abstract public function ExecuteRule();
}

?>