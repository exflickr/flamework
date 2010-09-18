<?
	#
	# $Id$
	#

	include('include/init.php');


	#
	# this is so we can test the logging output
	#

	if ($_GET[log_test]){
		log_error("This is an error!");
		log_fatal("Fatal error!");
	}


	#
	# output
	#

	$smarty->display('page_index.txt');
?>