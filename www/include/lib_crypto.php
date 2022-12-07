<?php

	#
	# Welcome to our encryption/decryption library. It has some history.
	#
	# mcrypt_encrypt/decrypt were deprecated in PHP 7.1.0 and removed in 7.2.0
	# They, and the functions calling them, have been replaced with modern (and correct)
	# encryption functions powered by Sodium: https://www.php.net/manual/en/intro.sodium.php
	#
	# Flamework uses these functions for encrypting/decrypting login cookies, preventing tampering
	# and easy user impersonation.
	#
	# This also means that if you ran Flamework prior to the PHP 7+ upgrade, your cookies won't work,
	# and all users will be logged-out on next visit until they login again. This might not be so bad.
	# If it is, or if you used these functions for other uses that don't have easy recovery, you'll
	# probably want to explore re-implementing them using `openssl_encrypt()` etc, which can be done
	# but isn't recommended. It should be possible to detect and upgrade these over time or in batch.
	#
	# Thanks to @thisisaaronland for the research behind this.
	#
	# To use this library you will need to set `crypto_libsodium_nonce` in `config.php` to a 24-byte
	# random value (and never lose it)
	# TODO: I think this is supposed to be chosen per-encrypted value and stored for decryption. Like
	# in a session row
	#

	#################################################################

	#
	# At runtime, ensure we have crypto configured correctly
	#

	if (strlen($GLOBALS['cfg']['crypto_libsodium_nonce']) != SODIUM_CRYPTO_SECRETBOX_NONCEBYTES){
		log_fatal("Invalid libsodium nonce");
	}

	#################################################################

	#
	# Given a plaintext and key, encrypt it. You'll need the key to decrypt it
	# Returns the encrypted data as base64-encoded
	#

	function crypto_encrypt($plaintext, $key){
		$out = sodium_crypto_secretbox($plaintext, $GLOBALS['cfg']['crypto_libsodium_nonce'], $key);

		return base64_encode($out);
	}

	#################################################################

	#
	# Given base64-encoded encrypted data and a key, decrypt it.
	#

	function crypto_decrypt($enc_b64, $key){
		$enc = base64_decode($enc_b64);

		return sodium_crypto_secretbox_open($enc, $GLOBALS['cfg']['crypto_libsodium_nonce'], $key);
	}

	#################################################################

