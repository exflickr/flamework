<?php
	#
	# we don't want to connect to the database here
	#
	$_GET['no_db'] = true;

	include('include/init.php');

	#
	# output
	#
	Header("HTTP/1.1 403 Forbidden");

	$smarty->display('page_403.txt');
?>