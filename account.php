<?php

	#
	# $Id$
	#

	include("include/init.php");

	login_ensure_loggedin("/account");

	$smarty->display("page_account.txt");
	exit();

?>