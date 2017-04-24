<?php

	#################################################################

	#
	# make sure the users is signed in. if not, bounce them
	# to the login page, with an optional post-login redirect.
	#

	function login_ensure_loggedin($redir=null){

		if ($GLOBALS['cfg']['user']['id']){
			return;
		}

		if (! $redir){
			# $redir = ltrim($_SERVER['REQUEST_URI'], "/");
			$redir = $_SERVER['REQUEST_URI'];
		}

		header("location: {$GLOBALS['cfg']['abs_root_url']}signin/?redir={$redir}");
		exit;
	}

	#################################################################

	#
	# make sure the user is NOT logged in. if they are, redirect them,
	# optionally logging them out first.
	#

	function login_ensure_loggedout($redir="", $force_logout=false){

		if (! $GLOBALS['cfg']['user']['id']){
			return;
		}

		if ($force_logout){
			login_do_logout();
		}

		header("location: {$GLOBALS['cfg']['abs_root_url']}{$redir}");
		exit;
	}

	#################################################################

	function login_check_login(){

		if (!$GLOBALS['cfg']['enable_feature_signin']){
			return 0;
		}

		if ($GLOBALS['cfg']['user']['id']){
			return 1;
		}

		$auth_cookie = login_get_cookie($GLOBALS['cfg']['auth_cookie_name']);

		if (!$auth_cookie){
			return 0;
		}

		$auth_cookie = crypto_decrypt($auth_cookie, $GLOBALS['cfg']['crypto_cookie_secret']);

		list($user_id, $password) = explode(':', $auth_cookie, 2);

		if (!$user_id){
			return 0;
		}

		$user = users_get_by_id($user_id);

		if (!$user){
			return 0;
		}

		if ($user['deleted']){
			return 0;
		}

		if ($user['password'] != $password){
			return 0;
		}

		$GLOBALS['cfg']['user'] = $user;

		return 1;
	}

	#################################################################

	function login_do_login(&$user, $redir=''){

		$expires = ($GLOBALS['cfg']['enable_feature_persistent_login']) ? strtotime('now +10 years') : 0;

		$auth_cookie = login_generate_auth_cookie($user);
		login_set_cookie($GLOBALS['cfg']['auth_cookie_name'], $auth_cookie, $expires);

		$url = "{$GLOBALS['cfg']['abs_root_url']}checkcookie/";

		if ($redir){
			$url .= "?redir={$redir}";
		}

		header("location: {$url}");
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

	function login_get_cookie($name){
		return $_COOKIE[$name];
	}

	#################################################################

	# inre: "securification"
	# 
	# The vulnerability stems from website developers' failure to designate
	# authentication cookies as secure. That means web browsers are free to
	# send them over the insecure http channel, and that's exactly what CookieMonster
	# causes them to do. It does this by caching all DNS responses and then
	# monitoring hostnames that use port 443 to connect to one of the domain names
	# stored there. CookieMonster then injects images from insecure (non-https)
	# portions of the protected website, and - voila! - the browser sends the
	# authentication cookie.
	# http://www.theregister.co.uk/2008/09/11/cookiemonstor_rampage/

	# See also:
	# https://code.google.com/p/cookiemonster/source/browse/trunk/cookiemonster.py

	function login_set_cookie($name, $value, $expire=0, $path='/'){
		$domain = ($GLOBALS['cfg']['environment'] == 'localhost') ? $GLOBALS['cfg']['auth_cookie_domain'] : false;
		$securify = (($GLOBALS['cfg']['auth_cookie_require_https']) && (isset($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on')) ? 1 : 0;
		$res = setcookie($name, $value, $expire, $path, $domain, $securify);
	}

	#################################################################

	function login_unset_cookie($name){
		login_set_cookie($name, "", time() - 3600);
	}

	#################################################################

	# the end
