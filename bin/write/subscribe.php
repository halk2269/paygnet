<?php

require_once(CMSPATH_MOD_WRITE . "docwriting.php");

/**
 * Запись документов с определённым типом
 */

define('SUBSCRIBED_OK', 1);
define('WAIT_FOR_SUBSCRIBE', 2);
define('WAIT_FOR_DELETE', 3);

/**
 * Виды ошибок:
 * BadEmail - e-mail не прошел проверку
 * BadUid - неправильный UID
 */

class SubscribeWriteClass extends DocWritingWriteClass {

	public function MakeChanges() {
		$sendmail = false;
		
		$act = "";
		$email = "";
		$status = 0;
		$uid = "";
		$dtName = "";
		
		$qref = 0;
		$id = 0;
		$canCreateEnabled = false;
		$params = "";
		

		$ret = $this->_CheckSecurity($act, $qref, $id, $dtName, $canCreateEnabled, $params);
		if (!$ret) {
			return false;
		}

		// если не прописаны retpath и errpath, то жестко устанавливаем
		if (!$this->_GetParam("errpath")) {
			$this->errPath = "http://" . $_SERVER['HTTP_HOST'] . $this->conf->Prefix . "subscribe";
		}
		
		if (!$this->_GetParam("retpath")) {
			$this->retPath = "http://" . $_SERVER['HTTP_HOST'] . $this->conf->Prefix . "subscribe";
		}

		// получаем адрес отправителя
		$mailFrom = $this->conf->Param("SubscriptionMailFrom");

		// определяем тип действия
		$ret = $this->_GenAction($act, $email, $status, $uid, $dtName);
		if (!$ret) {
			return false;
		}
	
		switch ($act) {
			case "subscribe":
				$email = trim($email);
				if (!$this->mail->TestOneEmail($email)) {
					// email не прошел проверку регулярным выражением
					$this->_WriteError("BadEmail", $email);
				} else {
					// если e-mail успешно прошел проверку регулярным выражением
					$uid = "";
					
					// пробуем найтие в таблице запись с таким же e-mail`ом
					$row = $this->db->GetRow(
						"SELECT id, status, uid FROM dt_{$dtName} WHERE email = ?", 
						array($email)
					);
					if ($row) {
						// запись с таким e-mail уже есть в таблице
						// проверяем статус и по результатам высылаем или не высылаем повторное письмо с активацией
						if ($row->status == WAIT_FOR_SUBSCRIBE || $row->status == WAIT_FOR_DELETE) {
							$stmtUpdate = $this->db->SQL("
								UPDATE 
									dt_{$dtName} 
								SET 
									notifdate = NOW(), status = '" . WAIT_FOR_SUBSCRIBE . "'
							");
							
							if ($stmtUpdate->rowCount() < 1) {
								// если в базу ничего не добавилось
								$this->_WriteError("DBError", "Can't create a new user subscribe entry of DB error");
							} else {
								// если все обновилось успешно
								if ($row->status == WAIT_FOR_DELETE) {
									$this->_WriteInfo("UserAdded");	
								} else {
									$this->_WriteInfo("UserUpdated");	
								}
									
								$sendmail = true;
								$lastID = $row->id;
							}
						} else if ($row->status == SUBSCRIBED_OK) {
							$this->_WriteInfo("UserAllreadyConfirmed");
						}
					} else {
						// записи с таким email нет
						// тогда добавляем запись в таблицу и отсылаем уведомление пользователю
						$uid = md5($email . "subscribing" . time());
						$stmtInsert = $this->db->SQL(
							"INSERT INTO 
								dt_{$dtName} (ref, email, status, uid, notifdate, addtime) 
							VALUES 
								(?, ?, ?, ?, NOW(), NOW())",
							array($qref, $email, WAIT_FOR_SUBSCRIBE, $uid)
						);
						if (!$stmtInsert->rowCount()) {
							// если в базу ничего не добавилось - ошибка
							$this->_WriteError("DBError", "Can't create a new user subscribe entry of DB error");
						} else {
							$this->_WriteInfo("UserAdded");
							
							// если все обновилось успешно
							$lastID = $this->db->GetLastID();
							$sendmail = true;
						}
					}
	
					/* отсылка сообщения со ссылкой активации рассылки */
					if ($sendmail) {
						$fileName = $this->db->GetValue(
							"SELECT 
								xslt 
							FROM 
								sys_dt_notifies 
							WHERE 
								ref = ? AND action = ?",
							array($qref, 'Subscribe')
						);
						$fileName = ($fileName) ? $fileName : "notifysend.xslt";
												
						$this->_MailNotify(
							"subscribe", 
							$email, 
							$mailFrom, 
							GenPath($fileName, CMSPATH_XSLT, CMSPATH_PXSLT), 
							$qref, 
							$dtName, 
							$lastID
						);
					}
				}
				
				break;

			// подтверждение рассылки	
			case "сonfirmsubscribe":
				$row = $this->db->SQL("SELECT id, email, status, uid FROM dt_{$dtName} WHERE uid = '{$uid}'");
				if ($row) {
					// пользователь c таким uid существует
					// он подтвердил свое согласие получать рассылку - меняем его статус
					if ($row->status != SUBSCRIBED_OK) {
						$stmt = $this->db->SQL(
							"UPDATE 
								dt_{$dtName} 
							SET 
								uid = '', status = ? 
							WHERE 
								uid = ?",
							array(SUBSCRIBED_OK, $uid)
						);
						if ($stmt->rowCount() < 1) {
							// если в базе ничего не обновилось
							$this->_WriteError("DBError", "Can't update user subscribe entry of DB error");
						} else {
							$this->_WriteInfo("UserConfirmed");
						}
					} else {
						// если у пользователя уже активирована рассылка
						$this->_WriteInfo("UserAllreadyConfirmed");
					}
				} else {
					$this->_WriteInfo("UserNotExist");
				}
				
				break;

			case "unsubscribe":
				$email = trim($email);
				if (!$this->mail->TestOneEmail($email)) {
					// email не прошел проверку регулярным выражением
					$this->_WriteError("BadEmail", $email);
				} else {
					// если e-mail успешно прошел проверку регулярным выражением
					$uid = "";
					
					// пробуем найтие в таблице запись с таким же e-mail`ом
					$row = $this->db->GetRow("SELECT id, status, uid FROM dt_{$dtName} WHERE email = '{$email}'");
					if ($row) {
						// запись с таким e-mail уже есть в таблице
						// проверяем статус и по результатам высылаем или не высылаем повторное письмо с активацией
						$uid = ($row->status == WAIT_FOR_DELETE) 
							? $row->uid 
							: md5($email . "unsubscribed" . time());
						
						$stmtUpdate = $this->db->SQL(
							"UPDATE 
								dt_{$dtName} 
							SET 
								status = ?, uid = ?, notifdate = NOW() 
							WHERE 
								id = ?",
							array(WAIT_FOR_DELETE, $uid, $row->id)
						);
						
						if ($stmt->rowCount() < 1) {
							// если в базу ничего не добавилось
							$this->_WriteError("DBError", "Can't create a new user subscribe entry of DB error");
						} else {
							if ($row->status == WAIT_FOR_DELETE) {
								$this->_WriteInfo("UnsubscribeUpdated");
							} else if ($row->status == WAIT_FOR_SUBSCRIBE || $row->status == SUBSCRIBED_OK) {
								// отписываем пользователя
								$this->_WriteInfo("UnsubscribePrepeare");
							}
							
							// если все обновилось успешно
							$sendmail = true;
							$lastID = $row->id;
						}
					} else {
						$this->_WriteInfo("UserNotSubscribed");
					}
	
					/* отсылка сообщения со ссылкой активации рассылки */
					if ($sendmail) {
						$notifyXSLTFile = CMSPATH_XSLT . "notifysend.xslt";
						$this->_MailNotify("unsubscribe", $email, $mailFrom, $notifyXSLTFile, $qref, $dtName, $lastID);
					}
				}
				
				break;

			case "confirmunsubscribe":
				$row = $this->db->GetRow(
					"SELECT id, email, status FROM dt_{$dtName} WHERE uid = '{$uid}'"
				);
				if ($row) {
					// пользователь c таким uid существует
					// он подтвердил свое согласие отписаться от рассылки - удаляем его из таблицы
					if ($row->status == WAIT_FOR_DELETE || $row->status == WAIT_FOR_SUBSCRIBE) {
						$stmtDelete = $this->db->SQL(
							"DELETE FROM dt_{$dtName} WHERE id = '{$row->id}' LIMIT 1"
						);
						if ($stmt->rowCount() < 1) {
							// если в базе ничего не обновилось
							$this->_WriteError("DBError", "Can't update user subscribe entry of DB error");
						} else {
							$this->_WriteInfo("UnsubscribeConfirmed");
						}
					} else {
						$this->_WriteInfo("UserNotExist");
					}
	
				} else {
					$this->_WriteInfo("UserNotExist");
				}
				
				break;

			// случай, когда пользователь зарегистрировался, но не активировал рассылку и
			// теперь он хочет отписаться	
			case "quiteunsubscribe":
				$row = $this->db->GetRow(
					"SELECT id, status, uid FROM dt_{$dtName} WHERE email = '{$email}'"
				);
				if ($row) {
					// пользователь c таким email существует
					// он подтвердил свое согласие отписаться от рассылки - удаляем его из таблицы
					if ($row->status == WAIT_FOR_SUBSCRIBE) {
						$stmtDelete = $this->db->SQL("DELETE FROM dt_{$dtName} WHERE id = '{$row->id}' LIMIT 1");
						if ($stmt->rowCount() < 1) {
							// если в базе ничего не обновилось
							$this->_WriteError("DBError", "Can't update user subscribe entry of DB error");
						} else {
							$this->_WriteInfo("UserNotSubscribed");
						}
					} else {
						$this->_WriteInfo("UserNotExist");
					}
				} else {
					$this->_WriteInfo("UserNotExist");
				}
	
				break;
		}
		
		return true;
	}

	/**
	 *  проверяем входящие параметры и генерируем действие 
	 */
	private function _GenAction(&$act, &$email, &$status, &$uid, &$dtName) {
		$uid = $this->_GetParam("uid");
		
		if (!$uid) {
			$email = $this->_GetParam("email");
			if (!$email) {
				return false;
			}
						
			$act = $this->_GetParam("act");
			if (!$act === false) {
				return false;
			} else if ($act != "subscribe" && $act != "unsubscribe" && $act != "send") {
				return false;
			}
			
			if ($act == "unsubscribe") {
				$row = $this->db->GetRow("SELECT id, status, uid FROM dt_{$dtName} WHERE email = '{$email}'");
				if ($row && $row->status == WAIT_FOR_SUBSCRIBE) {
					$act = "quiteunsubscribe";
				}
			}
		} else {
			$row = $this->db->GetRow("SELECT id, email, status FROM dt_{$dtName} WHERE uid = '{$uid}'");
			if ($row) {
				// запись с таким uid присутствует в БД
				$email = $row->email;
				$status = $row->status;
				
				if ($status == WAIT_FOR_SUBSCRIBE){
					$act = "сonfirmsubscribe";
				} else if ($status == WAIT_FOR_DELETE) {
					$act = "confirmunsubscribe";
				} else {
					return false;
				}
			} else {
				// записи с таким uid нету в БД
				$this->_WriteError("BadUID");
				return false;
			}
		}
		
		return true;
	}

	private function _MailNotify($action, $email, $fromEMail, $notifyXSLTFile, $ref, $dtName, $lastID) {
		if (file_exists($notifyXSLTFile)) {
			// Создаём дерево XML
			$mailXML = new DOMDocument("1.0", "UTF-8");
			// Корневая нода root
			$mailXMLRoot = $mailXML->createElement("root");
			$mailXML->appendChild($mailXMLRoot);
			
			// Параметры запроса --> XML
			$queryNode = $mailXML->createElement("QueryParams");
			$queryNode->setAttribute("action", $action);
			$queryNode->setAttribute("host", $_SERVER['HTTP_HOST']);
			$queryNode->setAttribute("prefix", $this->conf->Prefix);
			$queryNode->setAttribute("retPath", rawurlencode($this->retPath));
			$queryNode->setAttribute("errPath", rawurlencode($this->errPath));
			$queryNode->setAttribute("writeModuleName", "Subscribe");
			$queryNode->setAttribute("qref", $ref);
			$queryNode->setAttribute("ref", $this->ref);
			$mailXMLRoot->appendChild($queryNode);
			
			$this->_MailNotifyModify($mailXML, $mailXMLRoot);
			
			// Выводим обработанный документ в XML
			$stmt = $this->db->SQL("SELECT * FROM dt_{$dtName} WHERE id = {$lastID}");
			$this->dt->Select2XML_V2($stmt, $mailXML, $mailXMLRoot, $dtName);
			
			// XSLT
			$xslDoc = new DOMDocument();
	    	$xslDoc->loadXml(LoadFile($notifyXSLTFile));
	    	
	    	$xslProc = new XSLTProcessor();
	    	$xslProc->importStylesheet($xslDoc);
	    	
	    	$html = $xslProc->transformToXml($mailXML);

			if ($action == "subscribe") {
				$mailSubject = "Подписка на рассылку";
			} else if ($action == "unsubscribe") {
				$mailSubject = "Отписка от рассылки";
			} else {
				$mailSubject = "Подписчикам рассылки";
			}
			
			$attachList = array();
			$this->mail->SendToAll($email, $fromEMail, "", "", $mailSubject, $html, $attachList);
		} else if (!file_exists($notifyXSLTFile)) {
			$this->log->writeWarning(
				"DocWritingWriteClass", 
				"Bad XSLT while letter sending trial (" . $notifyXSLTFile . " does not exist)"
			);
		}
	}
		
}

?>