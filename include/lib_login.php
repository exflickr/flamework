<?php

	#
	# $Id$
	#

	#################################################################

	function login_ensure_loggedin($redir='/'){

		if (login_is_loggedin()){
			return 1;
		}

		$redir = urlencode($redir);
		header("location: /signin?redir={$redir}");
		exit();
	}

	#################################################################

	function login_is_loggedin(){

		If (($GLOBALS['cfg']['user']) && ($GLOBALS['cfg']['user_ok'])){
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

		$GLOBALS['cfg']['user_ok'] = 1;
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
		header("location: /checkcookie?redir={$redir}");

		exit();
	}

	#################################################################

	function login_do_logout(&$user){

		login_unset_cookie($GLOBALS['cfg']['auth_cookie_name']);

		header("location: /");
		exit();
	}

	#################################################################

	function login_generate_auth_cookie(&$user){

		$cookie = implode(":", array($user['user_id'], $user['password']));
		return crypto_encrypt($cookie, $GLOBALS['cfg']['crypto_cookie_secret']);
	}

	#################################################################

	function login_encrypt_password($pass, $secret=''){
		return hash_hmac("sha256", $pass, $secret);
	}

	#################################################################

	function login_get_cookie($name){
		return $_COOKIE[$name];
	}

	#################################################################

	function login_set_cookie($name, $value, $expire=0, $path='/'){
		$res = setcookie($name, $value, $expire, $path, $GLOBALS['cfg']['auth_cookie_domain']);

		error_log("[COOKIE] $name, $value, $expire, $path, {$GLOBALS['cfg']['auth_cookie_domain']}");
		error_log("[COOKIE] {$res}");
	}

	#################################################################

	function login_unset_cookie($name){
		login_set_cookie($name, "", time() - 3600); 
	}

	#################################################################
?>