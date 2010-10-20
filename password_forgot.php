<?
	#
	# $Id$
	#

	include("include/init.php");

	login_ensure_loggedout();


	#
	# send the reminder?
	#

	if (post_str('remind')){

		$email	= post_str('email');
		$user	= users_get_by_email($email);

		$ok = 1;

		if (!$user){

			$smarty->assign('error_nouser', 1);
			$ok = 0;
		}

		if ($ok && $user['deleted']){

			$smarty->assign('error_deleted', 1);
			$ok = 0;
		}

		if ($ok && !users_generate_password_reset_code($user)){

			$smarty->assign('error_notsent', 1);
			$ok = 0;
		}

		if ($ok){
			$smarty->assign('sent_to', $user['email']);

			$smarty->display('page_password_forgot_sent.txt');
			exit;
		}
	}


	#
	# output
	#

	$smarty->display('page_password_forgot.txt');
?>