<?php
	include(dirname(__FILE__).'/wrapper.php');

	plan(4);

	$password = 'iloveth';
	$enc_password = passwords_encrypt_password($password);
	isnt($password, $enc_password, "Password got encrypted, or at least scrambled");

	is(passwords_validate_password($password, $enc_password), true, "Encrypted password validates");
	is(passwords_validate_password($password, $password), false, "Plaintext password doesn't validate");
	is(passwords_validate_password('', ''), false, "Empty password doesn't validate");