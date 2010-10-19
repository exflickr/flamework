<?
	#
	# $Id$
	#

	include("include/init.php");

	login_ensure_loggedin("/account/password/change");

	$new_crumb = crumb_generate_crumb($GLOBALS['cfg']['user']);
	$smarty->assign("crumb", $new_crumb);

	if (post_str('change')){

		$crumb = post_str('crumb');

		if (! crumb_validate_crumb($crumb, $GLOBALS['cfg']['user'])){

			$GLOBALS['error']['badcrumb'] = 1;
			$smarty->display("page_account_password.txt");
			exit();
		}

		$old_pass = post_str('old_password');

		if (login_encrypt_password($old_pass) !== $GLOBALS['cfg']['user']['password']){

			$GLOBALS['error']['oldpass_mismatch'] = 1;
			$smarty->display("page_account_password.txt");
			exit();
		}

		$new_pass1 = post_str('new_password1');
		$new_pass2 = post_str('new_password2');

		if ((trim($new_pass1) === '') || (trim($new_pass2) === '')){

			$GLOBALS['error']['newpass_empty'] = 1;
			$smarty->display("page_account_password.txt");
			exit();
		}

		if ($new_pass1 !== $new_pass2){

			$GLOBALS['error']['newpass_mismatch'] = 1;
			$smarty->display("page_account_password.txt");
			exit();
		}

		if (! users_update_password($GLOBALS['cfg']['user'], $new_pass1)){
			$GLOBALS['error']['fail'] = 1;
			$smarty->display("page_account_password.txt");
			exit();
		}

		# Refresh the user so that we pick up the newer password when
		# we set new cookies. Should this be a function in lib_users?
		# (20101012/asc)

		$GLOBALS['cfg']['user'] = users_get_by_id($GLOBALS['cfg']['user']['user_id']);

		login_do_login($GLOBALS['cfg']['user'], "/account/?password=1");
		exit();
	}


	#
	# output
	#

	$smarty->display("page_account_password.txt");
?>