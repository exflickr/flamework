<?php
	include('include/init.php');


	#
	# this is so we can test the logging output
	#

	if (get_bool('log_test')){
		log_error("This is an error!");
		log_fatal("Fatal error!");
	}


	#
	# this is so we can test the HTTP library
	#

	if (get_bool('http_test')){
		$ret = http_get("http://google.com");
	}


	#
	# output
	#

	$smarty->display('page_index.txt');
