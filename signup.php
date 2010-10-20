<?
	#
	# $Id$
	#

	include("include/init.php");

	if (login_is_loggedin()){

		$GLOBALS['error']['loggedin'] = 1;
		$smarty->display('page_signup.txt');
		exit();
	}

	if (post_str('signup')){

		$email = post_str('email');
		$password = post_str('password');
		$username = post_str('username');

		$redir = post_str('redir');
		$smarty->assign('redir', $redir);

		if ((! $email) || (! $password)){

			$GLOBALS['error']['missing'] = 1;
			$smarty->display('page_signup.txt');
			exit();
		}

		if (users_is_email_taken($email)){

			$GLOBALS['error']['email_taken'] = 1;

			$smarty->assign('username', $username);
			$smarty->assign('password', $password);
			$smarty->display('page_signup.txt');
			exit();
		}

		if (($username) && (users_is_username_taken($username))){

			$GLOBALS['error']['username_taken'] = 1;

			$smarty->assign('email', $email);
			$smarty->assign('password', $password);
			$smarty->display('page_signup.txt');
			exit();
		}

		$user = users_create_user(array(
			'username'	=> $username,
			'email'		=> $email,
			'password'	=> $password,
		));

		if (!$user['user_id']){

			$GLOBALS['error']['failed'] = 1;
			$smarty->display('page_signup.txt');
			exit;
		}

		$redir = ($redir) ? $redir : '/';

		login_do_login($user);
		exit;
	}

	$smarty->assign('redir', get_str('redir'));


	#
	# output
	#

	$smarty->display('page_signup.txt');
?>