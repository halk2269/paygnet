<?php
class ResponseData {

	private $headers;
	private $body;
	private $firstLine;

	private $error;

	public function __construct($responseData, $error = '') {
		$this->error = trim($error);

		if (mb_strlen($this->error) > 0) {
			return;
		}

		$responseData = str_replace("\r\n", "\n", $responseData);
		$headerEndPos = $this->getHeaderEndPos($responseData);

		$this->body = trim(mb_substr($responseData, $headerEndPos + 2));

		$this->headers = trim(mb_substr($responseData, 0, $headerEndPos));
		$this->firstLine = mb_substr($this->headers, 0, mb_strpos($this->headers, "\n"));

		if (!mb_strpos($this->headers, '200 OK')) {
			$this->error = '200 OK Response not recieved';
		}

	}

	/**
	 * @return string
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @return string
	 */
	public function getFirstLine() {
		return $this->firstLine;
	}

	/**
	 * @return bool
	 */
	public function hasError() {
		return mb_strlen($this->error) > 0;
	}

	/**
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}

	private function getHeaderEndPos($response) {
		$lastHttp = mb_strrpos($response, "\nHTTP/1.");
		return mb_strpos($response, "\n\n", $lastHttp);
	}
}
?>