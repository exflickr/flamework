<?php

	loadlib("crypto");

	function crumb_generate_crumb($ttl_min, $context=''){

		$context = crumb_ensure_context($context);
		$crumb_data = crumb_generate_crumb_data($context);

		$ttl_secs = time() + (60 * $ttl_min);
		$crumb_data = implode(":", array($ttl_secs, $crumb_data));

		return crypto_encrypt($crumb_data, $ttl);
	}

	function crumb_validate_crumb($crumb, $context=''){

		$context = crumb_ensure_context($context);
		$crumb_data = crumb_generate_crumb_data($context);

		$crumb = crypto_decrypt($crumb);

		list($test_ttl, $test_data) = explode(":", $crumb, 2);

		if ($crumb_data != $test_data){
			return 0;
		}

		if ($test_ttl < time()){
			return 0;
		}

		return 1;
	}

	function crumb_generate_crumb_data($context){

		$data = array(
			$GLOBALS['HTTP_SERVER_VARS']['HTTP_USER_AGENT'],
			$context,
		);

		if ($context){
			$data[] = $context;
		}

		return base64_encode(implode(":", $data));
	}

	function crumb_ensure_context($context){

		if (! $context){
			$context = $GLOBALS['HTTP_SERVER_VARS']['SCRIPT_NAME'];
		}

		return $context;
	}
?>