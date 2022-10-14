<?php
	include(dirname(__FILE__).'/wrapper.php');

	plan(5);

	$password = 'iloveth';
	$enc_password = passwords_encrypt_password($password);
	isnt($password, $enc_password, "Password got encrypted, or at least scrambled");

	is(passwords_validate_password($password, $enc_password), true, "Encrypted password validates");
	is(passwords_validate_password($password, $password), false, "Plaintext password doesn't validate");
	is(passwords_validate_password('', ''), false, "Empty password doesn't validate");

	$ret = tests_helpers_create_user();
	$test_user = $ret['user'];
	$test_user_data = $ret['data'];

	$is_password_ok = passwords_validate_password_for_user($test_user_data['password'], $test_user);
	is($is_password_ok, true, "Test user's password validates");
	# TODO: Test password promotion?