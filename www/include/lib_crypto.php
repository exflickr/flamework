<?php

	#
	# Welcome to our encryption/decryption library. It has some history.
	#
	# mcrypt_encrypt/decrypt were deprecated in PHP 7.1.0 and removed in 7.2.0
	# They, and the functions calling them, have been replaced with the 
	# polyfill'd versions from https://github.com/phpseclib/mcrypt_compat
	# They are now crypto_encrypt/decrypt_old
	#
	# Use crypto_encrypt/decrypt for new code -- it attempts to detect use of the
	# old (and it turns out incorrect!) use of mcrypt when decrypting, and handles
	# it. So, in theory, your cookies and logins won't break. But you should upgrade them.
	#

	const CIPHER_METHOD = 'aes-256-gcm';

	#################################################################

	function crypto_encrypt($data, $key){
		if (mb_strlen($key)) {
			$key = hash('sha256', $key, true);
		} else {
			log_fatal('[lib_crypto] Trying to encrypt with a blank key');
		}

		$iv = openssl_random_pseudo_bytes(24);
		if (!$iv) log_fatal('[lib_crypto] Error generating IV');

		$tag = null;
		$ciphertext = openssl_encrypt($data, CIPHER_METHOD, $key, 0, $iv, $tag);
		if (!$ciphertext) log_fatal('[lib_crypto] Encryption error');

		$out = $iv . $ciphertext . $tag;
		return base64_encode($out);
	}

	#################################################################

	function crypto_decrypt($enc_b64, $key){
		if (strlen($key)) $key = hash('sha256', $key, true);

		$enc = base64_decode($enc_b64);

		$enc_text = substr($enc, 24, -16); # Ciphertext is between the iv and tag
		$tag = substr($enc, -16); # Last 16 bits (tag length is 16 by default)
		$iv = substr($enc, 0, 24); # First 24 bits

		if (strlen($iv) !== 24) {
			log_error('[lib_crypto] Invalid IV length');
			return '';
		}

		$dec = openssl_decrypt($enc_text, CIPHER_METHOD, $key, 0, $iv, $tag);
		if (!$dec) {
			log_error('[lib_crypto] Decryption error');
			return '';
		}

		return trim($dec);
	}

	#################################################################

	function crypto_encrypt_old($data, $key){

		# TODO: Catch key size greater than... 32?
		if (!strlen($key)) log_fatal("[lib_crypto] Trying to encrypt with a blank key");

		$key = hash('sha256', $key, true);

		$enc = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB);
		return base64_encode($enc);


				$td = new Rijndael('ecb');
				$td->setBlockLength(256);
				$td->disablePadding();


				$iv = null;
				$td->setKey($key);

				$td->enableContinuousBuffer();
				$td->mcrypt_polyfill_init = true;

				return $td->encrypt($data);
	}

	#################################################################

	function crypto_decrypt_old($enc_b64, $key){

		if (strlen($key)) $key = hash('sha256', $key, true);

		$enc = base64_decode($enc_b64);
		$dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $enc, MCRYPT_MODE_ECB);

		return trim($dec);
	}

	#################################################################

