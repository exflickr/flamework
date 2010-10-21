<?php
	#
	# $Id$
	#

	#################################################################

	function login_ensure_loggedin($redir=null){

		if (!login_check_login()){
			if ($redir){
				header("location: /signin/?redir=".urlencode($redir));
			}else{
				header("location: /signin/");
			}
			exit;
		}
	}

	#################################################################

	function login_ensure_loggedout($redir="/"){

		if (login_check_login()){
			header("location: $redir");
			exit;
		}

	}

	#################################################################

	function login_is_loggedin(){
		return $GLOBALS['cfg']['user']['id'] ? true : false;
	}

	#################################################################

	function login_check_login(){

		if ($GLOBALS['cfg']['user']['id']){
			return 1;
		}

		$auth_cookie = login_get_cookie($GLOBALS['cfg']['auth_cookie_name']);

		if (! $auth_cookie){
			return 0;
		}

		$auth_cookie = crypto_decrypt($auth_cookie, $GLOBALS['cfg']['crypto_cookie_secret']);

		list($user_id, $password) = explode(':', $auth_cookie, 2);

		if (! $user_id){
			return 0;
		}

		$user = users_get_by_id($user_id, $password);

		if (! $user){
			return 0;
		}

		if ($user['deleted']){
			return 0;
		}

		if ($user['password'] !== $password){
			return 0;
		}
 
		$GLOBALS['cfg']['user'] = $user;

		return 1;
	}

	#################################################################

	function login_do_login(&$user, $redir=''){

		$auth_cookie = login_generate_auth_cookie($user);
		login_set_cookie($GLOBALS['cfg']['auth_cookie_name'], $auth_cookie);

		if (! $redir){
			$redir = '/';
		}

		$redir = urlencode($redir);
		header("location: /checkcookie/?redir={$redir}");
		exit;
	}

	#################################################################

	function login_do_logout(){
		$GLOBALS['cfg']['user'] = null;
		login_unset_cookie($GLOBALS['cfg']['auth_cookie_name']);
	}

	#################################################################

	function login_generate_auth_cookie(&$user){

		$cookie = implode(":", array($user['id'], $user['password']));
		return crypto_encrypt($cookie, $GLOBALS['cfg']['crypto_cookie_secret']);
	}

	#################################################################

	function login_encrypt_password($pass){
		return hash_hmac("sha256", $pass, $GLOBALS['cfg']['crypto_password_secret']);
	}

	#################################################################

	function login_get_cookie($name){
		return $_COOKIE[$name];
	}

	#################################################################

	function login_set_cookie($name, $value, $expire=0, $path='/'){
		$res = setcookie($name, $value, $expire, $path, $GLOBALS['cfg']['auth_cookie_domain']);
	}

	#################################################################

	function login_unset_cookie($name){
		login_set_cookie($name, "", time() - 3600);
	}

	#################################################################
?>
