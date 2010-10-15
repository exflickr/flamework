<?php

	#
	# $Id$
	#

	#################################################################

	function crumb_generate_crumb($user=null, $ttl=null){

		if (! $ttl_secs){
			$ttl_secs = $GLOBALS['cfg']['crumb_ttl_default'];
		}

		$crumb_data = crumb_generate_crumb_data($user);

		$ttl_secs = time() + $ttl_secs;
		$crumb_data = implode(":", array($ttl_secs, $crumb_data));

		return crypto_encrypt($crumb_data, $GLOBALS['cfg']['crypto_crumb_secret']);
	}

	#################################################################

	function crumb_validate_crumb($crumb, $user=null){

		$crumb_data = crumb_generate_crumb_data($user);

		$crumb = crypto_decrypt($crumb, $GLOBALS['cfg']['crypto_crumb_secret']);

		list($test_ttl, $test_data) = explode(":", $crumb, 2);

		if ($crumb_data != $test_data){
			return 0;
		}

		if ($test_ttl < time()){
			return 0;
		}

		return 1;
	}

	#################################################################

	function crumb_generate_crumb_data($user=null){

		$data = array(
			$GLOBALS['_SERVER']['HTTP_USER_AGENT'],
			$GLOBALS['_SERVER']['SCRIPT_NAME'],
			$GLOBALS['_SERVER']['REMOTE_ADDR'],	# check if mobile?
		);

		if ($user){
			$data[] = md5($user['created'] * $user['user_id']);
		}

		return base64_encode(implode(":", $data));
	}

	#################################################################

	function crumb_ensure_valid_crumb($template='/page_bad_crumb.txt'){

		$crumb = post_str('crumb');

		if (! crumb_validate_crumb($crumb, $GLOBALS['cfg']['user'])){

			$GLOBALS['error']['badcrumb'] = 1;
			$smarty->display($template);
			exit();
		}

		return 1;
	}

	#################################################################
?>