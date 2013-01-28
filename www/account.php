<?

	include("include/init.php");

	features_ensure_enabled("signin");
	login_ensure_loggedin("/account");
	
	$GLOBALS['smarty']->assign('nav_tab', 'account');
	$GLOBALS['smarty']->display("page_account.txt");
	exit();
