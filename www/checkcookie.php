<?
	#
	# $Id$
	#

	include("include/init.php");

	#
	# do we have a valid cookie set?
	#

	if (!login_check_login()){

		$smarty->display("page_checkcookie.txt");
		exit;
	}

	#
	# where shall we bounce to?
	#

	$redir = $GLOBALS['cfg']['abs_root_url'];

	if ($_redir = get_str("redir")){
		$redir .= $_redir;
	}

	#
	# go!
	#

	header("location: {$redir}");
	exit;
?>
