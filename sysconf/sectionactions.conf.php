<?php
require_once(CMSPATH_LIB . "section/enablesectaction.php");
require_once(CMSPATH_LIB . "section/disablesectaction.php");
require_once(CMSPATH_LIB . "section/createsectaction.php");
require_once(CMSPATH_LIB . "section/renamesectaction.php");
require_once(CMSPATH_LIB . "section/chnamesectaction.php");
require_once(CMSPATH_LIB . "section/showsectaction.php");
require_once(CMSPATH_LIB . "section/hidesectaction.php");
require_once(CMSPATH_LIB . "section/showonmapsectaction.php");
require_once(CMSPATH_LIB . "section/hideonmapsectaction.php");
require_once(CMSPATH_LIB . "section/deletesectaction.php");
require_once(CMSPATH_LIB . "section/gotochildsectaction.php");
require_once(CMSPATH_LIB . "section/movesectaction.php");

/**
 * Класс, который конфигурирует и заведует действиями над секциями.
 * Модуль записи SectWriteClass запрашивает у SectionActionsConf (данный класс) 
 * метод GetSectionActionClass($name), который возвращает объект-действие над
 * секцией. 
 * Сам SectionActionsConf может быть дополнен персональным классом PersonalSectionActionsConf,
 * который содержит допольнительные и переопределённые для проекта действия над секциями.
 * 
 * Последовательность запроса на получение объекта-действия над секцией:
 * 1 проверяется, есть ли персональный конфигуратор действий PersonalSectionActionsConf
 * 1.1 если есть, то проверяется, есть ли для запрошенного действия класс, и если да, 
 * то возвращается объект для этого действия
 * 1.2 если нет в персональном, ищется в общем, и если есть в общем, то возвращается объект 
 * для этого действия
 * 1.3 возвращается нуль
 * 
 * @author fred
 */
class SectionActionsConf {

	private $create = "CreateSectAction";
	private $enable = "EnableSectAction";
	private $disable = "DisableSectAction";
	private $rename = "RenameSectAction";
	private $chname = "ChnameSectAction";
	private $show = "ShowSectAction";
	private $hide = "HideSectAction";
	private $showonmap = "ShowOnMapSectAction";
	private $hideonmap = "HideOnMapSectAction";
	private $delete = "DeleteSectAction";
	private $gotochild = "GoToChildSectAction";
	private $move = "MoveSectAction";
	
	protected $rights = array();
	/**
	 * @private QueryClass
	 */
	private $query;
	/**
	 * @private PersonalSectionActionsConf
	 */
	private $personalSysConf;

	/**
	 * @param QueryClass $query
	 * @param array $rights
	 * @return SectionActionsConf
	 */
	public function __construct($query, $rights) {
		$this->query = $query;
		$this->rights = $rights;

		if (file_exists(CMSPATH_MAIN . "psysconf/personalsectionactions.conf.php")) {
			require_once(CMSPATH_MAIN . "psysconf/personalsectionactions.conf.php");
			$this->personalSysConf = new PersonalSectionActionsConf();
		}
	}

	/**
	 * Возвращает объект-действие над секций для запрощенного типа действия
	 *
	 * @param string $name имя действия
	 * @return AbstractSectAction
	 */
	public function GetSectionActionClass($name) {
		if ($this->personalSysConf && $this->personalSysConf->GetSectionActionClassName($name)) {
			$className = $this->personalSysConf->GetSectionActionClassName($name);
		} elseif (isset($this->$name)) {
			$className = $this->$name;
		} else {
			return null;
		}

		$action = new $className($this->query, $this->rights);
		return $action;
	}
}
?>