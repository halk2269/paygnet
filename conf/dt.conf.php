<?php

class DTConfClass {

	public $dtn;
	public $dteh;
	public $dtt;
	public $dtf;
	public $dttbl;
	
	static private $instance; 
	
	static public function GetInstance() {
		if (!self::$instance instanceof DTConfClass) {
			self::$instance = new DTConfClass();
		}
		
		return self::$instance;
	}

	public function __construct() {

		/* Пустой */

		$this->dtn["blank"] = "Пустой";

		$this->dtt["blank"] = "";


		/* Модуль, или связь (reference) между секцией и модулем */

		$this->dtn["ref"] = "Связь";

		$this->dtt["ref"] = ""; // В данном ТД не используется

		$this->dtf["ref"]["filename"]["type"] = "string";
		$this->dtf["ref"]["filename"]["leng"] = 255;
		$this->dtf["ref"]["filename"]["desc"] = "Имя модуля";
		$this->dtf["ref"]["filename"]["impt"] = true;

		$this->dtf["ref"]["class"]["type"] = "string";
		$this->dtf["ref"]["class"]["leng"] = 50;
		$this->dtf["ref"]["class"]["desc"] = "Имя класса";
		$this->dtf["ref"]["class"]["impt"] = true;

		$this->dtf["ref"]["name"]["type"] = "string";
		$this->dtf["ref"]["name"]["leng"] = 50;
		$this->dtf["ref"]["name"]["desc"] = "Имя связи";
		$this->dtf["ref"]["name"]["impt"] = true;

		$this->dtf["ref"]["xslt"]["type"] = "string";
		$this->dtf["ref"]["xslt"]["leng"] = 50;
		$this->dtf["ref"]["xslt"]["desc"] = "XSLT-шаблон";
		$this->dtf["ref"]["xslt"]["impt"] = false;

		$this->dtf["ref"]["param"]["type"] = "text";
		$this->dtf["ref"]["param"]["desc"] = "Дополнительные параметры";
		$this->dtf["ref"]["param"]["impt"] = false;



		/* Новость */

		$this->dtn["news"] = "News";

		$this->dtt["news"] = "#doc/newsitem.xslt";

		$this->dtf["news"]["title"]["type"] = "string";
		$this->dtf["news"]["title"]["leng"] = 255;
		$this->dtf["news"]["title"]["desc"] = "Заголовок новости";
		$this->dtf["news"]["title"]["impt"] = true;
		$this->dtf["news"]["title"]["srch"] = true;

		$this->dtf["news"]["pubdate"]["type"] = "date";
		$this->dtf["news"]["pubdate"]["desc"] = "Дата";
		$this->dtf["news"]["pubdate"]["view"] = "d.m.Y";
		$this->dtf["news"]["pubdate"]["impt"] = true;
		$this->dtf["news"]["pubdate"]["deft"] = "NOW()";

		$this->dtf["news"]["preview"]["type"] = "text";
		$this->dtf["news"]["preview"]["desc"] = "Краткое описание (на главную страницу)";
		$this->dtf["news"]["preview"]["impt"] = false;
		$this->dtf["news"]["preview"]["mode"] = "simple";

		$this->dtf["news"]["text"]["type"] = "text";
		$this->dtf["news"]["text"]["desc"] = "Полный текст";
		$this->dtf["news"]["text"]["impt"] = false;
		$this->dtf["news"]["text"]["srch"] = true;
		$this->dtf["news"]["text"]["mode"] = "simple";



		/* Event */

		$this->dtn["event"] = "Event";

		$this->dtt["event"] = "#doc/articleitem.xslt";

		$this->dtf["event"]["title"]["type"] = "string";
		$this->dtf["event"]["title"]["leng"] = 255;
		$this->dtf["event"]["title"]["desc"] = "Заголовок";
		$this->dtf["event"]["title"]["impt"] = true;

		$this->dtf["event"]["pubdate"]["type"] = "date";
		$this->dtf["event"]["pubdate"]["desc"] = "Дата";
		$this->dtf["event"]["pubdate"]["view"] = "d.m.Y";
		$this->dtf["event"]["pubdate"]["impt"] = false;
		$this->dtf["event"]["pubdate"]["deft"] = "NOW()";

		$this->dtf["event"]["preview"]["type"] = "text";
		$this->dtf["event"]["preview"]["desc"] = "Анонс";
		$this->dtf["event"]["preview"]["impt"] = false;
		$this->dtf["event"]["preview"]["mode"] = "simple";

		$this->dtf["event"]["text"]["type"] = "text";
		$this->dtf["event"]["text"]["desc"] = "Полный текст";
		$this->dtf["event"]["text"]["impt"] = false;
		$this->dtf["event"]["text"]["srch"] = true;
		$this->dtf["event"]["text"]["mode"] = "wyswyg";

		
		
		/* Award */
		
		$this->dtn["award"] = "Award";
		
		$this->dtt["award"] = "";
		
		$this->dtf["award"]["title"]["type"] = "string";
		$this->dtf["award"]["title"]["leng"] = 255;
		$this->dtf["award"]["title"]["desc"] = "Title";
		$this->dtf["award"]["title"]["impt"] = true;
		
		$this->dtf["award"]["description"]["type"] = "text";
		$this->dtf["award"]["description"]["desc"] = "Description";
		$this->dtf["award"]["description"]["impt"] = false;
		$this->dtf["award"]["description"]["srch"] = true;
		
		$this->dtf["award"]["smallimg"]["type"] = "image";
		$this->dtf["award"]["smallimg"]["desc"] = "Small image (smaller 200x200)";
		$this->dtf["award"]["smallimg"]["impt"] = false;
		$this->dtf["award"]["smallimg"]["exts"] = "jpg, jpeg, gif, png";
		$this->dtf["award"]["smallimg"]["twid"] = 200;
		$this->dtf["award"]["smallimg"]["thei"] = 200;
		$this->dtf["award"]["smallimg"]["imil"] = true;
		$this->dtf["award"]["smallimg"]["imqu"] = 85;
		$this->dtf["award"]["smallimg"]["gdil"] = false;
		$this->dtf["award"]["smallimg"]["gdqu"] = 75;
		$this->dtf["award"]["smallimg"]["isth"] = "bigimg";
		
		$this->dtf["award"]["bigimg"]["type"] = "image";
		$this->dtf["award"]["bigimg"]["desc"] = "Big image (from 300х300 to 1024х768)";
		$this->dtf["award"]["bigimg"]["impt"] = false;
		$this->dtf["award"]["bigimg"]["exts"] = "jpg, jpeg, png";
		$this->dtf["award"]["bigimg"]["maxs"] = 2 * 1024 * 1024;
		$this->dtf["award"]["bigimg"]["maxw"] = 1024;
		$this->dtf["award"]["bigimg"]["maxh"] = 768;
		$this->dtf["award"]["bigimg"]["minw"] = 300;
		$this->dtf["award"]["bigimg"]["minh"] = 300;
		
		$this->dtf["award"]["offer"]["type"] = "select";
		$this->dtf["award"]["offer"]["list"] = 4;
		$this->dtf["award"]["offer"]["desc"] = "Offer for";
		$this->dtf["award"]["offer"]["impt"] = false;
		$this->dtf["award"]["offer"]["deft"] = 27;
		
		
	
		/* Team */

		$this->dtn["team"] = "Team";

		$this->dtt["team"] = "#doc/teamitem.xslt";

		$this->dtf["team"]["title"]["type"] = "string";
		$this->dtf["team"]["title"]["leng"] = 255;
		$this->dtf["team"]["title"]["desc"] = "Title";
		$this->dtf["team"]["title"]["impt"] = true;

		$this->dtf["team"]["description"]["type"] = "text";
		$this->dtf["team"]["description"]["desc"] = "Description";
		$this->dtf["team"]["description"]["impt"] = true;
		$this->dtf["team"]["description"]["leng"] = 800;

		$this->dtf["team"]["state"]["type"] = "string";
		$this->dtf["team"]["state"]["desc"] = "State";
		$this->dtf["team"]["state"]["impt"] = false;
		$this->dtf["team"]["state"]["leng"] = 100;

		$this->dtf["team"]["city"]["type"] = "string";
		$this->dtf["team"]["city"]["desc"] = "City";
		$this->dtf["team"]["city"]["impt"] = true;
		$this->dtf["team"]["city"]["leng"] = 255;

		
		
		/* School */
		
		$this->dtn["school"] = "School";
		
		$this->dtt["school"] = "#doc/schoolitem.xslt";
		
		$this->dtf["school"]["title"]["type"] = "string";
		$this->dtf["school"]["title"]["leng"] = 255;
		$this->dtf["school"]["title"]["desc"] = "Title";
		$this->dtf["school"]["title"]["impt"] = true;
		
		$this->dtf["school"]["description"]["type"] = "text";
		$this->dtf["school"]["description"]["desc"] = "Description";
		$this->dtf["school"]["description"]["impt"] = true;
		$this->dtf["school"]["description"]["leng"] = 800;
		
		$this->dtf["school"]["state"]["type"] = "string";
		$this->dtf["school"]["state"]["desc"] = "State";
		$this->dtf["school"]["state"]["impt"] = false;
		$this->dtf["school"]["state"]["leng"] = 100;
		
		$this->dtf["school"]["city"]["type"] = "string";
		$this->dtf["school"]["city"]["desc"] = "City";
		$this->dtf["school"]["city"]["impt"] = true;
		$this->dtf["school"]["city"]["leng"] = 255;
		
		
		
		/* Classes */
		
		$this->dtn["classes"] = "Classes";
		
		$this->dtt["classes"] = "";
		
		$this->dtf["classes"]["source"]["type"] = "string";
		$this->dtf["classes"]["source"]["leng"] = 255;
		$this->dtf["classes"]["source"]["desc"] = "URL";
		$this->dtf["classes"]["source"]["impt"] = true;
		
		$this->dtf["classes"]["width"]["type"] = "int";
		$this->dtf["classes"]["width"]["desc"] = "Width";
		$this->dtf["classes"]["width"]["impt"] = false;
		$this->dtf["classes"]["width"]["deft"] = 0;
		
		$this->dtf["classes"]["height"]["type"] = "int";
		$this->dtf["classes"]["height"]["desc"] = "Height";
		$this->dtf["classes"]["height"]["impt"] = false;
		$this->dtf["classes"]["height"]["deft"] = 0;
		
		$this->dtf["classes"]["description"]["type"] = "text";
		$this->dtf["classes"]["description"]["desc"] = "Description";
		$this->dtf["classes"]["description"]["impt"] = true;
		$this->dtf["classes"]["description"]["leng"] = 800;
		
		
		
		/* Текст */

		$this->dtn["text"] = "Текст";

		$this->dtt["text"] = "doc/text.xslt";

		$this->dteh["text"] = true; // "eh" - Enabled is Hidden. Прячет поле enabled от обычных админов

		$this->dtf["text"]["text"]["type"] = "text";
		$this->dtf["text"]["text"]["desc"] = "Текст";
		$this->dtf["text"]["text"]["impt"] = false;
		$this->dtf["text"]["text"]["srch"] = true;



		/* HTML-код */

		$this->dtn["html"] = "HTML-код";

		$this->dtt["html"] = "#doc/text.xslt";

		$this->dteh["html"] = true;

		$this->dtf["html"]["text"]["type"] = "text";
		$this->dtf["html"]["text"]["mode"] = "html";
		$this->dtf["html"]["text"]["desc"] = "HTML-код";
		$this->dtf["html"]["text"]["impt"] = false;
		$this->dtf["html"]["text"]["srch"] = false;



		/* ТД для формы*/

		$this->dtn["form"] = "Форма";

		$this->dtt["form"] = "";

		$this->dtf["form"]["title"]["type"] = "string";
		$this->dtf["form"]["title"]["leng"] = 255;
		$this->dtf["form"]["title"]["desc"] = "Заголовок";
		$this->dtf["form"]["title"]["impt"] = true;
		$this->dtf["form"]["title"]["srch"] = true;

		$this->dtf["form"]["email"]["type"] = "string";
		$this->dtf["form"]["email"]["leng"] = 255;
		$this->dtf["form"]["email"]["desc"] = "E-mail адрес";
		$this->dtf["form"]["email"]["impt"] = true;
		$this->dtf["form"]["email"]["rexp"] = "/^[-a-z0-9_.]+@([-a-z0-9]+\.)+[a-z]{2,4}$/i";
		$this->dtf["form"]["email"]["rphr"] = "Введённое значение не является корректным e-mail адресом";



		/* Контакты */

		$this->dtn["contacts"] = "E-mail from site";

		$this->dtt["contacts"] = "#doc/contactsitem.xslt";

		$this->dtf["contacts"]["name"]["type"] = "string";
		$this->dtf["contacts"]["name"]["leng"] = 30;
		$this->dtf["contacts"]["name"]["desc"] = "Your name";
		$this->dtf["contacts"]["name"]["impt"] = true;

		$this->dtf["contacts"]["email"]["type"] = "string";
		$this->dtf["contacts"]["email"]["leng"] = 40;
		$this->dtf["contacts"]["email"]["desc"] = "E-mail";
		$this->dtf["contacts"]["email"]["impt"] = true;
		$this->dtf["contacts"]["email"]["rexp"] = "/^[-a-z0-9_.]+@([-a-z0-9]+\.)+[a-z]{2,4}$/i";
		$this->dtf["contacts"]["email"]["rphr"] = "Введённое значение не является корректным e-mail адресом";

		$this->dtf["contacts"]["theme"]["type"] = "string";
		$this->dtf["contacts"]["theme"]["leng"] = 30;
		$this->dtf["contacts"]["theme"]["desc"] = "Theme";
		$this->dtf["contacts"]["theme"]["impt"] = true;

		$this->dtf["contacts"]["answer"]["type"] = "text";
		$this->dtf["contacts"]["answer"]["leng"] = 2000;
		$this->dtf["contacts"]["answer"]["desc"] = "Question";
		$this->dtf["contacts"]["answer"]["impt"] = true;
		$this->dtf["contacts"]["answer"]["mode"] = "nl2br";

		

		/* Пользователь */

		$this->dtn["user"] = "Пользователь";

		$this->dtt["user"] = "doc/user.xslt";

		$this->dtf["user"]["login"]["type"] = "string";
		$this->dtf["user"]["login"]["leng"] = 20;
		$this->dtf["user"]["login"]["desc"] = "Login";
		$this->dtf["user"]["login"]["impt"] = true;
		$this->dtf["user"]["login"]["rexp"] = "/^[-_0-9a-zA-Z]{3,32}$/";
		$this->dtf["user"]["login"]["rphr"] = "Логин может состоять только из символов латинского алфавита, цифр, дефиса и знака подчёркивания. Логин должен содержать в себе от 3 до 32 символов";

		$this->dtf["user"]["pass"]["type"] = "password";
		$this->dtf["user"]["pass"]["desc"] = "Password";
		$this->dtf["user"]["pass"]["impt"] = true;
		$this->dtf["user"]["pass"]["rexp"] = "/^[0-9a-zA-Z]{3,32}$/";
		$this->dtf["user"]["pass"]["rphr"] = "Пароль может состоять только из цифр и символов латинского алфавита. Пароль должен быть длиной от 3 до 32 символов";

		$this->dtf["user"]["email"]["type"] = "string";
		$this->dtf["user"]["email"]["leng"] = 200;
		$this->dtf["user"]["email"]["desc"] = "Email address";
		$this->dtf["user"]["email"]["impt"] = false;
		$this->dtf["user"]["email"]["rexp"] = "/^[-a-z0-9_.]+@([-a-z0-9]+\.)+[a-z]{2,4}$/i";
		$this->dtf["user"]["email"]["rphr"] = "Введённое значение не является корректным e-mail адресом";

		$this->dtf["user"]["role_id"]["type"] = "int";
		$this->dtf["user"]["role_id"]["desc"] = "User role";
		$this->dtf["user"]["role_id"]["impt"] = false;
		$this->dtf["user"]["role_id"]["hiddenfromall"] = true;


		$this->dtn["user_admin"] = "Администратор";

		$this->dtt["user_admin"] = "";

		$this->dtf["user_admin"]["extra"]["type"] = "string";
		$this->dtf["user_admin"]["extra"]["leng"] = 200;
		$this->dtf["user_admin"]["extra"]["desc"] = "Дополнительное поле";
		$this->dtf["user_admin"]["extra"]["impt"] = true;
		
		
		$this->dtn["user_team_member"] = "Team member";
		
		$this->dtt["user_team_member"] = "";
		
		$this->dtf["user_team_member"]["name"]["type"] = "string";
		$this->dtf["user_team_member"]["name"]["leng"] = 255;
		$this->dtf["user_team_member"]["name"]["desc"] = "Name";
		$this->dtf["user_team_member"]["name"]["impt"] = true;
		
		$this->dtf["user_team_member"]["surname"]["type"] = "string";
		$this->dtf["user_team_member"]["surname"]["leng"] = 255;
		$this->dtf["user_team_member"]["surname"]["desc"] = "Surname";
		$this->dtf["user_team_member"]["surname"]["impt"] = true;
		
		$this->dtf["user_team_member"]["school"]["type"] = "link";
		$this->dtf["user_team_member"]["school"]["doct"] = "school";
		$this->dtf["user_team_member"]["school"]["tdtt"] = "title";
		$this->dtf["user_team_member"]["school"]["desc"] = "School";
		
		$this->dtf["user_team_member"]["state"]["type"] = "string";
		$this->dtf["user_team_member"]["state"]["leng"] = 100;
		$this->dtf["user_team_member"]["state"]["desc"] = "State";
		$this->dtf["user_team_member"]["state"]["impt"] = true;
		
		$this->dtf["user_team_member"]["city"]["type"] = "string";
		$this->dtf["user_team_member"]["city"]["leng"] = 255;
		$this->dtf["user_team_member"]["city"]["desc"] = "City";
		$this->dtf["user_team_member"]["city"]["impt"] = true;
		
		$this->dtf["user_team_member"]["link_team"]["type"] = "link";
		$this->dtf["user_team_member"]["link_team"]["doct"] = "team";
		$this->dtf["user_team_member"]["link_team"]["tdtt"] = "title";
		$this->dtf["user_team_member"]["link_team"]["desc"] = "Team";
		
		$this->dtf["user_team_member"]["smallimg"]["type"] = "image";
		$this->dtf["user_team_member"]["smallimg"]["desc"] = "Small image (smaller then 200x200)";
		$this->dtf["user_team_member"]["smallimg"]["impt"] = false;
		$this->dtf["user_team_member"]["smallimg"]["exts"] = "jpg, jpeg, gif, png";
		$this->dtf["user_team_member"]["smallimg"]["twid"] = 200;
		$this->dtf["user_team_member"]["smallimg"]["thei"] = 200;
		$this->dtf["user_team_member"]["smallimg"]["imil"] = true;
		$this->dtf["user_team_member"]["smallimg"]["imqu"] = 85;
		$this->dtf["user_team_member"]["smallimg"]["gdil"] = false;
		$this->dtf["user_team_member"]["smallimg"]["gdqu"] = 75;
		$this->dtf["user_team_member"]["smallimg"]["isth"] = "bigimg";
		
		$this->dtf["user_team_member"]["bigimg"]["type"] = "image";
		$this->dtf["user_team_member"]["bigimg"]["desc"] = "Your picture (size from 300х300 to 1024х768)";
		$this->dtf["user_team_member"]["bigimg"]["impt"] = false;
		$this->dtf["user_team_member"]["bigimg"]["exts"] = "jpg, jpeg, png";
		$this->dtf["user_team_member"]["bigimg"]["maxs"] = 2 * 1024 * 1024;
		$this->dtf["user_team_member"]["bigimg"]["maxw"] = 1024;
		$this->dtf["user_team_member"]["bigimg"]["maxh"] = 768;
		$this->dtf["user_team_member"]["bigimg"]["minw"] = 300;
		$this->dtf["user_team_member"]["bigimg"]["minh"] = 300;

		
		
		$this->dtn["user_official"] = "School director";
		
		$this->dtt["user_official"] = "";
		
		$this->dtf["user_official"]["name"]["type"] = "string";
		$this->dtf["user_official"]["name"]["leng"] = 255;
		$this->dtf["user_official"]["name"]["desc"] = "Name";
		$this->dtf["user_official"]["name"]["impt"] = true;
		
		$this->dtf["user_official"]["surname"]["type"] = "string";
		$this->dtf["user_official"]["surname"]["leng"] = 255;
		$this->dtf["user_official"]["surname"]["desc"] = "Surname";
		$this->dtf["user_official"]["surname"]["impt"] = true;
		
		$this->dtf["user_official"]["position"]["type"] = "string";
		$this->dtf["user_official"]["position"]["leng"] = 255;
		$this->dtf["user_official"]["position"]["desc"] = "Position";
		$this->dtf["user_official"]["position"]["impt"] = true;
		
		$this->dtf["user_official"]["school"]["type"] = "link";
		$this->dtf["user_official"]["school"]["doct"] = "school";
		$this->dtf["user_official"]["school"]["tdtt"] = "title";
		$this->dtf["user_official"]["school"]["desc"] = "School";
		
		$this->dtf["user_official"]["state"]["type"] = "string";
		$this->dtf["user_official"]["state"]["leng"] = 100;
		$this->dtf["user_official"]["state"]["desc"] = "State";
		$this->dtf["user_official"]["state"]["impt"] = true;
		
		$this->dtf["user_official"]["city"]["type"] = "string";
		$this->dtf["user_official"]["city"]["leng"] = 255;
		$this->dtf["user_official"]["city"]["desc"] = "City";
		$this->dtf["user_official"]["city"]["impt"] = true;

		
		
		/* Тестовый тип */

		$this->dtn["test"] = "Тестовый тип";

		$this->dtt["test"] = "test/dt_test.xslt";

		$this->dtr["test"][0] = "#test";

		$this->dtf["test"]["tstring1"]["type"] = "string";
		$this->dtf["test"]["tstring1"]["leng"] = 200;
		$this->dtf["test"]["tstring1"]["desc"] = "Тестовый заголовок";
		$this->dtf["test"]["tstring1"]["impt"] = true;

		$this->dtf["test"]["ttable"]["type"] = "table";
		$this->dtf["test"]["ttable"]["desc"] = "Тестовая таблица";
		$this->dtf["test"]["ttable"]["impt"] = true;
		$this->dtf["test"]["ttable"]["hcnt"] = 1;
		$this->dtf["test"]["ttable"]["hspn"] = false;
		$this->dtf["test"]["ttable"]["bspn"] = true;
		$this->dtf["test"]["ttable"]["blnk"] = false;
		$this->dtf["test"]["ttable"]["mult"] = false;

		$this->dtf["test"]["tstring2"]["type"] = "string";
		$this->dtf["test"]["tstring2"]["leng"] = 100;
		$this->dtf["test"]["tstring2"]["desc"] = "Тестовый заголовок";
		$this->dtf["test"]["tstring2"]["impt"] = false;
		$this->dtf["test"]["tstring2"]["rexp"] = "/^[0-9]+$/";
		$this->dtf["test"]["tstring2"]["rphr"] = "Строка может состоять только из цифр";

		$this->dtf["test"]["ttext1"]["type"] = "text";
		$this->dtf["test"]["ttext1"]["desc"] = "Тестовый текст (simple)";
		$this->dtf["test"]["ttext1"]["impt"] = true;
		$this->dtf["test"]["ttext1"]["mode"] = "simple";

		$this->dtf["test"]["ttext2"]["type"] = "text";
		$this->dtf["test"]["ttext2"]["desc"] = "Тестовый текст (nl2br)";
		$this->dtf["test"]["ttext2"]["impt"] = true;
		$this->dtf["test"]["ttext2"]["mode"] = "nl2br";
		$this->dtf["test"]["ttext2"]["leng"] = 2000;

		$this->dtf["test"]["ttext3"]["type"] = "text";
		$this->dtf["test"]["ttext3"]["desc"] = "Тестовый текст (WYSWYG)";
		$this->dtf["test"]["ttext3"]["impt"] = true;
		$this->dtf["test"]["ttext3"]["mode"] = "wyswyg";

		$this->dtf["test"]["tdate"]["type"] = "date";
		$this->dtf["test"]["tdate"]["view"] = "d-m-Y";
		$this->dtf["test"]["tdate"]["desc"] = "Тестовая дата";
		$this->dtf["test"]["tdate"]["impt"] = true;
		$this->dtf["test"]["tdate"]["show"] = "selects";

		$this->dtf["test"]["tdatetime"]["type"] = "datetime";
		$this->dtf["test"]["tdatetime"]["view"] = "d-m-Y H:i";
		$this->dtf["test"]["tdatetime"]["desc"] = "Тестовая дата + время";
		$this->dtf["test"]["tdatetime"]["impt"] = true;
		$this->dtf["test"]["tdatetime"]["deft"] = "NOW()";
		$this->dtf["test"]["tdatetime"]["show"] = "selects";

		$this->dtf["test"]["tint"]["type"] = "int";
		$this->dtf["test"]["tint"]["desc"] = "Тестовое целое число";
		$this->dtf["test"]["tint"]["impt"] = true;
		$this->dtf["test"]["tint"]["deft"] = "120";
		$this->dtf["test"]["tint"]["minv"] = 100;
		$this->dtf["test"]["tint"]["maxv"] = 200;

		$this->dtf["test"]["tfloat"]["type"] = "float";
		$this->dtf["test"]["tfloat"]["desc"] = "Тестовое дробное число";
		$this->dtf["test"]["tfloat"]["impt"] = true;
		$this->dtf["test"]["tfloat"]["deft"] = "15.8";
		$this->dtf["test"]["tfloat"]["minv"] = 10;
		$this->dtf["test"]["tfloat"]["maxv"] = 20;
		$this->dtf["test"]["tfloat"]["sdgt"] = 3;

		$this->dtf["test"]["tpassword"]["type"] = "password";
		$this->dtf["test"]["tpassword"]["desc"] = "Тестовый пароль";
		$this->dtf["test"]["tpassword"]["impt"] = true;

		$this->dtf["test"]["tbool"]["type"] = "bool";
		$this->dtf["test"]["tbool"]["desc"] = "Тестовый логический тип";
		$this->dtf["test"]["tbool"]["deft"] = "0";
		$this->dtf["test"]["tbool"]["impt"] = true;

		$this->dtf["test"]["tfile"]["type"] = "file";
		$this->dtf["test"]["tfile"]["desc"] = "Тестовый файл";
		$this->dtf["test"]["tfile"]["impt"] = false;
		$this->dtf["test"]["tfile"]["exts"] = "doc, gif, png, swf";
		$this->dtf["test"]["tfile"]["maxs"] = 10 * 1024 * 1024;

		$this->dtf["test"]["ttable_file"]["type"] = "table";
		$this->dtf["test"]["ttable_file"]["desc"] = "Тестовая таблица";
		$this->dtf["test"]["ttable_file"]["impt"] = false;
		$this->dtf["test"]["ttable_file"]["hcnt"] = 1;
		$this->dtf["test"]["ttable_file"]["hspn"] = true;
		$this->dtf["test"]["ttable_file"]["bspn"] = false;
		$this->dtf["test"]["ttable_file"]["blnk"] = true;
		$this->dtf["test"]["ttable_file"]["mult"] = false;
		$this->dtf["test"]["ttable_file"]["file"] = "tfile_table";

		$this->dtf["test"]["tfile_table"]["type"] = "file";
		$this->dtf["test"]["tfile_table"]["desc"] = "Тестовый файл для формирования таблицы";
		$this->dtf["test"]["tfile_table"]["impt"] = false;
		$this->dtf["test"]["tfile_table"]["exts"] = "xls";
		$this->dtf["test"]["tfile_table"]["maxs"] = 10 * 10 * 1024;

		$this->dtf["test"]["timage"]["type"] = "image";
		$this->dtf["test"]["timage"]["desc"] = "Тестовое изображение";
		$this->dtf["test"]["timage"]["impt"] = false;
		$this->dtf["test"]["timage"]["exts"] = "jpg, jpeg, gif, png";
		$this->dtf["test"]["timage"]["maxs"] = 10 * 1024 * 1024;
		$this->dtf["test"]["timage"]["maxw"] = 800;
		$this->dtf["test"]["timage"]["maxh"] = 600;
		$this->dtf["test"]["timage"]["minw"] = 150;
		$this->dtf["test"]["timage"]["minh"] = 150;

		$this->dtf["test"]["tselect"]["type"] = "select";
		$this->dtf["test"]["tselect"]["list"] = 2;
		$this->dtf["test"]["tselect"]["desc"] = "Тестовый select";
		$this->dtf["test"]["tselect"]["impt"] = true;
		$this->dtf["test"]["tselect"]["deft"] = 2;

		$this->dtf["test"]["tstrlist"]["type"] = "strlist";
		$this->dtf["test"]["tstrlist"]["desc"] = "Тестовый strlist";
		$this->dtf["test"]["tstrlist"]["deft"] = "";
		$this->dtf["test"]["tstrlist"]["impt"] = true;

		$this->dtf["test"]["tarray"]["type"] = "array";
		$this->dtf["test"]["tarray"]["subt"] = "testa";
		$this->dtf["test"]["tarray"]["sort"] = "dt.id";
		$this->dtf["test"]["tarray"]["desc"] = "Элементы массива";

		$this->dtf["test"]["tlink"]["type"] = "link";
		$this->dtf["test"]["tlink"]["doct"] = "test";
		$this->dtf["test"]["tlink"]["tdtt"] = "tstring1";
		$this->dtf["test"]["tlink"]["desc"] = "Ссылка";

		$this->dtl["test"]["link"]["doct"] = "testa";
		$this->dtl["test"]["link"]["desc"] = "тестовый массив";
		$this->dtl["test"]["link"]["tdtt"] = "str";
		$this->dtl["test"]["link"]["both"] = false;

		
		$this->dtn["testa"] = "Тестовый элемент массива";

		$this->dtt["testa"] = "";

		$this->dtf["testa"]["str"]["type"] = "string";
		$this->dtf["testa"]["str"]["desc"] = "Тестовое описание элемента массива";
		$this->dtf["testa"]["str"]["leng"] = 255;
		$this->dtf["testa"]["str"]["impt"] = false;

		$this->dtf["testa"]["title"]["type"] = "string";
		$this->dtf["testa"]["title"]["desc"] = "Заголовок";
		$this->dtf["testa"]["title"]["leng"] = 255;
		$this->dtf["testa"]["title"]["impt"] = false;


	}
}

?>