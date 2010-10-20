<?
	#
	# $Id$
	#

	# HEY LOOK! THIS DOESN'T ACTUALLY SEND PASSWORD RESET EMAILS.
	# YET. (20101018/asc)

	include("include/init.php");

	if (login_is_loggedin()){

		header("location: /");
		exit();
	}

	$reset_code = post_str('reset');

	if (! $reset_code){
		$reset_code = get_str('reset');
	}

	if (! $reset_code){

		# seriously, go away...

		header("location: /");
		exit();
	}

	$user = users_get_by_password_reset_code($reset_code);

	if (! $user){

		$GLOBALS['error']['nouser'] = 1;		
		$smarty->display('page_password_reset.txt');
		exit();	
	}

	$new_reset_code = users_generate_password_reset_code($user);

	$smarty->assign('reset_code', $new_reset_code);

	if (post_str('reset')){

		$new_password1 = post_str('new_password1');
		$new_password2 = post_str('new_password2');

		if ((! $new_password1) || (! $new_password2)){

			$GLOBALS['error']['missing_password'] = 1;
			$smarty->display('page_password_reset.txt');
			exit();	
		}

		if ($new_password1 !== $new_password2){

			$GLOBALS['error']['password_mismatch'] = 1;
			$smarty->display('page_password_reset.txt');
			exit();	
		}

		if (! users_update_password($user, $new_password1)){

			$GLOBALS['error']['update_failed'] = 1;
			$smarty->display('page_password_reset.txt');
			exit();	
		}

		users_purge_password_reset_codes($user);

		$user = users_get_by_id($user['user_id']);

		login_do_login($user, "/account/?password=1");
		exit();	
	}


	#
	# output
	#

	$smarty->display('page_password_reset.txt');
?>