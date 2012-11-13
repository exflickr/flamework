<?
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', '1');

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
