<?php
interface ILogger {

	public function log($data);

	public function logError($data);

	public function logCustom($data, $node);

	public function logIncomingRequest($data, $serviceName = '');

	public function logOutgoingRequest($data, $serviceName = '');

	public function logServiceResponse($data, $serviceName = '');

	public function getProcessIdentifier();

}
?>