<?php
	#
	# this test turns on notice processing *before* loading init, so that we can see
	# if there are any unquoted keys in init itself.
	#

	$GLOBALS['TESTING_notice_buffer'] = "";

	set_error_handler('test_handle_errors', E_NOTICE);
	error_reporting(E_ALL | E_STRICT);

	function test_handle_errors($errno, $errstr, $errfile, $errline){
		$GLOBALS['TESTING_notice_buffer'] .= "ERROR: $errstr in $errfile, line $errline\n";
		return true;
	}


	#
	# now load the wrapper, which calls init
	#

	include(dirname(__FILE__).'/wrapper.php');

	plan(1);

	is($GLOBALS['TESTING_notice_buffer'], "");
