<?
	#
	# $Id$
	#

	include("include/init.php");

	login_ensure_loggedin();
	
	$GLOBALS['smarty']->assign('nav_tab', 'account');


	#
	# output
	#

	$smarty->display("page_account.txt");
