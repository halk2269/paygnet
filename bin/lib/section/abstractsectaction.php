<?php 

/**
 * Абстрактный класс для действий над секциями
 */
abstract class AbstractSectAction {

	protected $error = false;
	protected $info = false;
	
	/**
	 * @var DBClass
	 */
	protected $db;
	
	/**
	 * @var QueryClass
	 */
	protected $query;

	/**
	 * должно быть переопеделено в наследниках
	 */ 
	protected $right; 

	protected $rightsArray = array();

	protected $sectionId = 0;
	
	protected $lastSort;
	
	protected $retpath = false;

	/**
	 * @param QueryClass $query
	 * @param array[string] $rightsArray
	 * @return AbstractSectAction
	 */
	public function __construct($query, $rightsArray) {
		$this->db = DBClass::GetInstance();
		$this->query = $query;
		$this->rightsArray = $rightsArray;

		// тут же можно сделать проверку на наличие данной секции
		if (!$this->_SetSectionId()) {
			$this->error = "BadSectionId";
			return;
		}

		if (!$this->_IsRightsOK()) {
			$this->error = "BadRights";
			return;
		}

		$this->_MakeChanges();
	}

	public function GetError() {
		return $this->error;
	}
	
	/**
	 * Возвращает дополнительную информацию об ошибке
	 *
	 * @return mixed
	 */
	public function GetErrorDesc() {
		return "";
	}

	public function GetInfo() {
		return $this->info;
	}
	
	/**
	 * Возвращает дополнительную информацию. Должно быть переопределено в нужных подклассах
	 * @return mixed
	 */
	public function GetAdditionalInfo() {
		return "";
	}
	
	public function GetRetPath() {
		return $this->retpath;
	}

	/**
	 * Действие над секцией
	 * @abstract
	 */
	abstract protected function _MakeChanges();

	/**
	 * Должно быть переопределено для create
	 *
	 * @return bool
	 */
	protected function _SetSectionId() {
		if (!$this->query->GetParam("id") || !IsGoodId($this->query->GetParam("id"))) {
			return false;
		}
				
		$this->sectionId = (int)$this->query->GetParam("id");
		return ($this->db->RowExists("SELECT enabled FROM sys_sections WHERE id = {$this->sectionId}"));
	}
	
	/**
	 * @access protected
	 */
	protected function _RebuildSort() {
		$stmt = $this->db->SQL("SELECT id FROM sys_sections WHERE parent_id = {$this->sectionId} ORDER BY sort");
		
		$i = 0;
		while ($listRow = $stmt->fetchObject()) {
			$i++;
			$this->db->SQL(
				"UPDATE sys_sections SET sort = {$i} WHERE id = {$listRow->id}"
			);
		}
		
		$this->lastSort = ++$i;
		
	}
	
	/**
	 * @access protected
	 */
	protected function _UpdateChilderenPath($id) {
		$path = $this->db->GetValue("SELECT path FROM sys_sections WHERE id = '{$id}'");
		if (!$path) {
			return false;
		}
		
		$stmt = $this->db->SQL("SELECT id FROM sys_sections WHERE parent_id = '{$id}'");
		while ($row = $stmt->fetchObject()) {
			$this->db->SQL("UPDATE sys_sections SET path = '{$path},{$id}' WHERE id = {$row->id}");
			
			$result = $this->_UpdateChilderenPath($row->id);
			if (!$result) {
				return false;
			}
		}
		
		return true;
	}
	
	private function _IsRightsOK() {
		return (isset($this->rightsArray[$this->right]) && $this->rightsArray[$this->right]);
	}
	
}
?>