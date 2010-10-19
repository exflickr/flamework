<?
	#
	# this test turns on notice processing *before* loading init, so that we can see
	# if there are any unquoted keys in init itself.
	#

	set_error_handler('test_handle_errors', E_NOTICE);
	error_reporting(E_ALL | E_STRICT);

	function test_handle_errors($errno, $errstr){
		if (preg_match('!^Use of undefined constant!', $errstr)) return false;
		return true;
	}


	include('../include/init.php');

	echo "loaded init ok.<br />";
	echo "if there are no error messages above, all is well.";
?>