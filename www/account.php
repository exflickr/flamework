<?

	include("include/init.php");

	features_ensure_enabled("signin");

	login_ensure_loggedin();

	$GLOBALS['smarty']->display("page_account.txt");
	exit();

?>
