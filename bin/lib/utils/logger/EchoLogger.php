<?php

require_once CMSPATH_LIB . 'utils/logger/ILogger.php';

class EchoLogger implements ILogger {

	public function log($data) {
		$this->doEcho($data);
	}

	public function logError($data) {
		$this->doEcho($data, 'Error: ');
	}

	public function logCustom($data, $node) {
		$this->doEcho($data, $node . ': ');
	}

	public function logIncomingRequest($data, $serviceName = '') {
		$this->doEcho($data, "Incoming request from {$serviceName}:");
	}

	public function logOutgoingRequest($data, $serviceName = '') {
		$this->doEcho($data, "Request to {$serviceName}:");
	}

	public function logServiceResponse($data, $serviceName = '') {
		$this->doEcho($data, "Response from {$serviceName}:");
	}

	public function getProcessIdentifier() {
		return 0;
	}

	private function doEcho($word, $prefix = '') {
		if (is_scalar($word)) {
			echo "{$prefix}{$word}\n";
		} else {
			echo $prefix . print_r($word, true) . "\n";
		}
	}

}
?>