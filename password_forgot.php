<?
	#
	# $Id$
	#

	include("include/init.php");


	#
	# only show this page if we're signed out
	#

	if (login_is_loggedin()){

		header("location: /");
		exit;
	}


	#
	# send the reminded?
	#

	if (post_str('remind')){

		$email = post_str('email');
		$user = users_get_by_email($email);

		if (! $user){

			$GLOBALS['error']['nouser'] = 1;
		}

		else if ($user['deleted']){

			$GLOBALS['error']['user_deleted'] = 1;
		}

		# check conf code here?

		else if (! users_generate_password_reset_code($user)){

			$GLOBALS['error']['notsent'] = 1;
		}

		else {
			$smarty->assign('sent', 1);
			$smarty->assign('sent_to', $user['email']);
		}
	}


	#
	# output
	#

	$smarty->display('page_password_forgot.txt');
?>