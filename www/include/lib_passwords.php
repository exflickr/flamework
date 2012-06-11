<?php

	$GLOBALS['passwords_canhas_bcrypt'] = 0;

	if (CRYPTO_BLOWFISH){
		$GLOBALS['passwords_canhas_bcrypt'] = 1;
		loadlib("bcrypt");
	}

	#################################################################

	function passwords_encrypt_password($password){

		if ($GLOBALS['passwords_canhas_bcrypt']){
			$h = new BCryptHasher();
			return $h->HashPassword($password);
		}

		return hash_hmac("sha256", $password, $GLOBALS['cfg']['crypto_password_secret']);
	}

	#################################################################

	function passwords_validate_password($password, $enc_password){

		if ($GLOBALS['passwords_canhas_bcrypt']){
			$h = new BCryptHasher();
			return $h->CheckPassword($password, $enc_password)
		}

		$test = passwords_encrypt_password($password);

		$len_test = strlen($test);
		$len_pswd = strlen($enc_password);

		if ($len_test != $len_pswd){
			return 0;
		}

		for ($i=0; $i < $len_test; $i++){

			if ($test[$i] != $enc_password[$i]){
				return 0;
			}
		}

		return 1;
	}

	#################################################################
?>
