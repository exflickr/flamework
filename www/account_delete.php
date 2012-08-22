<?
	#
	# $Id$
	#

	include("include/init.php");

	features_ensure_enabled("signin");
	features_ensure_enabled("account_delete");

	login_ensure_loggedin();


	#
	# generate a crumb
	#

	$crumb_key = 'account_delete';
	$GLOBALS['smarty']->assign('crumb_key', $crumb_key);


	#
	# delete account?
	#

	if (post_str('delete') && crumb_check($crumb_key)){

		if (post_str('confirm')){

			$ok = users_delete_user($GLOBALS['cfg']['user']);

			if ($ok){
				login_do_logout();

				$GLOBALS['smarty']->display('page_account_delete_done.txt');
				exit;
			}

			$GLOBALS['smarty']->assign('error_deleting', 1);

			$GLOBALS['smarty']->display('page_account_delete.txt');

			exit();
		}

		$GLOBALS['smarty']->display('page_account_delete_confirm.txt');
		exit();
	}

	$GLOBALS['smarty']->display("page_account_delete.txt");
	exit();

?>
