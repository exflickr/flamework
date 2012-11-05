<?
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

	$dirname = dirname(__FILE__);
	include($dirname.'/wrapper.php');

	plan(1);

	# A couple things (20121105/straup):
	#
	# 1) This is mostly here to make the Travis CI robots (at Github) stop
	#    losing their minds. This test will basically always fail because
	#    the config file is prevented from being checked in, by design.
	#
	# 2) Something (else) appears to be trapping STDOUT so the message below
	#    is not actually displayed. This is not ideal...

	if (! file_exists($dirname.'/../www/include/config.php')){
		skip("You don't have a config file! How can you have any pudding if you don't have a config file?!", 1);
	}

	else {
		is($GLOBALS['TESTING_notice_buffer'], "");
	}
