<?
	#
	# $Id$
	#

	include("include/init.php");


	#
	# are they signed in?
	#

	if (!login_check_login()){


		#
		#
		#

		if (get_isset('deleted')){

			$smarty->assign('step', 'deleted');

			$smarty->display('page_account_delete.txt');
			exit;
		}

		header("location: /");
		exit;
	}


	#
	# generate a crumb
	#

	$new_crumb = crumb_generate_crumb($GLOBALS['cfg']['user']);
	$smarty->assign('crumb', $new_crumb);


	#
	# delete account?
	#

	if (post_str('delete')){

		$crumb = post_str('crumb');

		if (! crumb_validate_crumb($crumb, $GLOBALS['cfg']['user'])){

			$GLOBALS['error']['badcrumb'] = 1;
			$smarty->display('page_account_delete.txt');
			exit();
		}

		if (post_str('confirm')){

			$ok = users_delete_user($GLOBALS['cfg']['user']);

			if ($ok){
				login_do_logout('/account/delete?deleted=1');
				exit();
			}

			$smarty->assign('step', 'delete');
			$smarty->display('page_account_delete.txt');
			exit();
		}

		$smarty->assign('step', 'confirm');

		$smarty->display('page_account_delete.txt');
		exit;
	}


	#
	# output
	#

	$smarty->assign('step', 'start');

	$smarty->display("page_account_delete.txt");
	exit;
?>