<?php

/**
 * Осуществляет рассылки.
 * Срабатывает один раз в сутки.
 */

class SubscribeSendCronClass extends CronBaseClass {

	public function MakeChanges() {
		// устанавливаем переменные для отправки почты
		$mailFrom = $this->conf->Param("SubscriptionMailFrom");
		$this->report = "Starting sending subscribe letters...\n";
 		
 		// получаем все записи таблицы, содержащей информацию 
 		// о рассылках (id получателя, id рассылки, статус рассылки)
 
 		$stmt = $this->db->SQL("SELECT id, userid, subid FROM sys_dt_subscribe WHERE startok = 0 and mailok = 0");
 		while ($sendData = $stmt->fetchObject()) {
 			// получаем полную информацию о требуемой рассылке (тема рассылки, тело рассылки)
 			$stmtSub = $this->db->SQL(
				"SELECT id, subject, body FROM dt_subscribe WHERE id = ?", 
				array($sendData->subid)
			);
						
			if ($stmtSub->rowCount()) {
				$subscribeData = $stmtSub->fetchObject();
				
				$queryEmailRet = "SELECT email, status FROM dt_subscribeusers WHERE enabled = 1 and id = ?";
				// получаем информацию о получателе рассылки (смотрим)
				$stmtEmailRet = $this->db->SQL($queryEmailRet, array($sendData->userid));
				$subscribeUser = $stmtEmailRet->fetchObject();
				if ($subscribeUser) {
					if ($subscribeUser->status != 1) {
						// если статус пользователя отличен от "получает рассылку",
						// то переходим к следующей итерации
						$this->report .= "Ошибка отправки сообщения.\n";
						$this->report .= "Статус пользователя с id = {$subscribeUser->id} и e-mail = {$subscribeUser->email} имеет статус {$subscribeUser->status}\n";
						continue;
					}
				} else {
					// ошибка получения информации о пользователе
					$this->report .= "Error in SQL query: {$queryEmailRet}\n";
					continue;
				}

				/* устанавливаем переменные для отправки почты */				
				$mailBody = $subscribeData->body;
				$mailSubject = $subscribeData->subject;
				$mailTo = $subscribeUser->email;

				/* здесь должна быть обработка вложений */
				$mailAttachList = array();

				/* обновляем информацию о начале отсылки сообщения */
				$queryUpdate = "UPDATE sys_dt_subscribe SET startok = 1 WHERE id = ?";
				$stmtUpdate = $this->db->SQL($queryUpdate, array($sendData->id));
				
				if (0 == $stmtUpdate->rowCount()) {
					/* не получилось обновить информацию об отсылки сообщения */
					$this->report .= "Error updating was happened before sending information. SQL query is: {$queryUpdate}.\n";
					continue;
				}

				/* пробуем отослать сообщение пользователю */
				$ret = $this->mail->SendToOne($mailTo, $mailFrom, "", "", $mailSubject, $mailBody, $mailAttachList);
				if ($ret != 0) {
					/* функция mail() вернула ошибку */
					$this->report .= get_class($this) . " :: Error executing Mail() function ({$mailTo}).\n";
				} else {
					// обновляем информацию об успешной отсылке сообщения
					$queryUpdateLast = "UPDATE sys_dt_subscribe SET mailok = 1 WHERE id = ?";
					$stmtLast = $this->db->SQL($queryUpdateLast, array($sendData->id));
					if (0 == $stmtLast) {
						$this->report .= "Error updating was happened after sending information. SQL query: {$queryUpdateLast}.\n";
						continue;
					}
				}
			} else {
				// если информация о требуемой рассылке не была получена
				$this->report .= "Error executing SQL query: {$SQLSubscribeData}.\n";
				continue;
			}
 		}
	}
}

?>