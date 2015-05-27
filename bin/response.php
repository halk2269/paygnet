<?php
/**
 * Данный класс отвечает за ответ пользователю
 */
class ResponseClass {
	
	/**
	 * Главный класс конфигурации
	 * @var GlobalConfClass
	 */
	var $conf;
	
	/**
	 * Работа с базой данных
	 * @var DBClass
	 */
	private $db;
	
	/**
	 * Обработка ошибок
	 * @var ErrorClass
	 */
	private $error;
	
	/**
	 * Класс, отвечающий за измерение времени исполнения скрипта 
	 * @var TimeClass
	 */
	private $time;

	private $action;

	public function __construct($time, $action) {
		$this->time = $time;
		$this->action = $action;
		$this->conf = GlobalConfClass::GetInstance();
		$this->db = DBClass::GetInstance();
		$this->error = ErrorClass::GetInstance();
	}

	/**
	 * Ф-ция даёт ответ клиенту.
	 *
	 * @param domelement $xml - дерево-xml
	 * @param domelement $xmlRoot - корневая нода
	 * @param unknown_type $xsl - xsl-преобразование
	 * @param bool $onlyXMLNeeded
	 */
	public function DoResponse($xml, $xmlRoot, $xsl, $onlyXMLNeeded) {
		if ($onlyXMLNeeded) {
			// Для RSS и т.п.
			$this->action = "xmlutf"; 
		}

		if (!$onlyXMLNeeded && $this->conf->Param("ShowTime")) {
			$xmlRoot->setAttribute("duration", $this->time->ScriptTime());
		}

		switch ($this->action) {
			case "xml": {
				$this->SetHeaders("xml");
				echo $xml->saveXML();
				if ($this->conf->Param("ShowTime")) {
					echo $this->time->ScriptTimeComment();
				}
				break;
			}
			
			case "xmlutf": {
				$xslForXMLOut = <<<simple
<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:msxsl="urn:schemas-microsoft-com:xslt" exclude-result-prefixes="msxsl">
	<xsl:output encoding="UTF-8" method="xml" indent="no" />
	<xsl:template match="root">
	<xsl:for-each select="*">
	<xsl:copy-of select="." />
	</xsl:for-each>
	</xsl:template>
	</xsl:stylesheet>
simple;
				$this->SetHeaders("xml");
				echo XSL_Transformation($xslForXMLOut, $xml);
				if ($this->conf->Param("ShowTime")) {
					$this->time->ScriptTimeComment();
				}
				break;
			}
			
			case "txml" : {
				$this->SetHeaders("html");
				echo "<pre>";
				echo XMLEntities($xml->saveXML());
				echo "</pre>";
				if ($this->conf->Param("ShowTime")) {
					echo $this->time->ScriptTimePre();
				}
				break;
			}
			
			case "xsl": {
				$this->SetHeaders("xml");
				echo $xsl;
				if ($this->conf->Param("ShowTime")) {
					echo $this->time->ScriptTimeComment();
				}
				
				break;
			}
			
			case "sql": {
				$this->SetHeaders("html");
				
				$html = "<html><head><title>SQL Debugger</title><body bgcolor='white'>";
				$html .= "<style type='text/css'> table, td, tr, body { font-family: verdana,arial, sans-serif;color:black;font-size:11px }</style>";
				$html .= "<h1 align='center'>SQL Total Time: " . $this->db->debugGetSQLTime() . " for " . $this->db->debugGetQueryCount() . " queries</h1><br />" . $this->db->debugGetHTML();
				$html .= "<br/><div align='center'><strong>Total SQL Time: " . $this->db->debugGetSQLTime() . "</div></body></html>";
				
				echo $html;
				
				break;
			}
			
			case "normal": {
				$this->SetHeaders("html");
				echo XSL_Transformation($xsl, $xml);
				if ($this->conf->Param("ShowTime")) {
					echo $this->time->ScriptTimeComment();
				}
				break;
			}
		}
	}

	public function WriteJS($jsCode) {
		echo $jsCode;
	}

	/**
	 * Установка HTTP-заголовков для заданного типа контента
	 * @param string $ContType - тип контента
	 */
	static public function SetHeaders($contType) {
		switch ($contType) {
			case "html": {
				header("Content-type: text/html; charset=UTF-8"); 
				break;
			}
			
			case "xml": {
				header("Content-type: text/xml; charset=UTF-8"); 
				break;
			}
			
			case "js": {
				header("Content-type: text/javascript; charset=UTF-8"); 
				break;
			}
			
			case "json": {
				header('Content-type: application/json; charset=UTF-8');
				break;
			}
			
			case "plain": {
				header("Content-type: text/plain; charset=UTF-8"); 
				break;
			}
			
			default: {
				$this->error->StopScript(
					"ResponseClass", 
					"Попытка установки неизвестного типа контента"
				);
			}
		}
	}

	/**
	 * Отправка заголовка
	 * @param string $header
	 */
	public function SetHeader($header) {
		header($header);
	}
	
	
	/**
	 * Отправка заголовка с 404 ошибкой
	 */
	public function SetError404Header() {
		header("HTTP/1.0 404 Not Found");
	}

	/**
	 * Переадресация на указанный адрес и остановка скрипта
	 * @param string $path
	 */
	public function Redirect($path) {
		header("Location: " . $path);
	}
}
?>