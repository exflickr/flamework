<?php

	#
	# $Id$
	#

	include("include/init.php");

	if (! login_check_login()){

		if (get_isset('deleted')){

			$smarty->assign('step', 'deleted');
			$smarty->display('page_account_delete.txt');
			exit();
		}

		header("location: /");
		exit();
	}

	$new_crumb = crumb_generate_crumb($GLOBALS['cfg']['user']);
	$smarty->assign('crumb', $new_crumb);

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
		exit();
	}

	$smarty->assign('step', 'start');
	$smarty->display("page_account_delete.txt");
	exit();
?>