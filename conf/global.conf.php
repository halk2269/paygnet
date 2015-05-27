<?php
/**
 * Главный класс конфигурации - Глобальные конфигурационные переменные
 */
class GlobalConfClass {

	/*******************************************************************************************/
	/* Основные настройки сайта                                                                */
	/*******************************************************************************************/

	/* Параметры подключения к БД */

	// Хост
	//private $DBHost = "mysql1120.ixwebhosting.com";
	// private $DBHost = "localhost";
	private $DBHost = "localhost";

	// Имя базы данных
	//private $DBName = "A876010_paygnet";
	// private $DBName = "math";
	private $DBName = "A876010_paygnet";

	// Имя пользователя
	 private $DBUser = "A876010_pgnroot";
	// private $DBUser = "math";
	//private $DBUser = "admin";

	// Пароль
	private $DBPass = "rVcSn5c8GW";
	// private $DBPass = "math-olymp";
	// private $DBPass = "2269";

	/* Префикс - путь к сайту, начиная от названия домена.
	Если, например, сайт доступен как http://www.domain.com/,
	то префикс - это слэш ("/"). Если же сайт доступен как
	http://www.domain.com/folder/, то префикс - "/folder/" 
	*/

	private $Prefix = "/";
	// private $Prefix = "/math/";
	// private $Prefix = "/paygnet_org/";

	/*******************************************************************************************/
	/* Дополнительные настройки сайта                                                          */
	/*******************************************************************************************/

	// Кодировка писем, рассылаемых системой
	private $EMailEncoding = "UTF-8";

	// E-mail, от которого будут отсылаться уведомления о заполнении форм
	private $NotifyMailFrom = "math@global-card.ru";

	private $RegisterMailFrom = 'lancer.sps@gmail.com';

	// E-mail, на который будут отсылаться уведомления о заполнении формы
	// Разрешено указывать несколько адресов через запятую
	private $FormMailTo = "lancer.sps@gmail.com";

	// E-mail, на который будут отсылаться уведомления о заполнении формы контактов
	// Разрешено указывать несколько адресов через запятую
	private $ContactsMailTo = "lancer.sps@gmail.com";

	private $UserRegTo = "lancer.sps@gmail.com";

	// Интеграция с форумом.
	// Если установлен IPB форум и у сайта и форума общая форма авторизации, и таблицы
	// пользователей форума и сайта должны быть синхронизованы, тогда флаг нужно установить в true
	private $IPBIntegration = false;

	// Скачивание файлов не "напрямую" через веб-сервер, а через скрипт.
	// Медленнее, но позволяет указывать имя файла при отдавании пользователю
	private $SmartFileDownload = true;

	// Необходимость знания хэша для скачивания файла и картинки.
	// Используется только если $SmartFileDownload == true
	private $FileDownloadWithoutHash = true;
	private $ImageDownloadWithoutHash = true;

	// Подсчитывать ли количество скачиваний для файлов и изображений
	private $CalculateDownloadCount = true;

	/*******************************************************************************************/
	/* Сообщения, выдаваемые при ошибках                                                       */
	/*******************************************************************************************/

	/* Сообщение, выводимое при критичной ошибке, приводящей к останову скрипта. Выводится только
	если $ShowError == false; - иначе выводится сама ошибка (этот вариант дожен использоваться
	только при разработке) */
	private $CriticalErrorMsg = "Sorry, requested page is unavalilable now due to server error. Administrator has been informed yet.<br />\nИзвините, запрошенная страница сейчас недоступна. Администратор об этом уже извещён.";

	/*******************************************************************************************/
	/* Переменные, использующиеся при разработке. (!!!) На действующем сайте рекомендуется всё */
	/* установить в false (!!!). Для этих переменных разрешено значение "AS DEV" - в это       */
	/* случае переменные буду установлены в значение $DEV (глобальная переменная,              */
	/* инициализирующаяся в index.php                                                          */
	/*******************************************************************************************/

	// Разрешить ли обработку параметра ser.
	// Более подробно о параметре ser см. в комментариях к файлу bin/query.php
	private $AllowSer = "AS DEV";

	// Всегда давать XML-выдачу, а не результат XSLT-преобразования.
	// Аналогично приписыванию параматра ?ser=txml каждой строке запроса.
	// Работает только если $AllowSer == true
	private $AlwaysXML = false;

	// Показывать ли время работы скрипта.
	private $ShowTime = "AS DEV";

	// Показывать ли ошибки во время работы скрипта.
	private $ShowError = "AS DEV";

	// Не кэшировать XSLT
	private $DontCacheXSLT = "AS DEV";

	// Статические адреса
	private $StaticURL = true;

	private $LogUserActions = true;

	static public function GetInstance() {
		static $instance;

		if (!is_object($instance)) {
			$instance = new GlobalConfClass();
		}

		return $instance;
	}

	public function __construct() {
		global $DEV;
		
		/* Проверка начального и конечного слэшей */
		if ($this->Prefix[0] != '/') {
			$this->Prefix = "/" . $this->Prefix;
		}
		
		if (substr($this->Prefix, -1) != '/') { 
			$this->Prefix .= "/";
		}
		
		if ($this->AllowSer == "AS DEV") {
			$this->AllowSer = $DEV;
		}
		
		if ($this->AlwaysXML == "AS DEV") {
			$this->AlwaysXML = $DEV;
		}
		
		if ($this->ShowTime == "AS DEV") {
			$this->ShowTime = $DEV;
		}
		
		if ($this->ShowError == "AS DEV") {
			$this->ShowError = $DEV;
		}
		
		if ($this->DontCacheXSLT == "AS DEV") {
			$this->DontCacheXSLT = $DEV;
		}
	}

	public function Param($name) {
		return (isset($this->$name) ? $this->$name : false);
	}

}

?>
