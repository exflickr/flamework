<?php
	include("include/init.php");


	#
	# do we have a valid cookie set?
	#

	if (!login_check_login()){

		$smarty->display("page_error_cookie.txt");
		exit;
	}


	#
	# where shall we bounce to?
	#

	$redir = $GLOBALS['cfg']['abs_root_url'];

	if ($_redir = get_str("redir")){
		if (substr($_redir, 0, 1) == '/') $_redir = substr($_redir, 1);
		$redir .= $_redir;
	}


	#
	# go!
	#

	header("location: {$redir}");
	exit;
