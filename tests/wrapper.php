<?php
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', '1');

	if (function_exists('xdebug_get_code_coverage')){
	if (@include('PHP/CodeCoverage/Autoload.php')){

		$dir = dirname(__FILE__);
		$file = "$dir/coverage.state";

		if (file_exists($file)){
			$coverage = unserialize(file_get_contents($file));
		}else{
			$coverage = new PHP_CodeCoverage;
		}

		$trace = debug_backtrace();
		$name = basename($trace[0]['file']);

		$coverage->start($name);

		register_shutdown_function('end_coverage');
	}
	}

	function end_coverage(){
		$GLOBALS['coverage']->stop();

		$dir = dirname(__FILE__);
		$fh = fopen("$dir/coverage.state", 'w');
		fwrite($fh, serialize($GLOBALS['coverage']));
		fclose($fh);
	}

	$dir = dirname(__FILE__);
	include($dir.'/testmore.php');
	include($dir.'/../www/include/init.php');

	$GLOBALS['log_handlers']['error'] = array('test_wrapper');
	$GLOBALS['log_handlers']['fatal'] = array('test_wrapper');

	function _log_handler_test_wrapper($level, $msg, $more = array()){

		$type = $more['type'] ? $more['type'] : $level;

		$out = '';

		if ($type) $out .= "[$type] ";

		$out .= $msg;

		if ($more['time'] > -1) $out .= " ($more[time] ms)";

		diag($out);
	}
