<?
	#
	# $Id$
	#

	include("include/init.php");

	features_ensure_enabled("signin");
	features_ensure_enabled("account_delete");

	login_ensure_loggedin();
	
	$GLOBALS['smarty']->assign('nav_tab', 'account');


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

<<<<<<< HEAD
?>
=======
	#
	# output
	#

	$smarty->display("page_account_delete.txt");
>>>>>>> 7644b1df38b8fedecd5c03c2f0c75d2c243724fd
