<?
	#
	# $Id$
	#

	include("include/init.php");


	#
	# are they signed in?
	#

	if (!login_check_login()){

		$smarty->display('page_account_delete_done.txt');
		exit;
	}


	#
	# generate a crumb
	#

	$crumb_key = 'account_delete';
	$smarty->assign('crumb_key', $crumb_key);


	#
	# delete account?
	#

	if (post_str('delete') && crumb_check($crumb_key)){

		if (post_str('confirm')){

			$ok = users_delete_user($GLOBALS['cfg']['user']);

			if ($ok){
				login_do_logout('/account/delete/?deleted=1');
				exit;
			}

			$smarty->assign('error_deleting', 1);

			$smarty->display('page_account_delete.txt');
			exit;
		}

		$smarty->display('page_account_delete_confirm.txt');
		exit;
	}


	#
	# output
	#

	$smarty->display("page_account_delete.txt");
	exit;
?>