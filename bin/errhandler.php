<?php

$errorHandlerIsOn = true;

function ErrorHandlerOn() {
	global $errorHandlerIsOn;
	$errorHandlerIsOn = true;
}

function ErrorHandlerOff() {
	global $errorHandlerIsOn;
	$errorHandlerIsOn = false;
}

function SwitchErrorHandler() {
	global $errorHandlerIsOn;
	$errorHandlerIsOn = (!$errorHandlerIsOn);
}

function errhandler($errno, $errstr, $errfile, $errline) {
	global $errorHandlerIsOn;
	if (!$errorHandlerIsOn) {
		return;
	}
	
	require_once(CMSPATH_BIN . "response.php");
	ResponseClass::SetHeaders("html");
	
	global $phpErrorOut;
	
	$phpErrorOut .= "<div>";
	$phpErrorOut .= "<h1>Error</h1>\n";
	$er = GetErrorDescription($errno);
	
	$phpErrorOut .= "<h2>{$er[0]} (<a href='#' onclick='return false' title='{$er[1]}'>?</a>)</h2>\n";
	$phpErrorOut .= "<div>{$errstr}</div>\n";

	if (isset($_GET["fullbacktrace"])) {
		$phpErrorOut .= "File: {$errfile} (line {$errline})<br />\n";
		$phpErrorOut .= "<h2>Full backtrace</h2>\n";
		$phpErrorOut .= "<div><pre>";
		$phpErrorOut .= htmlspecialchars(var_export(debug_backtrace(), true));
		$phpErrorOut .= "</pre></div>";
	} else {
		$phpErrorOut .= "<h2>Backtrace</h2>\n";
		$bt = debug_backtrace();
		
		foreach ($bt As $idx => $val) {
			if ($idx == 0) continue;
			$pFunction = (isset($val["function"])) ? ($val["function"] . "()") : "";
			$pLine = (isset($val["line"])) ? $val["line"] : "";
			$pFile = (isset($val["file"])) ? $val["file"] : "";
			$pClass = (isset($val["class"])) ? $val["class"] : "";
			$pType = (isset($val["type"])) ? $val["type"] : "";
			
			if ($pClass != "" && $pFunction != "") {
				$errCall = $pClass . $pType . $pFunction;
			} else if ($pFunction != "") {
				$errCall = $pFunction;
			} else {
				$errCall = "";
			}
			
			$errPlace = ($pFile != "") ? " (File {$pFile}, line {$pLine})" : "";
			$phpErrorOut .= "<div>{$idx}: <b>{$errCall}</b>{$errPlace}</div>\n";
			$phpErrorOut .= "<div><br /></div>\n";
		}

		$query = $_SERVER['REQUEST_URI'];
		$query = preg_replace("/[?&]$/", "", $query);
		$query = htmlspecialchars($query);
		$sign = (strpos($query, "?") === false) ? "?" : "&amp;";
		$query = $query . $sign . "fullbacktrace=1";
		$phpErrorOut .= "<div><a href=\"{$query}\">Full backtrace</a></div>\n";
	}

	$phpErrorOut .= "<div><br /></div><hr />";
	$phpErrorOut .= "</div>";
	
	global $phpError;
	$phpError = true;
}

function GetErrorDescription($errno) {
	switch ($errno) {
		case E_ERROR: 
			return array("E_ERROR", "Fatal run-time errors. These indicate errors that can not be recovered from, such as a memory allocation problem. Execution of the script is halted."); 
			break;
		
		case E_WARNING: 
			return array("E_WARNING", "Run-time warnings (non-fatal errors). Execution of the script is not halted."); 
			break;
		
		case E_PARSE: 
			return array("E_PARSE", "Compile-time parse errors. Parse errors should only be generated by the parser."); 
			break;
		
		case E_NOTICE: 
			return array("E_NOTICE", "Run-time notices. Indicate that the script encountered something that could indicate an error, but could also happen in the normal course of running a script."); 
			break;
		
		case E_CORE_ERROR: 
			return array("E_CORE_ERROR", "Fatal errors that occur during PHP's initial startup. This is like an E_ERROR, except it is generated by the core of PHP."); 
			break;
		
		case E_CORE_WARNING: 
			return array("E_CORE_WARNING", "Warnings (non-fatal errors) that occur during PHP's initial startup. This is like an E_WARNING, except it is generated by the core of PHP."); 
			break;
		
		case E_COMPILE_ERROR: 
			return array("E_COMPILE_ERROR", "Fatal compile-time errors. This is like an E_ERROR, except it is generated by the Zend Scripting Engine."); 
			break;
		
		case E_COMPILE_WARNING: 
			return array("E_COMPILE_WARNING", "Compile-time warnings (non-fatal errors). This is like an E_WARNING, except it is generated by the Zend Scripting Engine."); 
			break;
		
		case E_USER_ERROR: 
			return array("E_USER_ERROR", "User-generated error message. This is like an E_ERROR, except it is generated in PHP code by using the PHP function trigger_error()."); 
			break;
		
		case E_USER_WARNING: 
			return array("E_USER_WARNING", "User-generated warning message. This is like an E_WARNING, except it is generated in PHP code by using the PHP function trigger_error()."); 
			break;
		
		case E_USER_NOTICE: 
			return array("E_USER_NOTICE", "User-generated notice message. This is like an E_NOTICE, except it is generated in PHP code by using the PHP function trigger_error()."); 
			break;
		
		case E_STRICT: 
			return array("E_STRICT", "Run-time notices. Enable to have PHP suggest changes to your code which will ensure the best interoperability and forward compatibility of your code."); 
			break;
		
		default: 
			return array("", "");
			break;
	}
}

?>