<?php

	$GLOBALS['passwords_canhas_bcrypt'] = 0;

	if (CRYPT_BLOWFISH){
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
			return $h->CheckPassword($password, $enc_password);
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

	# Basically a helper function to save a tiny amount of typing but
	# mostly to make it easier to ensure that user passwords are encrypted
	# using the "safe thing", which is currently bcrypt (20120611/straup)

	function passwords_validate_password_for_user($password, &$user, $more=array()){

		$defaults = array(
			'ensure_bcrypt' => 1,
		);

		$more = array_merge($defaults, $more);

		$enc_password = $user['password'];

		$is_bcrypt = (substr($enc_password, 0, 4) == '$2a$') ? 1 : 0;

		$is_ok = passwords_validate_password($password, $enc_password);

		if (($is_ok) && (! $is_bcrypt) && ($more['ensure_bcrypt']) && ($GLOBALS['passwords_canhas_bcrypt'])){
			users_update_password($user, $password);
		}

		return $is_ok;
	}

	#################################################################
?>
