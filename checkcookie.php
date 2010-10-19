<?php

	#
	# $Id$
	#

	include("include/init.php");

	if (! login_check_login()){

		$GLOBALS['error']['badcookies'] = 1;
		$smarty->display("page_checkcookie.txt");
		exit();
	}

	$redir = "/";

	if ($alt_redir = get_str("redir")){
		$redir = $alt_redir;
	}

	header("location: {$redir}");
	exit();

?>