<?php

/**
 * Связь с БД, здесь же выборка в XML
 */

class DBClass {

	private $conf;
	private $error;

	/**
	 * @var PDO
	 */
	private $conn;

	/**
	 * Debug variables
	 */ 
	private $debug = false;
	private $debugHtml = '';
	
	private $sqlTime = 0;
	private $queryCount = 0;
	
	private $startTime;
	
	private static $instance;

	public function __construct() {
		$this->conf = GlobalConfClass::GetInstance();
		$this->error = ErrorClass::GetInstance();
		
		// Открываем соединение с БД
		$this->Open();
	}
	
	static public function GetInstance() {
		if (!self::$instance instanceof DBClass) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	/**
	 * Установить соединение с БД 
	 */
	public function Open() {
		try {
			$this->conn = new PDO(
				"mysql:dbname={$this->conf->Param('DBName')};host={$this->conf->Param('DBHost')}",
				$this->conf->Param("DBUser"), 
				$this->conf->Param("DBPass"),
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
			);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $exception) {
			$this->error->StopScript(
				"DBProviderClass",
				"Невозможно подключится к MySQL (" . $this->conf->Param("DBHost") . ")"
			);
		}
	}
	
	// Закрыть соединение с БД
	public function Close() {
		$this->conn = null;
	}
	
	/**
	 * Выполнить SQL запрос
	 * 
	 * @var string $query
	 * @return PDOStatement
	 */
	public function SQL($query, array $params = null) {
		if ($this->debug) {
			$this->queryCount++;
			$this->startTime = GetMicrotime();
		}
				
		$stmt = $this->conn->prepare($query);
		$stmt->execute($params);
						
		$this->debugQuery($query, $params);
						
		return $stmt;
	}
	
	public function GetValue($query, array $params = array()) {
		$stmt = $this->SQL($query, $params);
		if (!$stmt || !$stmt->rowCount()) {
			return null;
		}
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * @param string $query
	 * @return stdClass
	 */
	public function GetRow($query, array $params = array()) {
		$stmt = $this->SQL($query, $params);
		if (!$stmt || !$stmt->rowCount()) {
			return null;
		}
		
		return $stmt->fetchObject();
	}
	
	public function GetRowCount($query) {
		$stmt = $this->SQL($query);
		return $stmt->rowCount();
	}
	
	public function RowExists($query) {
		$stmt = $this->SQL($query);
		return ($stmt->rowCount() > 0);
	}
	
	public function Begin() {
		if ($this->conn->inTransaction()) {
			return;
		}
		
		$this->conn->beginTransaction();
	}
	
	public function Commit() {
		if ($this->conn->inTransaction()) {
			$this->conn->commit();
		}
	}
	
	public function Rollback() {
		if ($this->conn->inTransaction()) {
			$this->conn->rollBack();
		}
	}

	public function GetLastID() {
		return $this->conn->lastInsertId();
	}
	
	public function quote($value) {
		$this->conn->quote($value);
	}
	
	private function debugQuery($query, array $params = null) {
		if ($this->debug) {
			$endTime = GetMicrotime();
			$execTime = ($endTime - $this->startTime);
			$execTimeStr = sprintf("%.4f", $execTime);

			$shutdown = '';

			if (preg_match( "/^\s*select/i", $query)) {
				$stmt = $this->SQL("EXPLAIN {$query}", $params);

				$this->debugHtml .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FFE8F3' align='center'>
										<tr>
											<td colspan='8' style='font-size:14px' bgcolor='#FFC5Cb'>
												<b>{$shutdown}Select Query</b>
											</td>
										</tr>
										<tr>
											<td colspan='8' style='font-family:courier, monaco, arial;font-size:14px;color:black'>{$query}</td>
										</tr>
										<tr bgcolor='#FFC5Cb'>
											<td><b>table</b></td>
											<td><b>type</b></td>
											<td><b>possible_keys</b></td>
											<td><b>key</b></td>
											<td><b>key_len</b></td>
											<td><b>ref</b></td>
											<td><b>rows</b></td>
											<td><b>Extra</b></td>
										</tr>\n"
				;
				
				while ($array = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$typeCol = '#FFFFFF';
					if ($array['type'] == 'ref' or $array['type'] == 'eq_ref' or $array['type'] == 'const') {
						$typeCol = '#D8FFD4';
					} else if ($array['type'] == 'ALL') {
						$typeCol = '#FFEEBA';
					}

					$this->debugHtml .= "<tr bgcolor='#FFFFFF'>
											<td>{$array['table']}&nbsp;</td>
											<td bgcolor='{$typeCol}'>{$array['type']}&nbsp;</td>
											<td>{$array['possible_keys']}&nbsp;</td>
											<td>{$array['key']}&nbsp;</td>
											<td>{$array['key_len']}&nbsp;</td>
											<td>{$array['ref']}&nbsp;</td>
											<td>{$array['rows']}&nbsp;</td>
											<td>{$array['Extra']}&nbsp;</td>
										</tr>\n";
				}

				$this->sqlTime += $execTime;
				if ($execTime > 0.05) {
					$execTime = "<span style='color:red'><b>$execTime</b></span>";
				}

				$this->debugHtml .= "
						<tr>
							<td colspan='8' bgcolor='#FFD6DC' style='font-size:14px'>
								<b>MySQL time</b>: $execTime</b>
							</td>
						</tr>
				    </table>
				    \n<br/>\n
				";
			} else {
				$this->debugHtml .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FEFEFE'  align='center'>
										 <tr>
										 	<td style='font-size:14px' bgcolor='#EFEFEF'><b>{$shutdown}Non Select Query</b></td>
										 </tr>
										 <tr>
										 	<td style='font-family:courier, monaco, arial;font-size:14px'>$query</td>
										 </tr>
										 <tr>
										 	<td style='font-size:14px' bgcolor='#EFEFEF'><b>MySQL time</b>: $execTime</span></td>
										 </tr>
									</table><br/>\n\n";
			}
		}
	}
	
	public function SetDebugMode($debug) {
		$this->debug = $debug;
	}

	public function GetTime() {
		return date("Y-m-d H:i:s");
	}
	
	// Timestamp --> Datetime and Datetime --> Datetime
	function ToDateTime($datetime) {
		if (strlen($datetime) == 14) {
			return preg_replace("~([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})~", "\\1-\\2-\\3 \\4:\\5:\\6", $datetime);
		} else {
			return $datetime;
		}
	}

	function DateParser($indate) {
		$date = new DateTime($indate);
		$begin = new DateTime('1970-01-01');
				
		if ($date->format('U') <= $begin->format('U')) {
			$date->setDate(1970, 1, 2);
		}
		
		return $date->format('U');
	}

	// Debug functions
	public function debugGetHTML() {
		return $this->debugHtml;
	}

	public function debugGetSQLTime() {
		return $this->sqlTime;
	}

	public function debugGetQueryCount() {
		return $this->queryCount;
	}
	
}
?>