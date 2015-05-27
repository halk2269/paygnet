<?php
/**
  * Фабрика Строителей полей документа
  */

class FieldFactory {
	
	static private $instance;

	/**
	 * В зависимости от типа поля выбираем, какой из строителей полей использовать.
	 * Если объект-строитель для данного типа поля уже создан, то возвращаем ссылку на него, 
	 * иначе создаем соответствующий объект-строитель.
	 * 
	 * @param object $xml
	 * @param object $dt (ссылка на делегирующий объект DTClass)
	 * @param string $type
	 * @param object $error
	 * @return object
	 */
	static public function MakeFTClass($xml, $dt, $type, &$error) {
		if (isset(self::$instance[$type]) && is_object(self::$instance[$type])) {
			self::$instance[$type]->SetXML($xml);
			
			return self::$instance[$type];
		}

		switch ($type) {
			case "array" : {
				require_once(CMSPATH_LIB . "dt/array.ft.php");
				self::$instance["array"] = new ArrayFTClass($xml, $dt);
				break;
			}
			case "aux" : {
				require_once(CMSPATH_LIB . "dt/auxft.php");
				self::$instance["aux"] = new AuxFTClass($xml, $dt);
				break;
			}
			case "bool" : {
				require_once(CMSPATH_LIB . "dt/bool.ft.php");
				self::$instance["bool"] = new BoolFTClass($xml, $dt);
				break;
			}
			case "date" : {
				require_once(CMSPATH_LIB . "dt/date.ft.php");
				self::$instance["date"] = new DateFTClass($xml, $dt);
				break;
			}
			case "datetime" : {
				require_once(CMSPATH_LIB . "dt/datetime.ft.php");
				self::$instance["datetime"] = new DatetimeFTClass($xml, $dt);
				break;
			}
			case "file" : {
				require_once(CMSPATH_LIB . "dt/file.ft.php");
				self::$instance["file"] = new FileFTClass($xml, $dt);
				break;
			}
			case "float" : {
				require_once(CMSPATH_LIB . "dt/float.ft.php");
				self::$instance["float"] = new FloatFTClass($xml, $dt);
				break;
			}
			case "image" : {
				require_once(CMSPATH_LIB . "dt/image.ft.php");
				self::$instance["image"] = new ImageFTClass($xml, $dt);
				break;
			}
			case "int" : {
				require_once(CMSPATH_LIB . "dt/int.ft.php");
				self::$instance["int"] = new IntFTClass($xml, $dt);
				break;
			}
			case "link" : {
				require_once(CMSPATH_LIB . "dt/link.ft.php");
				self::$instance["link"] = new LinkFTClass($xml, $dt);
				break;
			}
			case "password" : {
				require_once(CMSPATH_LIB . "dt/password.ft.php");
				self::$instance["password"] = new PasswordFTClass($xml, $dt);
				break;
			}
			case "select" : {
				require_once(CMSPATH_LIB . "dt/select.ft.php");
				self::$instance["select"] = new SelectFTClass($xml, $dt);
				break;
			}
			case "radio" : {
				require_once(CMSPATH_LIB . "dt/radio.ft.php");
				self::$instance["radio"] = new RadioFTClass($xml, $dt);
				break;
			}
			case "multibox" : {
				require_once(CMSPATH_LIB . "dt/multibox.ft.php");
				self::$instance["multibox"] = new MultiboxFTClass($xml, $dt);
				break;
			}
			case "string" : {
				require_once(CMSPATH_LIB . "dt/string.ft.php");
				self::$instance["string"] = new StringFTClass($xml, $dt);
				break;
			}
			case "strlist" : {
				require_once(CMSPATH_LIB . "dt/strlist.ft.php");
				self::$instance["strlist"] = new StrlistFTClass($xml, $dt);
				break;
			}
			case "table" : {
				require_once(CMSPATH_LIB . "dt/table.ft.php");
				self::$instance["table"] = new TableFTClass($xml, $dt);
				break;
			}
			case "text" : {
				require_once(CMSPATH_LIB . "dt/text.ft.php");
				self::$instance["text"] = new TextFTClass($xml, $dt);
				break;
			}
			
			default: {
				$error->StopScript("DTClass", "Unknown field type - " . $type);
			}

		}
		
		
		return self::$instance[$type];
	}
}
?>