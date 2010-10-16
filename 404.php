<?php
	include('include/init.php');


	#
	# This is where you redirect users to a different page incase you've moved stuff or you can use .htaccess
	# do this for you.
	#

	if ($_SERVER['REQUEST_URI'] == '/home'){

		header("Location: /");
		exit;
	}


	#
	# output
	#

	$smarty->display('page_404.txt');
?>