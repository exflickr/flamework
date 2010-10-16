<?php
	#
	# we don't want to connect to the database here
	#
	$_GET['no_db'] = true;

	include('include/init.php');

	#
	# This is where you redirect users to a different page incase you've moved stuff or you can use .htaccess
	# do this for you.
	#
	if (
		$_SERVER['REQUEST_URI'] == '/home'
	) {
		Header("Location: /");
		exit;
	}

	#
	# output
	#
	Header("HTTP/1.1 404 Not Found")

	$smarty->display('page_404.txt');
?>