<?php

	#
	# $Id$
	#

	include("include/init.php");

	if (login_is_loggedin()){

		header("location: /");
		exit();
	}

	if (post_str('signin')){

		$email = post_str('email');
		$password = post_str('password');
		$redir = post_str('redir');

		$smarty->assign('email', $email);
		$smarty->assign('redir', $redir);

		if ((! $email) || (! $password)){

			$GLOBALS['error']['missing'] = 1;
			$smarty->display('page_signin.txt');
			exit();
		}

		$user = users_get_by_email($email);

		if (! $user['user_id']){

			$GLOBALS['error']['nouser'] = 1;
			$smarty->display('page_signin.txt');
			exit();
		}

		if ($user['deleted']){

			$GLOBALS['error']['deleted'] = 1;
			$smarty->display('page_signin.txt');
			exit();
		}

		$enc_password = login_encrypt_password($password, $GLOBALS['cfg']['crypto_password_secret']);

		if ($enc_password != $user['password']){

			$GLOBALS['error']['password'] = 1;
			$smarty->display('page_signin.txt');
			exit();
		}

		$redir = ($redir) ? $redir : '/';

		login_do_login($user, $redir);
		exit();
	}

	$smarty->assign('redir', get_str('redir'));

	$smarty->display('page_signin.txt');
	exit();

?>