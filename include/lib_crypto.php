<?php

	$GLOBALS['crypto_td'] = MCRYPT_RIJNDAEL_256;
	$GLOBALS['crypto_ivsize'] = mcrypt_get_iv_size($GLOBALS['crypto_td'], MCRYPT_MODE_ECB);
	$GLOBALS['crypto_iv'] = mcrypt_create_iv($GLOBALS['crypto_ivsize'], MCRYPT_RAND);

	function crypto_encrypt($data, $ttl=0, $secret=''){

		$secret = crypto_ensure_secret($secret);
		$enc = mcrypt_encrypt($GLOBALS['crypto_td'], $secret, $data, MCRYPT_MODE_ECB, $GLOBALS['crypto_iv']);

		return base64_encode($enc);
	}

	function crypto_decrypt($enc_b64, $secret=''){

		$secret = crypto_ensure_secret($secret);

		$enc = base64_decode($enc_b64);
		$dec = mcrypt_decrypt($GLOBALS['crypto_td'], $secret, $enc, MCRYPT_MODE_ECB, $GLOBALS['crypto_iv']);

		return trim($dec);
	}

	function crypto_ensure_secret($secret=''){

		if (! $secret){
			$secret = $GLOBALS['cfg']['crypto_secret'];
		}

		return $secret;
	}

?>