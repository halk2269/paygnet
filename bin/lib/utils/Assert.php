<?php
class Assert {

	static public function isTrue($bool, $message = "") {
		if (!$bool) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isFalse($bool, $message = "") {
		if ($bool) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isEqual($var1, $var2, $message = "") {
		if ($var1 != $var2) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function notEqual($var1, $var2, $message = "") {
		if ($var1 == $var2) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isIdentical($var1, $var2, $message = "") {
		if ($var1 !== $var2) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isNonNegativeInteger($int, $message = "") {
		self::isMatchRegExp('/^[0-9]*$/', $int, $message);
	}

	static public function isPositiveInteger($int, $message = "") {
		self::isMatchRegExp('/^[1-9]{1}[0-9]*$/', $int, $message);
	}

	static public function isInstanceOf($object, $class, $message = "") {
		if (!$object instanceof $class) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function typeOf($var, $type, $message = "") {
		if ($type != gettype($var)) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isMatchRegExp($regExp, $str, $message = "") {
		if (!preg_match($regExp, $str)) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isDefined($const, $message = "") {
		if (!defined($const)) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isArray($var, $message = "") {
		if (!is_array($var)) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function indexIsSet(array $array, $index, $message = "") {
		if (!array_key_exists($index, $array)) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isNonEmptyString($str, $message = "") {
		$str = (string)$str;
		if (!is_string($str) || !mb_strlen(trim($str))) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function inArray(array $array, $needle, $message = "") {
		if (!in_array($needle, $array)) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function notNull($value, $message = '') {
		if (is_null($value)) {
			throw new InvalidArgumentException($message);
		}
	}

	static public function isNull($value, $message = '') {
		if (!is_null($value)) {
			throw new InvalidArgumentException($message);
		}
	}
}
?>