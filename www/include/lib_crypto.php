<?php
	if (!defined('MCRYPT_RIJNDAEL_256')) die("lib_crypto requires MCRYPT_RIJNDAEL_256");
	if (!defined('MCRYPT_MODE_ECB')) die("lib_crypto requires MCRYPT_MODE_ECB");

	#################################################################

	function crypto_encrypt($data, $key){

		$enc = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB);
		return base64_encode($enc);
	}

	#################################################################

	function crypto_decrypt($enc_b64, $key){

		$enc = base64_decode($enc_b64);
		$dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $enc, MCRYPT_MODE_ECB);

		return trim($dec);
	}

	#################################################################

