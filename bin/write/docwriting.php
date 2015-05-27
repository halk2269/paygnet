<?php

require_once(CMSPATH_LIB . "docwriting/documentvalidator.php");
require_once(CMSPATH_LIB . "logging/useractionlogging.php");

/**
 * Запись документов с определённым типом
 * @todo must refactored
 * 
 * Рассылка - тоже отдельной функцией. 
 * Проверка, нужна ли рассылка - тоже.
 */
class DocWritingWriteClass extends WriteModuleBaseClass {
	
	const NOTIFY_MAIL_PARAM_NAME = 'address';

	public function MakeChanges() {
		$ret = $this->_CheckSecurity($act, $qref, $id, $dtName, $canCreateEnabled, $params);
		if (!$ret) {
			return false;
		}
		
		// Проверка - не запись ли это элемента массива. Если запись - проверка всех входных параметров
		return $this->_WriteAll($act, $qref, $id, $dtName, $canCreateEnabled, $params, $lastID);

	}

	/**
	 * @param $act
	 * @param int $qref - id модуля, которому принадлежит документ
	 * @param int $id
	 * @param string $dtName
	 * @param boolean $canCreateEnabled
	 * @param string $params
	 * @param $lastID
	 * @return boolean
	 */
	protected function _WriteAll($act, $qref, $id, $dtName, $canCreateEnabled, $params, &$lastID) {
		$tblName = DocCommonClass::GetTableName($dtName);
		$isArrayElement = false;
		
		if ($id > 0 and $this->_GetParam("subname")) {
			$isArrayElement = true;
			// Название поля-массива, элемент которого будет редактироваться/добавляться
			$subName = $this->_GetParam("subname");
			// id документа - элемента массива
			$subID = (int)$this->_GetParam("subid");
			
			// $subID - нормальное число
			if ($subName === false or $subID === false or !IsGoodNum($subID)) {
				return false;
			}
			// Запрошенное поле существует?
			if (!isset($this->dtconf->dtf[$dtName][$subName])) {
				return false;
			}
			// ...и его тип - действительно массив...
			if (!isset($this->dtconf->dtf[$dtName][$subName]["type"])) {
				return false;
			}
			if ($this->dtconf->dtf[$dtName][$subName]["type"] != "array") {
				return false;
			}
			// ...и тип элементов массива определён
			if (!isset($this->dtconf->dtf[$dtName][$subName]["subt"])) {
				return false;
			}
			// Тип элементов массива
			$subDTName = $this->dtconf->dtf[$dtName][$subName]["subt"];
			// Дополнительные данные для поддокумента
			$subParams = array(
				"parent_id" => $id, 
				"field_name" => $subName, 
				"dt_name" => $dtName
			);
			
			// Имя таблицы поддокумента
			$subTblName = DocCommonClass::GetTableName($subDTName);
			// Действие под-элемента
			$subAct = ($this->_GetParam("delete") == 1) ? "Delete" : (($subID == 0) ? "Create" : "Edit");
			// Если редактирование или удаление (subid указан) - проверяем, что документ существует и относится к нашему основному документу
			if ($subID > 0) {
				// относится к нам
				$checkValue = $this->db->GetValue("SELECT {$subName} FROM {$tblName} WHERE id = {$id}");
				if (!$checkValue) {
					return false;
				}
				
				// существует
				$stmt = $this->db->SQL("SELECT ref FROM {$subTblName} WHERE id = {$subID}");
				if ($stmt->rowCount() < 1) {
					return false;
				}
			}
			// Собственно, запись
			$lastID = 0;

			if (!$this->OnBeforeSubWrite($qref, $dtName, $id, $subAct, $subDTName, $subID)) {
				return false;
			}
			$rv = $this->_GoWriting($subAct, $qref, $subID, $subDTName, true, $lastID, $subParams);
			$this->OnAfterSubWrite($qref, $dtName, $id, $subAct, $subDTName, $subID, $rv, $lastID);
			if ($rv) {
				$this->OnSuccessfulSubWrite($qref, $dtName, $id, $subAct, $subDTName, $subID, $rv, $lastID);
			}
			// Если это новый документ (элемент массива) - добавляем информацию о нём в основной документ
			if ($subAct == "Create" || $subID > 0) {
				$this->db->SQL("
					UPDATE 
						{$tblName} 
					SET 
						{$subName} = IF(
							{$subName} = 0, 1, (SELECT COUNT(*) FROM {$subTblName} WHERE parent_id = {$id})
						), 
						chtime = NOW() 
					WHERE 
						id = {$id}
				");
			}
			
			// Если удаление, удаляем ссылку на документ из основного документа
			if ($subAct == "Delete") {
				$this->db->SQL("
					UPDATE {$tblName} SET {$subName} = (SELECT COUNT(*) FROM {$subTblName} WHERE parent_id = {$id}) WHERE id = {$id}
				");
			}
		} else {
			$lastID = 0;
			if (!$this->OnBeforeWrite($qref, $act, $dtName, $id, $canCreateEnabled)) {
				return false;
			}
			if ($act == "Create" and $canCreateEnabled and $this->_GetParam("enabled") !== false) {
				$act = "CreateEnabled";
			}
			$rv = $this->_GoWriting($act, $qref, $id, $dtName, $canCreateEnabled, $lastID);
			$this->OnAfterWrite($qref, $act, $dtName, $id, $canCreateEnabled, $rv, $lastID);
			if ($rv) {
				$this->OnSuccessfulWrite($qref, $act, $dtName, $id, $canCreateEnabled, $rv, $lastID);
			}
			if ($rv && $lastID > 0) {
				$this->_SendNotify($qref, $act, $dtName, $lastID);
			}
		}
		return $rv;
	}

	/* Триггер, срабатывающий при записи элемента массива. Запись производится, если на выходе - true */
	protected function OnBeforeSubWrite($qref, $dtName, $id, $subAct, $subDTName, $subID) {
		return true;
	}

	/* Триггер, срабатывающий после записи элемента массива. Возвращаемое значение не проверяется */
	protected function OnAfterSubWrite($qref, $dtName, $id, $subAct, $subDTName, $subID, $rv, $lastID) {
		return;
	}

	/* Триггер, срабатывающий при удачной записи элемента массива. Возвращаемое значение не проверяется */
	protected function OnSuccessfulSubWrite($qref, $dtName, $id, $subAct, $subDTName, $subID, $rv, $lastID) {
		return;
	}

	/* Триггер, срабатывающий при записи документа. Запись производится, если на выходе - true */
	protected function OnBeforeWrite($qref, $act, $dtName, $id, $canCreateEnabled) {
		return true;
	}

	/* Триггер, срабатывающий после записи документа. Возвращаемое значение не проверяется */
	protected function OnAfterWrite($qref, $act, $dtName, $id, $canCreateEnabled, $rv, $lastID) {
		return;
	}

	/* Триггер, срабатывающий при удачной записи документа. Возвращаемое значение не проверяется */
	protected function OnSuccessfulWrite($qref, $act, $dtName, $id, $canCreateEnabled, $rv, $lastID) {
		return;
	}

	protected function _SendNotify($ref, $act, $dtName, $id, $userEmail = false, $attachList = array()) {
		$tblName = DocCommonClass::GetTableName($dtName);
		$stmt = $this->db->SQL("
			SELECT 
				xslt, mailto, mailfrom, subject, send_to_initiator, initiator_template 
			FROM 
				sys_dt_notifies 
			WHERE 
				ref = {$ref} AND action = '{$act}'
		");
		
		if ($row = $stmt->fetchObject()) {
			$mailfrom = (preg_match("~@~", $row->mailfrom) > 0) 
				? $row->mailfrom 
				: $this->conf->Param($row->mailfrom);
				
			$dtStmt = $this->db->SQL("SELECT * FROM {$tblName} WHERE id = {$id}");
			if ($rowQuery = $dtStmt->fetchObject()) {
				$mailXML = new DOMDocument('1.0', 'UTF-8');
				// Корневая нода root
				$mailXMLRoot = $mailXML->createElement('root');
				$mailXML->appendChild($mailXMLRoot);

				// Информация о пользователе
				$userID = $this->auth->GetUserID();
				$SQLQueryResult = $this->dt->FormatSelectQuery(
					"user", $mailXML, $mailXMLRoot, "*", "", "", "dt.id = {$userID}"
				);
				$this->dt->ProcessQueryResults(
					$SQLQueryResult, $mailXML, $mailXMLRoot, "user", false, false, 0, "", false, null, "user"
				);

				// Информация о сервере
				$QueryNode = $mailXML->createElement("QueryParams");
				$QueryNode->setAttribute("host", $this->host);
				$QueryNode->setAttribute("prefix", $this->prefix);
				$mailXMLRoot->appendChild($QueryNode);

				// Информация в XML, поставляемая модулем
				$this->_SendNotifyXMLModify($mailXML, $mailXMLRoot);

				// Выводим типы полей документа в XML
				$this->dt->GetFieldList($mailXML, $mailXMLRoot, $dtName, false);

				// Выводим обработанный документ в XML
				$SQLQueryResult = $this->dt->FormatSelectQuery(
					$dtName, $mailXML, $mailXMLRoot, "*", "", "", "dt.id = {$id}"
				);
				
				$this->dt->ProcessQueryResults(
					$SQLQueryResult, $mailXML, $mailXMLRoot, $dtName, false, false, 0, "", false
				);
				
				// Attach list
				foreach ($this->dtconf->dtf[$dtName] as $idx => $val) {
					if ($val["type"] == "file") {
						$fileStmt = $this->db->SQL("
							SELECT name, filename FROM sys_dt_files WHERE id = {$rowQuery->$idx}
						");
						
						if ($rowFile = $fileStmt->fetchObject()) {
							$attachList[$idx] = array(
								"path" => CMSPATH_UPLOAD . $rowFile->filename, 
								"name" => $rowFile->name
							);
						}
					}
				}
				
				// XSLT for admin
				if ($row->mailto) {
					$xsl = LoadFile(GenPath($row->xslt, CMSPATH_XSLT, CMSPATH_PXSLT));
					$html = XSL_Transformation($xsl, $mailXML);
					
					$this->mail->SendToAll(
						$this->_GetMailToAddress($row->mailto), $mailfrom, "", "", $row->subject, $html, $attachList
					);
				}

				// XSLT for initiator
				$to = ($userEmail) ? $userEmail : $this->auth->GetUserParam($row->send_to_initiator);
				if ($to) {
					$xsl = LoadFile(GenPath($row->initiator_template, CMSPATH_XSLT, CMSPATH_PXSLT));
					$html = XSL_Transformation($xsl, $mailXML);
					
					$this->mail->SendToAll($to, $mailfrom, "", "", $row->subject, $html, $attachList);
				}

			}
		}
	}
	
	// Дополнение XML
	protected function _SendNotifyXMLModify($xml, $parentNode) { }
	
	protected function _GoDelete($dtName, $id) {
		$tblName = DocCommonClass::GetTableName($dtName);
		$stmt = $this->db->SQL("SELECT * FROM {$tblName} WHERE id = {$id}");
		if (!$stmt->rowCount()) {
			return true;
		}
		
		$row = $stmt->fetchObject();
		
		foreach ($this->dtconf->dtf[$dtName] as $idx => $val) {
			$type = $val["type"];
			if ($type == "file") {
				$fStmt = $this->db->SQL("SELECT filename FROM sys_dt_files WHERE id = {$row->$idx}");
				if ($frow = $fStmt->fetchObject()) {
					$dest = CMSPATH_UPLOAD . $frow->filename;
					if (file_exists($dest)) {
						unlink($dest);
					}
				}
				
				$this->db->SQL("DELETE FROM sys_dt_files WHERE id = {$row->$idx}");
			} else if ($type == "image") {
				$fStmt = $this->db->SQL("SELECT filename FROM sys_dt_images WHERE id = {$row->$idx}");
				if ($frow = $fStmt->fetchObject()) {
					$dest = CMSPATH_UPLOAD . $frow->filename;
					if (file_exists($dest)) {
						unlink($dest);
					}
				}
				
				$this->db->SQL("DELETE FROM sys_dt_images WHERE id = {$row->$idx}");
				
				// удаляем превьюшку
				$preview = isset($this->dtconf->dtf[$dtName][$idx]["tmbh"]) 
					? $this->dtconf->dtf[$dtName][$idx]["tmbh"] 
					: false;
					
				if ($preview && !isset($this->dtconf->dtf[$dtName][$preview])) {
					$prevStmt = $this->db->SQL(
						"SELECT filename FROM sys_dt_images WHERE id = {$row->$preview}"
					);
					if ($frow = $prevStmt) {
						$dest = CMSPATH_UPLOAD . $frow->filename;
						if (file_exists($dest)) {
							unlink($dest);
						}
					}
					
					$this->db->SQL("DELETE FROM sys_dt_images WHERE id = {$row->$preview}");
				}
			} else if ($type == "select") {
				$this->db->SQL("DELETE FROM sys_dt_select WHERE id = {$row->$idx}");
			} else if ($type == "strlist")  {
				$this->db->SQL("DELETE FROM sys_dt_strlist WHERE id = {$row->$idx}");
			} else if ($type == "array") {
				$subTblName = DocCommonClass::GetTableName($val["subt"]);
				$this->db->SQL("
					DELETE FROM
						{$subTblName} sub
					USING 
						{$tblName} dt, {$subTblName} sub 
					WHERE 
						sub.parent_id = dt.id AND dt.id = {$id}
				");
			}
		}
		$this->db->SQL("DELETE FROM {$tblName} WHERE id = {$id}");

		if ($this->conf->Param("LogUserActions")) {
			$logger = new UserActionLogging();
			$logger->AddDocumentAction($dtName, $id, "Delete");
		}
	}

	/**
	 * Производит проверку пришедших данных для документа и производит их запись в БД
	 *
	 * 	Возможные варианты ошибок
	 * 'BlankField', descr = FieldName - поле, которое обязательно к заполнению, не заполнено.
	 * 'TooLong', descr = FieldName - превышена максимальная длина для поля.
	 * 'BadInt', descr = FieldName - неверное значение для целого типа.
	 * 'BadFloat', descr = FieldName - неверное значение для дробного типа.
	 * 'NumberTooBig', descr = FieldName - число слишком большое (больше заданного в dt.conf.php параметра maxv)
	 * 'NumberTooSmall', descr = FieldName - число слишком маленькое (меньше заданного в dt.conf.php параметра minv)
	 * 'BlankPassword', descr = FieldName - пустой пароль, что не есть хорошо.
	 * 'BadSelectID', descr = FieldName - неверный ID для вып. списка. Если пользователь не пытается "хакнуть" систему, этой ошибки появиться не должно.
	 * 'BadFileExt', descr = FieldName - непозволительное разрешение для файла.
	 * 'TooLargeFile', descr = FieldName - слишком большой файл.
	 * 'ThisIsNotImage', descr = FieldName - файл не является изображением.
	 * 'PasswordsAreNotIdentical', descr = FieldName - пароль и его подтверждение не совпадают.
	 * 'BadRegexp', descr = FieldName - введённая строка не соответствует заданному регулярному выражению.
	 * 'BadImageSizes', descr = FieldName - некорректные размеры файла изображения.
	 * 'ImageMagickFailure', descr = FieldName - ошибка сервера при работе ImageMagick
	 * 'MainImageIsNotLoaded', descr = FieldName – не было загружено главное изображение, из которого генерируется превью
	 * 'InvalidFile', descr = FieldName – при работе с документом в формате .xls с использованием модуля PEAR произошла ошибка
	 * 'MainImageIsNotExist', descr = FieldName – поле основного изображения отсутствует в описании ТД
	 * 'BadDate' - неправильный формат даты
	 * 'LinkToSelf' - ссылка документа на самого себя
	 * 
	 * @param string $act
	 * @param int $qref
	 * @param int $id
	 * @param string $dtName
	 * @param bool $canCreateEnabled
	 * @param int $lastID
	 * @param array $subParams
	 * @return bool
	 */
		
	protected function _GoWriting($act, $qref, $id, $dtName, $canCreateEnabled, &$lastID, $subParams = false) {
		$tblName = DocCommonClass::GetTableName($dtName);
		if ($act == "CreateEnabled") {
			$act = "Create";
		}
		if ("Delete" == $act) {
			$this->_GoDelete($dtName, $id);
			return true;
		}

		$documentValidator = new DocumentValidator($this->query, $dtName, $act, $canCreateEnabled, $id);

		foreach ($documentValidator->GetErrors() as $name => $value) {
			$this->_WriteError($value, $name);
		}

		// Отдаём модулю чтения дамп переменных и завершаем работу
		if ($this->IsErrorOccured()) {
			$this->_NeedDumpVars();
			return false;
		}
		
		// Успех!
		$fields = $documentValidator->GetFields();
		
		$this->db->Begin();
		
		$this->_ProcessFiles($documentValidator->GetFilesArray(), $act, $dtName, $id, $fields);
		$this->_ProcessImages($documentValidator->GetImagesArray(), $act, $dtName, $id, $fields);
		$this->_ProcessLists($documentValidator->GetSelectArray(), $act, $dtName, $id, $fields);
		$this->_ProcessLists($documentValidator->GetRadioArray(), $act, $dtName, $id, $fields);
		$this->_ProcessMultibox($documentValidator->GetMultiboxArray(), $act, $dtName, $id, $fields);
		$this->_ProcessStrList($documentValidator->GetStrListArray(), $act, $dtName, $id, $fields);
		
		// Собираем строку SET для запроса
		$bindParams = array();
		$bindValues = array();
		
		foreach ($fields as $idx => $val) {
			$bindParams[] = "`{$idx}` = ?";
			$bindValues[] = $val; 
		}
		if ($subParams) {
			foreach ($subParams as $idx => $val) {
				$bindParams[] = "`{$idx}` = ?";
				$bindValues[] = $val;
			}
		}
		
		$bindSetQueryPart = implode(", ", $bindParams);
				
		$date = date('Y-m-d, H:i:s');
		// Выполняем запрос создания новой строки или обновления существующей
		if ($act == "Edit") {
			$this->db->SQL(
				"UPDATE {$tblName} SET ref = {$qref}, chtime = NOW(), {$bindSetQueryPart} WHERE id = {$id}",
				$bindValues
			);
						
			$this->WriteActionLog(
				CMSPATH_LOG . "docwrite.log", 
				$date . " UPDATE {$tblName} SET ref = {$qref}, chtime = {$date}, {$bindSetQueryPart} WHERE id = {$id} :: " . implode(', ', $bindValues) 
			);
			$lastID = $id;
		} else {
			$writeRef = $this->GetWriteRef($qref);
			$idInsStr = ($id != 0) ? ", id = {$id}" : "";
			$this->db->SQL(
				"INSERT INTO {$tblName} SET ref = {$writeRef}, addtime = NOW(), chtime = NOW(), {$bindSetQueryPart} {$idInsStr}",
				$bindValues
			);
			
			$this->WriteActionLog(
				CMSPATH_LOG . "docwrite.log", 
				$date . " INSERT INTO {$tblName} SET ref = {$writeRef}, addtime = {$date}, chtime = {$date}, {$bindSetQueryPart} {$idInsStr} :: " . implode(', ', $bindValues)
			);
			$lastID = $this->db->GetLastID();
		}
	
		$this->_ModifyLinks($dtName, $lastID, $documentValidator);

		if (!$this->_ModifyRefs($dtName, (0 == $id) ? $lastID : $id, (0 == $id))) {
			$this->db->Rollback();
			return false;
		}
		
		$this->db->Commit();

		// удаляем временные файлы
		foreach ($documentValidator->GetFilesToDeleteArray() as $file) {
			unlink($file);
		}

		if ($this->_GetParam("retpath") === false) {
			$this->_WriteInfo("DocWasSaved");
			$opener = $this->_GetParam("opener");
			if ($opener !== false) {
				$this->_WriteInfo("opener", $opener);
			}
		}
		
		if ($this->conf->Param("LogUserActions")) {
			$logger = new UserActionLogging();
			$logger->AddDocumentAction($dtName, $lastID, $act);
		}
		
		return true;
	}
	
	protected function WriteActionLog($FileName, $str) {
		$f = fopen($FileName, "a+");
		fwrite($f, $str . "\n");
		fclose($f);
	}

	protected function GetWriteRef($qref) {
		eval($this->readParams);
		return isset($inWriteRef) ? $inWriteRef : $qref;
	}
	
	private function _GetMailToAddress($mailto) {
		$value = $this->query->GetParam(self::NOTIFY_MAIL_PARAM_NAME);
		$result = '';
		
		if ($value) {
			$mailFromList = $this->db->GetValue(
				"SELECT name FROM sys_dt_select_items WHERE id = ?", 
				array($value)
			);
			
			if ($mailFromList && preg_match('~@~', $mailFromList)) {
				$result = $mailFromList . ',';
			}
		}
		
		$result .= (preg_match("~@~", $mailto)
			? $mailto
			: $this->conf->Param($mailto)
		);
		
		return $result;
	}

	private function _CheckSecurity(&$act, &$qref, &$id, &$dtName, &$canCreateEnabled, &$params) {
		$qref = $this->_GetParam("qref");
		$id = (int)$this->_GetParam("id");
		
		if (1 == $this->_GetParam("delete")) {
			$act = "Delete";
			if ($id == 0) {
				$this->inputError = true;
				return false;
			}
		} else {
			$act = (0 == $id) ? "Create" : "Edit";
		}

		$dtName = "";

		require_once CMSPATH_LIB . "doc/doccommon.php";
		
		$docCommon = DocCommonClass::GetInstance();
		if (!$docCommon->CheckRights($act, get_class($this), $qref, $id)) {
			$this->inputError = true;
			return false;
		}

		$params = $docCommon->GetParams();
		$dtName = $docCommon->GetDtName();
		$canCreateEnabled = $docCommon->CanCreateEnabled();

		return true;
	}

	/**
	 * @param string $dtName
	 * @param int $docID
	 * @param DocumentValidator $documentValidator
	 * @return bool
	 */
	private function _ModifyLinks($dtName, $docID, $documentValidator) {
		foreach ($documentValidator->GetLinksToDelete() as $link) {
			$linksTable = "link_" . $dtName . "_" . $link["doct"];
			$deleteStr = implode(",", $link["links"]);
			$this->db->SQL("DELETE FROM {$linksTable} WHERE from_id = {$docID} AND to_id IN ({$deleteStr})");

			if ($link["both"]) {
				$backLinksTable = "link_" . $link["doct"] . "_" . $dtName;
				$this->db->SQL("DELETE FROM {$backLinksTable} WHERE from_id IN ({$deleteStr}) AND to_id = {$docID}");
			}
		}

		foreach ($documentValidator->GetLinksToInsert() as $link) {
			$linksTable = "link_" . $dtName . "_" . $link["doct"];

			$insertPair = array();
			foreach ($link["links"] as $value) {
				$insertPair[] = "({$docID}, {$value})";
			}
			$insertQuery = "INSERT INTO {$linksTable} (from_id, to_id) VALUES";
			$this->db->SQL($insertQuery . implode(",", $insertPair));

			if ($link["both"]) {
				$backLinksTable = "link_" . $link["doct"] . "_" . $dtName;
				$insertQuery = "INSERT INTO {$backLinksTable} (to_id, from_id) VALUES";
				$this->db->SQL($insertQuery . implode(",", $insertPair));
			}

		}

		return true;
	}

	/**
	 * Обновление таблицы со связями документов с рефами
	 *
	 * @param string $dtName
	 * @param int $docID
	 * @param bool $isNew
	 * @return bool
	 */
	function _ModifyRefs($dtName, $docID, $isNew) {
		// Если не включен режим MultiRef
		if (!isset($this->dtconf->dtm[$dtName]) || !$this->dtconf->dtm[$dtName]) {
			return true;
		}

		// нет изменений - отлично :)
		if (!$this->_GetParam("ref_ismodified") && !$isNew) {
			return true;
		}
		
		$refsInArray = explode(";", $this->_GetParam("ref_selectedids"));
		
		// не выбран ни один раздел
		if (!$refsInArray[0]) {
			$this->_WriteError("noRefSelected");
			return false;
		}

		$refsInString = implode(",", $refsInArray);

		$stmt = $this->db->SQL("
			SELECT 
				r.id 
			FROM 
				sys_references r
			WHERE 
				r.params REGEXP 'inDTName[[:space:]]*=[[:space:]]*(\"|\'){$dtName}(\"|\')'
				AND r.params REGEXP 'inSelectRef[[:space:]]*=[[:space:]]*(\"|\')(own|owndeep)(\"|\')'
				AND r.enabled = 1
				AND r.id IN ({$refsInString})
		");

		if (count($refsInArray) != $stmt->rowCount()) {
			$this->_WriteError("noProperRef");
			return false;
		}

		$multiRefTable = "dt_" . $dtName . "_ref";

		$stmt = $this->db->SQL("SELECT cat_id FROM {$multiRefTable} WHERE doc_id = {$docID}");
		$origIDs = array();
		while ($row = $stmt->fetchObject()) {
			$origIDs[] = $row->cat_id;
		}

		require_once(CMSPATH_LIB . "auxil/findvectorsdiff.php");
		$vectorDiff = new FindVectorsDiffClass($origIDs, $refsInArray);

		if ($vectorDiff->GetItemsForDelete()) {
			$this->db->SQL("DELETE FROM {$multiRefTable} WHERE doc_id = {$docID} AND cat_id IN (" . implode(",", $vectorDiff->GetItemsForDelete()) . ")");
		}

		if ($vectorDiff->GetItemsForInsert()) {
			$insertValues = array();

			foreach ($vectorDiff->GetItemsForInsert() as $insID) {
				$insertValues[] = "(" . $docID . ", " . $insID . ")";
			}

			$this->db->SQL("INSERT INTO {$multiRefTable} (doc_id, cat_id) VALUES " . implode(",", $insertValues));
		}

		return true;
	}

	private function _ProcessFiles($dtFileArray, $act, $dtName, $id, &$fields) {
		$tblName = DocCommonClass::GetTableName($dtName);
		
		// Разбираемся с файлами
		foreach ($dtFileArray as $idx => $val) {
			if ($val == "Insert") {
				// Смотрим, каково старое имя файла (fOldName)
				// Если На выходе пустя строка - файла нет, создаём новый
				
				if ($act == "Create") {
					// Создание нового документа? Старого имени не было точно
					$fOldName = "";
					$fOldExt = "";
				} else if ($act == "Edit") {
					$stmt = $this->db->SQL("
						SELECT 
							dt.{$idx} AS id, 
							f.filename AS filename, 
							f.ext AS ext 
						FROM 
							{$tblName} dt 
						LEFT JOIN 
							sys_dt_files f ON f.id = dt.{$idx} 
						WHERE 
							dt.id = {$id}
					");
							
					if (!$stmt->rowCount()) {
						$this->error->StopScript(
							"DocWritingWriteClass", 
							"Can't find file {$idx} in document with #dt {$dtName}, #id {$id}"
						);
						
						continue;
					}
					
					$row = $stmt->fetchObject();
					
					// Старое имя
					$fOldName = $row->filename;
					$fOldExt = $row->ext;
					// id файла в таблице sys_dt_files
					$fTableID = $row->id;
				} else {
					// На всякий случай. Сюда мы попасть не должны
					$fOldName = "";
					$fOldExt = "";
				}
				
				$f = $_FILES[$idx];
				// Имя файла
				$fName = $f["name"];
				// Расширение
				$fExt = (preg_match("~\.([0-9a-zA-Z$#()_]{1,10})$~", $fName, $matches) > 0) ? $matches[1] : "";
				// Размер
				$fSize = $f["size"];
				// Временное имя
				$fTmpName = $f["tmp_name"];
				
				// Новое имя. Если файл уже существовал, перезаписываем его (новое имя = старое имя).
				$fNewName = ($fOldName == "" || $fOldExt != $fExt) 
					? md5($fName . time() . $fSize . $this->auth->GetSID()) 
					: $fOldName;
					
				if (($fOldName == "" || $fOldExt != $fExt) && $fExt) {
					$fNewName .= ("." . $fExt);
				}
				
				// MIME-тип файла
				$fMimeType = $this->mime->GetMimeByExt($fExt);
				// Полное имя файла (с путём)
				$fDestination = CMSPATH_UPLOAD . $fNewName;
				// Если файл существует, удаляем его
				if ($fOldName && file_exists($fDestination) && !unlink($fDestination)) {
					$this->error->StopScript("DocWritingWriteClass", "Can't delete file '{$fDestination}'");
				}
				
				// Переносим файл в нужное место
				if (!copy($fTmpName, $fDestination)) {
					$this->error->StopScript("DocWritingWriteClass", "Can't copy file '{$fTmpName}' to '{$fDestination}' after uploading");
				}
				
				// Заносим информацию о файле в базу
				if ($fOldName) {
					// Файл уже существовал - заменяем информацию
					$this->db->SQL(
						"UPDATE 
							sys_dt_files 
						SET 
							name = ?, ext = ?, size = ?, mimetype = ?, filename = ? 
						WHERE 
							id = ?",
						array($fName, $fExt, $fSize, $fMimeType, $fNewName, $fTableID)
					);
					
					$fields[$idx] = $fTableID;
				} else {
					// Файл новый - создаём новую строку в таблице
					$this->db->SQL(
						"INSERT INTO 
							sys_dt_files 
						SET 
							name = ?, ext = ?, size = ?, mimetype = ?, filename = ?",
						array($fName, $fExt, $fSize, $fMimeType, $fNewName)
					);
					
					$fields[$idx] = $this->db->GetLastID();
					if ($fields[$idx] < 1) {
						$this->error->StopScript("DocWritingWriteClass", "Can't create new file because of DB error");
					}
				}

			} else if ($val == "Delete") {
				// Смотрим, каково старое имя файла (fOldName)
				$stmt = $this->db->SQL("
					SELECT 
						dt.{$idx} AS id, 
						f.filename AS filename 
					FROM 
						{$tblName} dt 
					LEFT JOIN 
						sys_dt_files f ON f.id = dt.{$idx} 
					WHERE 
						dt.id = {$id}
				");
						
				if (!$stmt->rowCount()) {
					$this->error->StopScript("DocWritingWriteClass", "Can't find file {$idx} in document with #dt {$dtName}, #id {$id}");
					continue;
				}
				
				$row = $stmt->fetchObject();
				
				// Старое имя
				$fOldName = $row->filename;
				// id файла в таблице sys_dt_files
				$fTableID = $row->id;
				// Полное имя файла (с путём)
				$fDestination = CMSPATH_UPLOAD . $fOldName;
				
				// Удаляем файл
				if (file_exists($fDestination) && $fOldName != "" && !unlink($fDestination)) {
					$this->error->StopScript("DocWritingWriteClass", "Can't delete file '{$fDestination}'");
				}
				
				// Обновляем информацию в базе
				$this->db->SQL("DELETE FROM sys_dt_files WHERE id = {$fTableID}");
				$fields[$idx] = 0;
			}
		}
	}

	private function _ProcessImages($dtImageArray, $act, $dtName, $id, &$fields) {
		$tblName = DocCommonClass::GetTableName($dtName);
		// Разбираемся с изображениями
		foreach ($dtImageArray as $idx => $val) {
			if ($val == "Insert") {
				// Смотрим, каково старое имя файла (fOldName)
				// Если На выходе пустя строка - файла нет, создаём новый
				if ($act == "Create") {
					// Создание нового документа? Старого имени не было точно
					$fOldName = "";
					$fOldExt = "";
				} elseif ($act == "Edit") {
					$stmt = $this->db->SQL("SELECT dt.{$idx} AS id, f.filename AS filename, f.ext AS ext FROM {$tblName} dt LEFT JOIN sys_dt_images f ON f.id = dt.{$idx} WHERE dt.id = {$id}");
					if ($stmt->rowCount() < 1) {
						$this->error->StopScript("DocWritingWriteClass", "Can't find image {$idx} in document with dt = {$dtName}, id = {$id}");
						continue;
					}
					
					$row = $stmt->fetchObject();
					
					// Старое имя
					$fOldName = $row->filename;
					$fOldExt = $row->ext;
					// id файла в таблице sys_dt_files
					$fTableID = $row->id;
				} else {
					// На всякий случай. Сюда мы попасть не должны
					$fOldName = '';
					$fOldExt = '';
				}
				$f = $_FILES[$idx];
				// Имя файла
				$fName = $f["name"];
				// Расширение
				$fExt = (preg_match("/\.([0-9a-zA-Z$#()_]{1,10})$/", $fName, $matches) > 0) ? $matches[1] : "";
				// Размер
				$fSize = $f["size"];
				// Временное имя
				$fTmpName = $f["tmp_name"];
				// Новое имя. Если файл уже существовал, перезаписываем его (новое имя = старое имя).
				$fNewName = ($fOldName == "" || $fOldExt != $fExt) 
					? md5($fName . time() . $fSize . $this->auth->GetSID()) 
					: $fOldName;
				if (($fOldName == "" || $fOldExt != $fExt) && $fExt) {
					$fNewName .= ("." . $fExt);
				}
				// MIME-тип файла
				$fMimeType = $this->mime->GetMimeByExt($fExt);
				// Полное имя файла (с путём)
				$fDestination = CMSPATH_UPLOAD . $fNewName;
				// Если файл существует, удаляем его
				if ($fOldName != "") {
					if (file_exists($fDestination) and !unlink($fDestination)) {
						$this->error->StopScript("DocWritingWriteClass", "Can't delete image '{$fDestination}'");
					}
				}
				// Переносим файл в нужное место
				if (!copy($fTmpName, $fDestination)) {
					$this->error->StopScript("DocWritingWriteClass", "Can't copy image '{$fTmpName}' to '{$fDestination}' after uploading");
				}

				// Проверяем размер изображений
				GetImageSizes($fDestination, $w, $h);
				// Заносим информацию о файле в базу
				if ($fOldName != "") {
					// Файл уже существовал - заменяем информацию
					$this->db->SQL("UPDATE sys_dt_images SET name = '{$fName}', ext = '{$fExt}', size = '{$fSize}', mimetype = '{$fMimeType}',  filename = '{$fNewName}', width = '{$w}', height = '{$h}' WHERE id = {$fTableID}");
					$fields[$idx] = $fTableID;
				} else {
					// Файл новый - создаём новую строку в таблице
						$this->db->SQL("
							INSERT INTO 
								sys_dt_images 
							SET 
								name = ?, ext = ?, size = ?, mimetype = ?, filename = ?, width = ?, height = ?
						",
						array($fName, $fExt, $fSize, $fMimeType, $fNewName, $w, $h)
					);
					
					$fields[$idx] = $this->db->GetLastID();
					if ($fields[$idx] < 1) {
						$this->error->StopScript("DocWritingWriteClass", "Can't create new image because of DB error");
					}
				}

			} elseif ($val == "Delete") {
				// Смотрим, каково старое имя файла (fOldName)
				$stmt = $this->db->SQL("SELECT dt.{$idx} AS id, f.filename AS filename FROM {$tblName} dt LEFT JOIN sys_dt_images f ON f.id = dt.{$idx} WHERE dt.id = {$id}");
				if ($stmt->rowCount() < 1) {
					$this->error->StopScript("DocWritingWriteClass", "Can't find image {$idx} in document with dt = {$dtName}, id = {$id}");
					continue;
				}
				
				$row = $stmt->fetchObject();
				// Старое имя
				$fOldName = $row->filename;
				// id файла в таблице sys_dt_files
				$fTableID = $row->id;
				// Полное имя файла (с путём)
				$fDestination = CMSPATH_UPLOAD . $fOldName;
				// Удаляем файл
				if (file_exists($fDestination) and $fOldName != "" and !unlink($fDestination)) {
					$this->error->StopScript("DocWritingWriteClass", "Can't delete image '{$fDestination}'");
				}

				// Обновляем информацию в базе
				$this->db->SQL("DELETE FROM sys_dt_images WHERE id = {$fTableID}");
				$fields[$idx] = 0;
			}
		}
	}

	private function _ProcessLists($lists, $act, $dtName, $id, &$fields) {
		$tblName = DocCommonClass::GetTableName($dtName);
		
		foreach ($lists as $idx => $val) {
			$needInsert = ($act == "Create");
			if (!$needInsert) {
				$listValue = $this->db->GetValue("SELECT {$idx} FROM {$tblName} WHERE id = {$id}");
				$listValue = ($listValue > 0) ? $listValue : 0;
				if (0 == $listValue) {
					$needInsert = true;
				}
			}
			
			$listTitle = $this->db->GetValue("SELECT title FROM sys_dt_select_lists WHERE id = {$val->list_id}");
			
			if ($needInsert) {
				$this->db->SQL("
					INSERT INTO 
						sys_dt_select 
					SET 
						list_id = {$val->list_id}, list_title = '{$listTitle}', 
						item_id = {$val->item_id}, item_name = '{$val->item_name}', 
						item_title = '{$val->item_title}'
				");
				$lastID = $this->db->GetLastID();
				if ($lastID < 1) {
					$this->db->Rollback();
					$this->error->StopScript("DocWritingWriteClass", "Can't add item into sys_dt_select");
				}
				
				$fields[$idx] = $lastID;
			} else {
				$this->db->SQL("
					UPDATE 
						sys_dt_select 
					SET 
						list_id = {$val->list_id}, list_title = '{$listTitle}', 
						item_id = {$val->item_id}, item_name = '{$val->item_name}', 
						item_title = '{$val->item_title}' 
					WHERE 
						id = {$listValue}
				");
			}
		}
	}
	
	private function _ProcessMultibox($multiboxArray, $act, $dtName, $id, &$fields) {
		$tblName = DocCommonClass::GetTableName($dtName);
		
		foreach ($multiboxArray as $idx => $val) {
			$needInsert = ($act == "Create");
			if (!$needInsert) {
				$fieldValue = $this->db->GetValue("SELECT {$idx} FROM {$tblName} WHERE id = {$id}");
				if (!$fieldValue) {
					$needInsert = true;	
				}
			}
			
			if ($needInsert) {
				$maxId = $this->db->GetValue("SELECT MAX(id) FROM sys_multibox_select");
				$multiboxValue = ($maxId) ? ++$maxId : 1;
				
				if (!$this->_InsertMultiboxValues($multiboxValue, $val)) {
					$this->db->Rollback();
					$this->error->StopScript("DocWritingWriteClass", "Can't add item into sys_multibox_select");
				}
				
				$fields[$idx] = $multiboxValue;
			} else {
				$stmt = $this->db->SQL("DELETE FROM sys_multibox_select WHERE id = {$fieldValue}");
				if ($stmt->rowCount() < 1) {
					$this->db->Rollback();
					$this->error->StopScript("DocWritingWriteClass", "Can't update item in sys_multibox_select");
				}
				
				if (!$this->_InsertMultiboxValues($fieldValue, $val)) {
					$this->db->Rollback();
					$this->error->StopScript("DocWritingWriteClass", "Can't update item in sys_multibox_select");
				}
			}
		}
	}
	
	private function _InsertMultiboxValues($currentId, $val) {
		$arInserted = array();
		foreach ($val as $itemId => $value) {
			$arInserted[] = "({$currentId}, {$itemId})"; 
		}
		$insertedValues = implode(', ', $arInserted);
		
		$stmt = $this->db->SQL("
			INSERT INTO 
				sys_multibox_select (id, item_id)
			VALUES " . $insertedValues
		);
		
		return ($stmt->rowCount() > 0);
	}

	private function _ProcessStrList($dtStrListArray, $act, $dtName, $id, &$fields) {
		$tblName = DocCommonClass::GetTableName($dtName);
		
		// Разбираемся со списками строк
		foreach ($dtStrListArray as $idx => $val) {
			$needInsert = ($act == "Create");
			if (!$needInsert) {
				$stmt = $this->db->SQL("SELECT {$idx} AS TableID FROM {$tblName} WHERE id = {$id}");
				$row = $stmt->fetchObject();
				
				$sTableID = ($row->TableID > 0) ? $row->TableID : 0;
				if (!$sTableID) {
					$needInsert = true;
				}
			}
			
			if ($needInsert) {
				$this->db->SQL("INSERT INTO sys_dt_strlist SET text = '{$val}'");
				$lastID = $this->db->GetLastID();
				if ($lastID < 1) {
					$this->error->StopScript("DocWritingWriteClass", "Can't add item into sys_dt_strlist");
				}
				
				$fields[$idx] = $lastID;
			} else {
				$this->db->SQL("UPDATE sys_dt_strlist SET text = '{$val}' WHERE id = {$sTableID}");
			}
		}
	}

}

?>