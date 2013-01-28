<?
	#
	# $Id$
	#

	include("include/init.php");

	features_ensure_enabled("signin");
	features_ensure_enabled("password_retrieval");

	login_ensure_loggedout();

	$GLOBALS['smarty']->assign('nav_tab', 'signin');

	#
	# send the reminder?
	#

	if (post_str('remind')){

		$email	= post_str('email');
		$user	= users_get_by_email($email);

		$ok = 1;

		if (!$user){

			$GLOBALS['smarty']->assign('error_nouser', 1);
			$ok = 0;
		}

		if ($ok && $user['deleted']){

			$GLOBALS['smarty']->assign('error_deleted', 1);
			$ok = 0;
		}

		if ($ok && !users_send_password_reset_code($user)){

			$GLOBALS['smarty']->assign('error_notsent', 1);
			$ok = 0;
		}

		if ($ok){
			$GLOBALS['smarty']->assign('sent_to', $user['email']);

			$GLOBALS['smarty']->display('page_forgot_sent.txt');
			exit();
		}
	}

	$GLOBALS['smarty']->display('page_forgot.txt');
