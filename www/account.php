<?
	#
	# $Id$
	#

	include("include/init.php");

	login_ensure_loggedin("/account");
	
	$GLOBALS['cfg']['nav_tab'] = 'account'; // for the navbar


	#
	# output
	#

	$smarty->display("page_account.txt");
?>