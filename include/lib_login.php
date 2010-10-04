<?php

	#
	# $Id$
	#

	function login_check_loggedin($force_signin=1, $redir='/'){

		If (($GLOBALS['cfg']['user']) && ($GLOBALS['cfg']['user_ok'])){
			return 1;
		}

		$auth_cookie = login_get_cookie($GLOBALS['cfg']['auth_cookie_name']);

		if (! $auth_cookie){
			return login_not_loggedin($force_signin, $redir);
		}

		$auth_cookie = crypto_decrypt($auth_cookie);

		list($user_id, $password) = explode(':', $auth_cookie, 2);

		if (! $user_id){
			return login_not_loggedin($force_signin, $redir);
		}

		$user = users_get_by_id($user_id, $password);

		if (! $user){
			return login_not_loggedin($force_signin, $redir);
		}

		if ($user['deleted']){
			login_do_logout($user);
		}

		users_email_load($user);

		$GLOBALS['cfg']['user_ok'] = 1;
		$GLOBALS['cfg']['user'] = $user;

		return 1;
	}

	function login_not_loggedin($force_signin=0, $redir='/'){
		
		if ($force_signin){
			$redir = urlencode($redir);
			header("location: /signin?redir={$redir}");
			exit();
		}

		 return 0;
	}

	function login_do_login(&$user, $redir=''){

		$cookie_name = 'a';

		$auth_cookie = login_generate_auth_cookie($user);
		login_set_cookie($cookie_name, $auth_cookie);

		if (! $redir){
			$redir = '/';
		}

		$redir = urlencode($redir);
		header("location: /cookiemonster?redir={$redir}");

		exit();
	}

	function login_do_logout(&$user){

		login_unset_cookie($GLOBALS['cfg']['auth_cookie_name']);

		header("location: /");
		exit();
	}

	function login_generate_auth_cookie(&$user){

		$cookie = implode(":", array($user['user_id'], $user['password']));
		return crypto_encrypt($cookie);
	}

	function login_encrypt_password($pass, $secret=''){
		$secret = login_ensure_secret($secret);
		return hash_hmac("sha256", $pass, $secret);
	}

	function login_get_cookie($name){
		return $_COOKIE[$name];
	}

	function login_set_cookie($name, $value, $expire=0, $path='/'){
		$res = setcookie($name, $value, $expire, $path, $GLOBALS['cfg']['auth_cookie_domain']);
	}

	function login_unset_cookie($name){
		login_set_cookie($name, "", time() - 3600); 
	}

	function login_ensure_secret($secret=''){
		return $secret;
	}

?>