<?php
require_once(CMSPATH_LIB . "docwriting/arrayfield.php");
require_once(CMSPATH_LIB . "docwriting/boolfield.php");
require_once(CMSPATH_LIB . "docwriting/datefield.php");
require_once(CMSPATH_LIB . "docwriting/datetimefield.php");
require_once(CMSPATH_LIB . "docwriting/filefield.php");
require_once(CMSPATH_LIB . "docwriting/floatfield.php");
require_once(CMSPATH_LIB . "docwriting/imagefield.php");
require_once(CMSPATH_LIB . "docwriting/intfield.php");
require_once(CMSPATH_LIB . "docwriting/linkfield.php");
require_once(CMSPATH_LIB . "docwriting/passwordfield.php");
require_once(CMSPATH_LIB . "docwriting/selectfield.php");
require_once(CMSPATH_LIB . "docwriting/radiofield.php");
require_once(CMSPATH_LIB . "docwriting/multiboxfield.php");
require_once(CMSPATH_LIB . "docwriting/stringfield.php");
require_once(CMSPATH_LIB . "docwriting/strlistfield.php");
require_once(CMSPATH_LIB . "docwriting/tablefield.php");
require_once(CMSPATH_LIB . "docwriting/textfield.php");

require_once(CMSPATH_LIB . "docwriting/linksvalidator.php");

/**
 * Класс, проверяющий корректность прищедщих данных
 * @author IDM
 */
class DocumentValidator {

	private $imagesArray = array();
	private $filesArray = array();
	private $selectArray = array();
	private $radioArray = array();
	private $multiboxArray = array();
	private $strListArray = array();
	private $filesToDeleteArray = array();

	var $linksToInsert = array();
	var $linksToDelete = array();
	
	var $linkValidators = array();

	/**
	 * @var DTConfClass
	 */
	var $dtconf;
	/**
	 * @var QueryClass
	 */
	var $query;

	var $dtName;

	var $errors = array();
	var $fields = array();

	var $act;
	var $canCreateEnabled;
	var $docId = 0;

	/**
	 * @param QueryClass $query
	 * @param string $dtName
	 * @param string $act
	 * @param bool $canCreateEnabled
	 * @return DocumentValidator
	 */
	public function __construct($query, $dtName, $act, $canCreateEnabled, $docId) {
		$this->dtconf = DTConfClass::GetInstance();
		$this->dtName = $dtName;
		$this->query = $query;
		
		$this->act = $act;
		$this->canCreateEnabled = $canCreateEnabled;
		$this->docId = $docId;

		$this->_SetEnabled();

		foreach ($this->dtconf->dtf[$this->dtName] as $name => $fieldConf) {
			$className = ucfirst(strtolower($fieldConf["type"])) . "Field";
			$field = new $className($this, $query, $fieldConf, $name);

			if ($field->GetError()) {
				$this->errors[$name] = $field->GetError();
			}
			
			if (false !== $field->GetValue()) {
				$this->fields[$name] = $field->GetValue();
			}
		}

		if (isset($this->dtconf->dtl[$dtName])) {
			foreach ($this->dtconf->dtl[$dtName] as $name => $value) {
				$this->linkValidators[$name] = new LinksValidator($this, $this->query, $value);
				
				if ($this->linkValidators[$name]->GetNumLinksToDelete() > 0) {
					$this->linksToDelete[] = array("doct" => $value["doct"], "links" => $this->linkValidators[$name]->GetLinksToDelete(), "both" => $value["both"]);
				}
				if ($this->linkValidators[$name]->GetNumLinksToInsert() > 0) {
					$this->linksToInsert[] = array("doct" => $value["doct"], "links" => $this->linkValidators[$name]->GetLinksToInsert(), "both" => $value["both"]);
				}
			}
		}

		if (isset($this->dtconf->dtr[$this->dtName])) {
			foreach ($this->dtconf->dtr[$this->dtName] as $ruleName) {
				$path = GenPath($ruleName, CMSPATH_BIN . "docrules/", CMSPATH_PBIN . "docrules/");
				require_once($path . ".php");

				if ("#" == $ruleName{0}) $ruleName = substr($ruleName, 1);

				$className = ucfirst(strtolower($ruleName)) . "DocumentRule";
				$rule = new $className($this, $query);
				$rule->ExecuteRule();
				if ($rule->GetError()) {
					$this->errors["rule_" . $ruleName] = $rule->GetError();
				}
			}
		}
	}

	function GetErrors() {
		return $this->errors;
	}

	function GetDocTypeField($name) {
		return isset($this->dtconf->dtf[$this->dtName][$name])
			? $this->dtconf->dtf[$this->dtName][$name]
			: false;
	}

	public function GetDocType() {
		return $this->dtconf->dtf[$this->dtName];
	}

	public function GetDocTypeName() {
		return $this->dtName;
	}

	public function GetFields() {
		return $this->fields;
	}

	public function GetImagesArray() {
		return $this->imagesArray;
	}

	public function GetFilesArray() {
		return $this->filesArray;
	}

	public function GetSelectArray() {
		return $this->selectArray;
	}
	
	public function GetRadioArray() {
		return $this->radioArray;
	}
	
	public function GetMultiboxArray() {
		return $this->multiboxArray;
	}

	public function GetStrListArray() {
		return $this->strListArray;
	}

	public function GetFilesToDeleteArray() {
		return $this->filesToDeleteArray;
	}

	public function GetAct() {
		return $this->act;
	}

	public function GetDocId() {
		return $this->docId;
	}

	public function SetImage($fieldName, $value) {
		$this->imagesArray[$fieldName] = $value;
	}

	public function SetFile($fieldName, $value) {
		$this->filesArray[$fieldName] = $value;
	}

	public function SetSelect($fieldName, $value) {
		$this->selectArray[$fieldName] = $value;
	}
	
	public function SetRadio($fieldName, $value) {
		$this->radioArray[$fieldName] = $value;
	}
	
	public function SetMultibox($fieldName, $value) {
		$this->multiboxArray[$fieldName] = $value;
	}

	public function SetStrList($fieldName, $value) {
		$this->strListArray[$fieldName] = $value;
	}

	public function SetFileToDelete($value) {
		$this->filesToDeleteArray[] = $value;
	}

	/**
	 * Записи, необходимые для внесения в таблицу линков
	 *
	 * @param string $linkTable
	 * @param int / array[int] $toId
	 * @param int / array[int] $fromId
	 */
	function SetLinkToInsert($linkTable, $toId, $fromId) {
		$this->linksToInsert[] = array("link_tbl" => $linkTable, "to_id" => $toId, "from_id" => $fromId);
	}

	/**
	 * Записи, необходимые для удаления из таблицы линков
	 *
	 * @param string $linkTable
	 * @param int / array[int] $toId
	 * @param int / array[int] $fromId
	 */
	function SetLinkToDelete($linkTable, $toId, $fromId) {
		$this->linksToDelete[] = array("link_tbl" => $linkTable, "to_id" => $toId, "from_id" => $fromId);
	}

	function GetLinksToDelete() {
		return $this->linksToDelete;
	}

	function GetLinksToInsert() {
		return $this->linksToInsert;
	}
	
	/**
	 * возвращает валидатор отношения документов "один-ко-многим"
	 *
	 * @param string $name
	 * @return LinkValidator
	 */
	function GetLinkValidator($name) {
		$null = null;
		$validator = (isset($this->linkValidators[$name])) ? $this->linkValidators[$name] : $null;
		return $validator;
	}

	function _SetEnabled() {
		$enab = $this->query->GetParam("enabled");

		if (!isset($this->dtconf->dteh[$this->dtName]) || !$this->dtconf->dteh[$this->dtName]) {
			if ($this->act == "Create") {
				$this->fields["enabled"] = ($this->canCreateEnabled and $enab == 1) ? "1" : "0";
			} else {
				$this->fields["enabled"] = ($enab == 1) ? "1" : "0";
			}
		} elseif ($this->canCreateEnabled and $enab !== false) {
			// Superadministrator
			$this->fields["enabled"] = ($enab == 1) ? "1" : "0";
		}
	}
}
?>