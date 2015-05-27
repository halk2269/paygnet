<?php
class HttpRequest {

	const METHOD_POST = 'post';
	const METHOD_GET = 'get';

	private $vars = array();
	private $options = array();

	private $hostVerificationNeeded = false;
	private $selfVerificationNeeded = false;
	private $urlValidationNeeded = true;

	private $caCertificateFilePath;
	private $selfCertificateFilePath;
	private $privateKeyFilePath;

	private $authParams = '';
	private $xmlRequest = '';
	private $url;
	private $method;
	private $timeout = 360;

	private $loggingEnabled = true;

	/**
	 * @var ILogger
	 */
	private $logger;

	public function __construct(ILogger $logger) {
		$this->logger = $logger;
	}

	public function disableLogging() {
		$this->loggingEnabled = false;
	}

	public function setVars(array $vars) {
		$this->vars = $vars;
	}

	/**
	 * set curl options
	 *
	 * @param array $options
	 */
	public function setOptions(array $options) {
		$this->options = $options;
	}

	public function enableHostVerification() {
		Assert::isTrue(
			file_exists($this->caCertificateFilePath),
			'CA certificate file does not exist:' . $this->caCertificateFilePath
		);

		$this->hostVerificationNeeded = true;
	}

	public function enableSelfVerification() {
		Assert::isTrue(
			file_exists($this->selfCertificateFilePath),
			'Self certificate file does not exist: ' . $this->selfCertificateFilePath
		);

		Assert::isTrue(
			file_exists($this->privateKeyFilePath),
			'Private key file does not exist: ' . $this->selfCertificateFilePath
		);

		$this->selfVerificationNeeded = true;
	}
	
	public function disableUrlValidation() {
		$this->urlValidationNeeded = false;
	}

	public function setCACertificateFilePath($filePath) {
		$this->caCertificateFilePath = $filePath;
	}

	public function setSelfCertificateAndKeyFilePaths($certPath, $keyPath) {
		$this->selfCertificateFilePath = $certPath;
		$this->privateKeyFilePath = $keyPath;
	}

	public function setAuthParams($authParams) {
		Assert::isNonEmptyString($authParams, 'Incorrect params for authentication: ' . $authParams);
		$this->authParams = trim($authParams);
	}

	public function setXmlRequest($xmlRequest) {
		$this->xmlRequest = trim($xmlRequest);
	}

	public function setMethod($method) {
		$this->method = $method;
	}

	public function setUrl($url) {
		$this->url = $url;
	}

	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

	public function getFinalUrl($url, $method) {
		return $this->normalizeUrl($url, $method);
	}

	/**
	 * @return ResponseData
	 */
	public function execute($url = null, $method = null) {
		if ($method) {
			$this->method = $method;
		}

		if ($url) {
			$this->url = $url;
		}

		Assert::inArray(
			array(self::METHOD_GET, self::METHOD_POST), $this->method, "Method {$this->method} is not allowed."
		);
		
		if ($this->urlValidationNeeded) {
			Assert::isTrue(filter_var($this->url, FILTER_VALIDATE_URL), 'Incorrect url: ' . $this->url);	
		}
		

		$this->url = $this->normalizeUrl($this->url, $this->method);
		$logText = ($this->xmlRequest) ? $this->xmlRequest : $this->vars;

		if ($this->loggingEnabled) {
			$this->logger->logCustom(
				array(
					'request_url' => $this->url,
					'method' => $this->method,
					'data' => $logText,
					'timeout' => $this->timeout
				),
				get_class($this)
			);
		}

		list($responseStr, $error) = $this->executeRequest();

		if ($error && $this->loggingEnabled) {
			$this->logger->logError(
				array(
					'request_url' => $this->url,
					'method' => $this->method,
					'data' => $logText,
					'error' => $error
				)
			);

			return new ResponseData('', $error);
		}

		$response = new ResponseData($responseStr);

		if ($this->loggingEnabled) {
			$this->logger->logCustom(
				array(
					'response_from' => $this->url,
					'data' => $response->getBody()
				),
				get_class($this)
			);
		}

		return $response;
	}

	protected function executeRequest() {
		$ch = curl_init($this->url);
		curl_setopt_array($ch, $this->getRequestOptions($this->method));
		$responseStr = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		return array($responseStr, $error);
	}

	private function normalizeUrl($url, $method) {
		if ($method != self::METHOD_GET) {
			return $url;
		}

		return $url .= (mb_strpos($url, '?') ? '&' : '?') . $this->getQueryParamsString();
	}

	private function getQueryParamsString() {
		$params = array();
		foreach ($this->vars as $name => $value) {
			$params[] = $name .'='. urlencode($value);
		}
		return implode('&', $params);
	}

	private function getRequestOptions($method) {
		$options = array(CURLOPT_HEADER => 1, CURLOPT_FOLLOWLOCATION => 1, CURLOPT_RETURNTRANSFER => 1);

		foreach ( $options as $key => $value ) {
			if (isset($this->options[$key])) {
				unset($options[$key]);
			}
		}

		$options = $options + $this->options;

		$options[CURLOPT_TIMEOUT] = $this->timeout;

		if ($this->authParams) {
			$options[CURLOPT_USERPWD] = $this->authParams;
		}

		if ($this->hostVerificationNeeded) {
			$options[CURLOPT_SSL_VERIFYPEER] = 1;
			$options[CURLOPT_SSL_VERIFYHOST] = 1;
			$options[CURLOPT_CAINFO] = $this->caCertificateFilePath;
		} else {
			$options[CURLOPT_SSL_VERIFYPEER] = 0;
			$options[CURLOPT_SSL_VERIFYHOST] = 0;
		}

		if ($this->selfVerificationNeeded) {
			$options[CURLOPT_SSLCERT] = $this->selfCertificateFilePath;
			$options[CURLOPT_SSLKEY] = $this->privateKeyFilePath;
		}

		if ($method == self::METHOD_POST) {
			$options[CURLOPT_POST] = 1;
			if ($this->getQueryParamsString()) {
				$options[CURLOPT_POSTFIELDS] = $this->getQueryParamsString();
			}

			if ($this->xmlRequest) {
				$source = tmpfile();
				fwrite($source, $this->xmlRequest);
				fseek($source, 0);

				$options[CURLOPT_INFILE] = $source;
				$options[CURLOPT_INFILESIZE] = strlen($this->xmlRequest);
				$options[CURLOPT_HTTPHEADER] = array('Content-type: text/xml', 'Content-length: ' . strlen($this->xmlRequest));
			}
		}

		return $options;
	}
}
?>