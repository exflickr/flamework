<?php
	include("include/init.php");

	login_ensure_loggedin();


	#
	# output
	#

	$smarty->display("page_account.txt");
