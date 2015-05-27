<?php
/**
 * Класс, обеспечивающий XML-выдачу
 */
class XMLClass extends BaseClass {
	
	/**
	 * @var PassInfoClass
	 */
	private $passInfo;
	
	/**
	 * @var QueryClass
	 */
	private $query;
	
	/**
	 * @var ResponseClass
	 */
	private $response;

	// Параметры текущей секции
	private $SCID;
	private $SCOutMethod;
	private $SCTitle;
	private $SCXSLT;
	private $SCAuth;
	private $SCPath;
	private $headers = array();

	private $redirectPath = "";

	private $xml;
	private $xmlRoot;
	private $xslList = array();
	private $error404 = false;

	public function __construct($passInfo, $query, $response) {
		parent::__construct();
		
		$this->passInfo = $passInfo;
		$this->query = $query;
		$this->response = $response;

		$this->_SetXML();
	}

	/**
	 * Возвращает ссылку на корневую ноду дерева
	 * @return DOMElement
	 */
	public function GetXMLRoot() {
		return $this->xmlRoot;
	}

	public function GetXSLList() {
		return $this->xslList;
	}

	public function OnlyXMLNeeded() {
		return ("xml" == $this->SCOutMethod);
	}

	public function &GetHeaders() {
		return $this->headers;
	}

	/**
	 * Возвращает полное xml-дерево
	 * @return domelement
	 */
	public function GetXML() {
		return $this->xml;
	}

	private function _SetXML() {
		// Имя текущей секции
		$sectionCurrentName = ($this->error404 || $this->query->IsError404()) 
			? $this->globalvars->GetStr("Error404URL") 
			: $this->query->GetSCName();
			
		$row = $this->GetSectionRow($sectionCurrentName);

		// если такой секции нет, то отправляемся выбирать параметры для секции404
		if (!$row) {
			$this->error404 = true;
			$this->response->SetError404Header();
			$this->_SetXML();
			return;
		}

		$this->SCID = $row->id;
		$this->SCOutMethod = $row->out;
		$this->SCTitle = $row->title;
		$this->SCXSLT = $row->xslt;
		$this->SCAuth = $row->auth;
		$this->SCPath = $row->path;

		// Содержимое текущей секции (в отдельном дереве)
		$sectionCurrentTree = $this->GetSectionCurrent();

		// Если для текущий секции не получено ничего хорошего,
		// то очищаем xslList и headers и отправляем на генерацию 404
		if (!($sectionCurrentTree instanceof DOMElement)) {
			// если есть редирект - то отправляем
			if ($this->redirectPath) {
				$this->response->Redirect($this->redirectPath);
				return;
			}

			$this->error404 = true;
			$this->response->SetError404Header();
			unset($this->headers);
			unset($this->xslList);
			$this->_SetXML();
			return;
		}

		// Основа xml-документа
		$this->xml = new DOMDocument("1.0", "UTF-8");
		$this->xmlRoot = $this->xml->createElement("root");
		$this->xml->appendChild($this->xmlRoot);

		// Смотрим, нужны ли нам дерево секций, информация о пользователе, запросе и т. д.,
		// или нам нужен только XML
		if ("xml" == $this->SCOutMethod) {
			$importedNode = $this->xml->importNode($sectionCurrentTree, true);
			$this->xmlRoot->appendChild($importedNode);
		} else {
			// Можно ли это добавлять и при выводе типа "xml"? Тогда бы вынесли из if блока
			$this->xmlRoot->setAttribute("date", date("Y-m-d"));
			$this->xmlRoot->setAttribute("time", date("H:i:s"));

			// Есть ли кэшированное дерево секций? Если нет - то создаем
			$tree = $this->cache->GetSectionTree();
			
			if (!$tree instanceof DOMElement) {
				$tree = $this->GetSectionTree();
				$this->cache->SaveSectionTree($tree);
			}
			
			// Дерево секций
			$this->xmlRoot->appendChild($this->xml->importNode($tree, true));
			
			// Информация о пользователе
			$this->xmlRoot->appendChild($this->GetVisitor());
			
			// Текущая секция
			$this->xmlRoot->appendChild($this->xml->importNode($sectionCurrentTree, true));
			
			// Параметры запроса
			$this->xmlRoot->appendChild($this->GetQueryParams());
		}
		
	}
	
	private function GetSectionRow($SCName) {
		return $this->db->GetRow(
			"SELECT 
				id, title, `out`, path, xslt, auth 
			FROM 
				sys_sections 
			WHERE 
				name = ? AND enabled = ?",
			array($SCName, 1)
		);
	}

	private function GetSectionTree() {
		$xml = new DOMDocument("1.0", "UTF-8");
		$sectionTreeNode = $xml->createElement("SectionTree");
		$xml->appendChild($sectionTreeNode);
		$this->_BuildSectionTreeNode($xml, $sectionTreeNode, 0);
		return $sectionTreeNode;
	}

	private function _BuildSectionTreeNode($xml, $rootNode, $rootID) {
		// Выбираем секции, являющиеся непосредственными потомками секции с id = $rootID
		$stmt = $this->db->SQL("
			SELECT 
				id, enabled, name, title, hidden, onmap, `out`, 
				auth, redirect_url, go_to_child 
			FROM 
				sys_sections 
			WHERE 
				parent_id = '{$rootID}' AND enabled = 1 
			ORDER BY sort
		");
		
		if (!$stmt->rowCount()) {
			return;
		}
		
		while ($row = $stmt->fetchObject()) {
			$newNode = $xml->createElement("section");
			$newNode->setAttribute("id", $row->id);
			$newNode->setAttribute("name", $row->name);
			$newNode->setAttribute("title", $row->title);
			$newNode->setAttribute("hidden", $row->hidden);
			$newNode->setAttribute("onMap", $row->onmap);
			$newNode->setAttribute("out", $row->out);
			$newNode->setAttribute("auth", $row->auth);
			$newNode->setAttribute(
				"content", 
				$this->db->RowExists("SELECT id FROM sys_references WHERE enabled = 1 AND ref = {$row->id}")
			);
			
			if ($row->redirect_url != '') {
				// если есть URL переадресации
				$newNode->setAttribute("isRedirect", "1");
				$newNode->setAttribute("URL", $row->redirect_url);
			} elseif ($row->go_to_child == 1) {
				// если включен переход к первой дочерней секции
				$newNode->setAttribute("goToChild", 1);
				// селектируем из базы первую дочернюю
				// если у первой дочерней есть URL переадресации или включен переход к первой дочерней
				$stmtChild = $this->db->SQL("
					SELECT 
						id, name, go_to_child 
					FROM 
						sys_sections 
					WHERE 
						parent_id = {$row->id} 
					ORDER BY 
						sort
				");
				
				// проверка наличия детей
				$first = $stmtChild->rowCount();
				if ($first) {
					$row2 = $stmtChild->fetchObject();
					// цикл по всем дочерним секциям текущей дочерней секции, пока не найдем секцию, у которой go_to_child = 0
					$doAgain = true;
					
					while ($row2->go_to_child != 0 && $doAgain) {
						$stmtRecursive = $this->db->SQL(
							"SELECT id, name, go_to_child FROM sys_sections WHERE parent_id = {$row2->id} ORDER BY sort"
						);
						// внутренняя проверка детей. Введена для устранения конфликта с внешней
						$doAgain = $stmtRecursive->fetchObject();
						
						// рекурсия
						if ($doAgain) {
							$row2 = $stmtRecursive->fetchObject();
						}
					}
				}
				
				$tmpName = (!$first) ? $row->name : $row2->name;
				$newNode->setAttribute("URL", $this->conf->Param("Prefix") . $tmpName . "/");
			} else {
				$newNode->setAttribute("URL", $this->conf->Param("Prefix") . $row->name . "/");
			}

			// выводим информацию о мета-тегах
			$resMeta = $this->db->SQL("SELECT id, name, content FROM sys_section_meta WHERE ref = {$row->id}");
			if ($resMeta->rowCount()) {
				while ($rowMeta = $resMeta->fetchObject()) {
					$newMetaNode = $xml->createElement("meta", $rowMeta->content);
					$newMetaNode->setAttribute("id", $rowMeta->id);
					$newMetaNode->setAttribute("name", $rowMeta->name);
					$newNode->appendChild($newMetaNode);
				}
			}

			$rootNode->appendChild($newNode);
			
			// Рекурсивно вызываем эту же функцию. Это долго. Надо подключить кэш
			$this->_BuildSectionTreeNode($xml, $newNode, $row->id);
		}
	}

	/**
	 * @return DOMElement
	 */
	private function GetVisitor() {
		$parentNode = $this->xml->createElement("Visitor");
		// Если пользователь не авторизован, то ничего в ноде Visitor не создаем
		if (!$this->auth->IsAuth()) {
			return $parentNode;
		}

		$parentNode->setAttribute("id", $this->auth->GetUserID());
		$parentNode->setAttribute("login", $this->auth->GetUserLogin());
		$roleName = $this->auth->GetRoleName();
		if ($roleName) {
			$roleNode = $this->xml->createElement("role");
			$roleNode->setAttribute("name", $this->auth->GetRoleName());
			$roleNode->setAttribute("dtSuperAccess", $this->auth->IsDTSuperAccess() ? "1" : "0");
			$roleNode->setAttribute("listEdit", $this->auth->CanEditLists() ? "1" : "0");
			$parentNode->appendChild($roleNode);
		}
		$sql = $this->dt->FormatSelectQuery("user", $this->xml, $parentNode, "*", "", "", "dt.id = " . $this->auth->GetUserID());
		$this->dt->ProcessQueryResults($sql, $this->xml, $parentNode, "user", false, false, 0, "", false, null, "user");

		/**
		 * Получение данных из таблицы, прикрепленной к роли пользователя,
		 * если такая таблица существует
		 **/
		$additionalData = "user_" . $roleName;

		if (isset($this->dtconf->dtf[$additionalData])) {
			$sql = $this->dt->FormatSelectQuery($additionalData, $this->xml, $parentNode, "*", "", "", "dt.id = " . $this->auth->GetUserID());
			$this->dt->ProcessQueryResults($sql, $this->xml, $parentNode, $additionalData, false, false, 0, "", false, null, "userdata");
		}

		return $parentNode;
	}

	/**
	 * Получение выборки модулей, для которых у текущего пользователя есть право на чтение.
	 */
	private function GetModulesSQLResult() {
		return $this->db->SQL("
			SELECT 
				r.id, 
				r.ref, 
				r.name, 
				r.class, 
				r.filename, 
				r.xslt, 
				r.params, 
				r.inherited, 
				r.loadinfo,
				IF(ISNULL(rr.rights), '" . $this->auth->GetDefRights() . "', rr.rights) AS ref_rights
			FROM 
				sys_references r
			LEFT JOIN 
				sys_ref_rights rr ON rr.ref_id = r.id AND rr.role_id = " . $this->auth->GetRoleID() . "
			WHERE 
				(r.ref = '{$this->SCID}' OR (r.ref IN ({$this->SCPath}) AND r.inherited = 1)) 
				AND r.enabled = 1 
				AND ((ISNULL(rr.rights) AND " . substr($this->auth->GetDefRights(), 0, 1) . " = 1) OR SUBSTRING(rr.rights FROM 1 FOR 1) = 1)
			ORDER BY priority DESC
		");
	}

	/**
	 * Результат выборки из БД модуля со страницей авторизации
	 */
	private function GetAuthModuleSQLResult($params) {
		$params = $this->db->quote($params);
		
		return $this->db->SQL("
			SELECT 
				r.id, 
				r.ref, 
				r.name, 
				r.class, 
				r.filename, 
				r.xslt, 
				'{$params}' AS params, 
				r.inherited, 
				r.loadinfo,
				IF(ISNULL(rr.rights), '" . $this->auth->GetDefRights() . "', rr.rights) AS ref_rights
			FROM 
				sys_references r
			LEFT JOIN 
				sys_ref_rights rr ON rr.ref_id = r.id AND rr.role_id = " . $this->auth->GetRoleID() . "
			WHERE 
				r.id = " . $this->globalvars->GetInt("AuthRef") . "
		");
	}

	/**
	 * @param string $rightsStr
	 * @return array
	 */
	private function GetRefRightsArray($rightsStr) {
		$rights = array();
		
		$rights["Read"] = ($rightsStr[0] == 1);
		$rights["Create"] = ($rightsStr[1] == 1);
		$rights["CreateEnabled"] = ($rightsStr[2] == 1);
		$rights["Edit"] = ($rightsStr[3] == 1);
		$rights["Delete"] = ($rightsStr[4] == 1);
		
		return $rights;
	}


	/**
	 * @param DOMDocument $xml
	 * @return DOMElement
	 */
	private function GetSectionNode(DOMDocument $xml) {
		$section = $xml->createElement("section");
		$section->setAttribute("id", $this->SCID);
		$section->setAttribute("name", $this->query->GetSCName());
		$section->setAttribute("title", $this->SCTitle);
		$section->setAttribute("out", $this->SCOutMethod);
		$section->setAttribute("xslt", $this->SCXSLT);
		$section->setAttribute("auth", $this->SCAuth);
		
		return $section;
	}

	/**
	 * @param DOMDocument $xml
	 * @param $row
	 * @return DOMElement
	 */
	private function GetModuleNode(DOMDocument $xml, stdClass $row) {
		$rights = $this->GetRefRightsArray($row->ref_rights);
		$adminMode = ($rights["CreateEnabled"] || $rights["Edit"] || $rights["Delete"]);

		// Создаём ноду модуля
		$moduleXML = $xml->createElement("module");

		// Добавляем атрибуты ноде модуля
		$moduleXML->setAttribute("id", $row->id);
		if (isset($row->name) && $row->name) {
			$moduleXML->setAttribute("name", $row->name);
		}
		
		$moduleXML->setAttribute("class", $row->class);
		$moduleXML->setAttribute("xslt", $row->xslt);
		$moduleXML->setAttribute("inherited", $row->inherited);
		
		if ($row->inherited) {
			$moduleXML->setAttribute("ownerSection", $row->ref);
		}
		
		$moduleXML->setAttribute("paramPrefix", "r" . $row->id . "_");

		// Выводим права в XML
		$moduleRights = $xml->createElement("rights");
		$moduleRights->setAttribute("read", $rights["Read"] ? "1" : "0");
		$moduleRights->setAttribute("create", $rights["Create"] ? "1" : "0");
		$moduleRights->setAttribute("createEnabled", $rights["CreateEnabled"] ? "1" : "0");
		$moduleRights->setAttribute("edit", $rights["Edit"] ? "1" : "0");
		$moduleRights->setAttribute("delete", $rights["Delete"] ? "1" : "0");
		$moduleRights->setAttribute("adminMode", $adminMode ? "1" : "0");
		
		$moduleXML->appendChild($moduleRights);

		// Подключаем информацию, пришедшую из модуля записи
		if (1 == $row->loadinfo) {
			$this->passInfo->ExportInfoAndErrors($row->id, $xml, $moduleXML);
			$this->passInfo->ExportVars($row->id, $xml, $moduleXML);
		}

		return $moduleXML;
	}

	/**
	 * @return mixed
	 */
	private function GetSectionCurrent() {
		$xml = new DOMDocument();
		$rootNode = $xml->createElement(
			("xml" == $this->SCOutMethod) ? "root" : "SectionCurrent"
		);
		
		$xml->appendChild($rootNode);

		if ($this->SCOutMethod != "xml") {
			$rootNode->appendChild($this->GetSectionNode($xml));
		}

		$secRights = $this->auth->GetSectionRights($this->SCID);
		if ($secRights["Read"]) {
			if (!$this->OnlyXMLNeeded()) {
				// Подключаем XSLT, назначенный секции
				$this->xslList[] = array(
					"filename" => $this->SCXSLT, 
					"match" => "/root/SectionCurrent"
				);
			}

			$stmt = $this->GetModulesSQLResult();
		} else {
			// Подключаем XSLT с просьбой авторизоваться
			$this->xslList[] = array(
				"filename" => $this->globalvars->GetStr("Template_AuthBase"), 
				"match" => "/root/SectionCurrent"
			);
			
			$stmt = $this->GetAuthModuleSQLResult("\$AuthType = \"{$this->SCAuth}\";");
		}


		while ($row = $stmt->fetchObject()) {
			$rights = $this->GetRefRightsArray($row->ref_rights);
			$adminMode = ($rights["CreateEnabled"] or $rights["Edit"] or $rights["Delete"]);
			if ($this->SCOutMethod != "xml") {
				$moduleXML = $this->GetModuleNode($xml, $row);
				$rootNode->appendChild($moduleXML);
			} else {
				$moduleXML = $rootNode;
			}

			// Подключаем файл с модулем и создаем объект модуля
			require_once(GenPath($row->filename, CMSPATH_MOD_READ, CMSPATH_PMOD_READ));
			
			$readClassName = $row->class;
			$module = new $readClassName(
				$row->id, $this->query, $xml, $moduleXML, $row->params, 
				$this->xslList, $this->headers, $rights, $adminMode
			);
						
			// Если модулю не сопоставлен шаблон, назначаем ему "пустой" шаблон, иначе подключаем указанный шаблон
			$xslFile = ($row->xslt != '') ? $row->xslt : $this->globalvars->GetStr("Template_Blank");
			$this->xslList[] = array(
				"filename" => $xslFile, 
				"match" => "module[@id = {$row->id}]"
			);

			$isOK = $module->CreateXML();

			// редирект
			if ($module->redirectPath) {
				$this->redirectPath = $module->redirectPath;
				return null;
			}

			// 404
			if (!$isOK && !$module->GetBadParamsDescr() && !$module->GetAccessDenied()) {
				return null;
			}

			// Ошибка в параметрах
			if ($module->GetBadParamsDescr()) {
				$this->error->StopScript(
					"XMLClass", 
					"Error in read module ({$row->class}): " . $module->GetBadParamsDescr()
				);
			}

			/**
			 * Нет доступа к модулю.
			 * В этом случае переопределяем массив xsl Определяем шаблон для модуля авторизации, 
			 * а для остальных ставим "пустой" шаблон. 
			 * А затем заменяем текущий результат запроса, который мы фетчим, на новый,
			 * с модулем авторизации (переменная $sql)
			 */
			if ($module->GetAccessDenied()) {
				$this->xslList[] = array(
					"filename" => $this->globalvars->GetStr("Template_AuthBase"), 
					"match" => "/root/SectionCurrent"
				);
				$this->xslList[] = array(
					"filename" => $this->globalvars->GetStr("Template_Blank"), 
					"match" => "module[@id != " . $this->globalvars->GetInt("AuthRef") . "]"
				);
				
				$sql = $this->GetAuthModuleSQLResult("\$AuthType = '" . $module->GetAccessDeniedReason() . "';");
				continue;
			}

			$module->WriteStoredInfo($moduleXML, $xml);
		}
		
		return $rootNode;
	}
	
	private function GetQueryParams() {
		$rootNode = $this->xml->createElement("QueryParams");
		$rootNode->setAttribute("referer", $this->query->GetReferer());
		$rootNode->setAttribute("userAgent", $this->query->GetUserAgent());
		$rootNode->setAttribute("query", $this->query->GetQuery());
		$rootNode->setAttribute("host", $this->query->GetHost());
		$rootNode->setAttribute("prefix", $this->conf->Param("Prefix"));
		$rootNode->setAttribute("url", $this->query->GetURL());
		$rootNode->setAttribute("SID", $this->auth->GetSID());
		
		$rootNode->setAttribute("corePath", CMSPATH_CORE);
		$rootNode->setAttribute("upload", $this->conf->Param("Prefix") . CMSPATH_UPLOAD);
		$rootNode->setAttribute("css", $this->conf->Param("Prefix") . CMSPATH_CSS);
		$rootNode->setAttribute("jscore", $this->conf->Param("Prefix") . CMSPATH_JSCORE);
		
		$queryEscaped = $this->conf->Param("StaticURL") 
			? rawurlencode(rawurlencode($this->query->GetQuery())) 
			: rawurlencode($this->query->GetQuery());
			
		if (isset($_GET["retpath"])) {
			$retpathEscaped = $this->conf->Param("StaticURL") 
				? rawurlencode(rawurlencode($_GET["retpath"])) 
				: rawurlencode($_GET["retpath"]);
		} else {
			$retpathEscaped =  $queryEscaped;
		}
				
		$rootNode->setAttribute("retpath", $retpathEscaped);
		$rootNode->setAttribute(
			"retpathPost", (isset($_GET["retpath"])) ? $_GET["retpath"] : $this->query->GetQuery()
		);
		$rootNode->setAttribute("queryEscaped", $queryEscaped);
		$rootNode->setAttribute(
			"prefixEscaped", 
			$this->conf->Param("StaticURL") 
				? rawurlencode(rawurlencode($this->conf->Param("Prefix"))) 
				: rawurlencode($this->conf->Param("Prefix"))
		);
		$rootNode->setAttribute(
			"urlEscaped", 
			$this->conf->Param("StaticURL") 
				? rawurlencode(rawurlencode($this->query->GetURL())) 
				: rawurlencode($this->query->GetURL())
		);
		$rootNode->setAttribute("staticURL", $this->conf->Param("StaticURL") ? "1" : "0");
		
		foreach ($_GET as $idx => $val) {
			// Защита от нелатинских символов, передаваемых через адресную строку
			if (!preg_match("~[^-_a-zA-Z0-9]~", $idx)) {
				$paramNode = X_CreateNode($this->xml, $rootNode, "param", $val);
				$paramNode->setAttribute("name", $idx);
				$paramNode->setAttribute(
					"escaped", 
					$this->conf->Param("StaticURL") 
						? rawurlencode(rawurlencode($val)) 
						: rawurlencode($val)
				);
			}
		}

		$postNode = X_CreateNode($this->xml, $rootNode, "post");
		foreach ($_POST as $idx => $val) {
			// Защита от нелатинских символов, передаваемых через адресную строку
			if (!preg_match("/[^-_a-zA-Z0-9]/", $idx)) {
				$paramNode = X_CreateNode($this->xml, $postNode, "param", $val);
				$paramNode->setAttribute("name", $idx);
			}
		}
		
		return $rootNode;
	}
}

?>