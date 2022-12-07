<?php
	include(dirname(__FILE__).'/wrapper.php');

	loadlib('random');

	plan(8);

	$data1 = '1234:iloveth'; # Simple model of what we encrypt in lib_login

	#
	# Basic tests with random keys
	#

	$random_key1 = sodium_crypto_secretbox_keygen(); # Random key 1
	$random_key2 = sodium_crypto_secretbox_keygen(); # Different random key 2

	$encrypted1 = crypto_encrypt($data1, $random_key1);
	isnt($encrypted1, $data1, "Encrypted output isn't the same as the input");
	isnt($encrypted1, '', "Encrypted output isn't blank");

	$encrypted2 = crypto_encrypt($data1, $random_key2);
	isnt($encrypted2, $encrypted1, "Different keys produce different encrypted output");

	$decrypted1 = crypto_decrypt($encrypted1, $random_key1);
	$decrypted2 = crypto_decrypt($encrypted2, $random_key2);
	$decrypted3 = crypto_decrypt($encrypted2, $random_key1);

	is($decrypted1, $data1, "Decrypted data matches with random_key1");
	is($decrypted2, $data1, "Decrypted data matches with random_key2");
	isnt($decrypted3, $data1, "Decrypting with the wrong key doesn't work");


	#
	# Basic tests with a static key
	#

	$static_key1 = "rD4ewpmJab0h2rO7AGpXBHef68pd32KD"; # A static key so we can test we get the same thing through crypto upgrades
	$encrypted3 = crypto_encrypt($data1, $static_key1);
	is($encrypted3, "LCssQKd+ANypkmhD1LLMX8hB9XJBxcB6uHHlXw==", "Encrypted output with static key matches");

	$decrypted4 = crypto_decrypt($encrypted3, $static_key1);
	is($decrypted4, $data1, "Decrypted data matches with static_key1");


	#
	# Unexpected keys
	#

	#$encrypted4 = crypto_encrypt($data1, '');
	#isnt($encrypted4, '', "Using a blank key shouldn't produce anything");