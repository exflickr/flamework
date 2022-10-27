<?php
	include(dirname(__FILE__).'/wrapper.php');

	plan(27);


	#
	# User creation tests
	# NB: These currently don't work well without dropping and re-creating the DB
	#

	$username = 'static-test-user';
	$data = array(
		'username'	=> $username,
		'email'		=> "{$username}@example.com",
		'password'	=> 'iloveth',
	);
	$test_user_ret = users_create_user($data);
	is($test_user_ret['ok'], 1, "users_create_user user was created ok");
	is($test_user_ret['user']['username'], $data['username'], "users_create_user user has expected username");
	isnt($test_user_ret['user']['password'], $data['password'], "users_create_user user has encrypted password");
	isnt($test_user_ret['user']['id'], 0, "users_create_user user has a user ID");

	$random_test_user_ret = tests_helpers_create_user();
	is($random_test_user_ret['ok'], 1, "tests_helpers_create_user user was created ok");
	$random_test_user = $random_test_user_ret['user'];
	$random_test_user_original_email = $random_test_user['email'];


	#
	# User edit/update tests
	#

	$random_test_user_new_email = "{$random_test_user['username']}@test.example.com";
	$edit_ret = users_update_user($random_test_user, array(
		'email'	=> $random_test_user_new_email,
	));
	is($edit_ret['ok'], 1, "Updating a user returns ok");

	$random_test_user_new_password = 'iloveth';
	$password_edit_ret = users_update_password($random_test_user, $random_test_user_new_password);
	is($password_edit_ret['ok'], 1, "Editing a user's password returns ok");


	#
	# User fetch tests
	#

	$test_user_refetch_by_id = users_get_by_id($test_user_ret['user']['id']);
	is($test_user_refetch_by_id['id'], $test_user_ret['user']['id'], "users_get_by_id returns the same user id");
	is($test_user_refetch_by_id['username'], $test_user_ret['user']['username'], "users_get_by_id returns the same username");

	$test_user_refetch_by_email = users_get_by_email($test_user_ret['user']['email']);
	is($test_user_refetch_by_email['id'], $test_user_ret['user']['id'], "users_get_by_email returns the same user id");
	is($test_user_refetch_by_email['username'], $test_user_ret['user']['username'], "users_get_by_email returns the same username");

	$test_user_refetch_by_login = users_get_by_login($test_user_ret['user']['email'], $data['password']);
	is($test_user_refetch_by_login['id'], $test_user_ret['user']['id'], "users_get_by_login returns the same user id");
	is($test_user_refetch_by_login['username'], $test_user_ret['user']['username'], "users_get_by_login returns the same username");

	$random_test_user_refetch_by_email = users_get_by_email($random_test_user_new_email);
	is($random_test_user_refetch_by_email['id'], $random_test_user['id'], "users_get_by_email returns the user after an email change");

	$random_test_user_refetch_by_email = users_get_by_email($random_test_user_original_email);
	isnt($random_test_user_refetch_by_email['id'], $random_test_user['id'], "users_get_by_email does not return the user with their old email");

	$random_test_user_refetch_by_login = users_get_by_login($random_test_user_new_email, $random_test_user_new_password);
	is($random_test_user_refetch_by_login['id'], $random_test_user['id'], "users_get_by_login returns the user after a password change");


	#
	# User exists tests
	#

	is(users_is_email_taken($random_test_user_new_email), true, "users_is_email_taken returns true for an email that's taken");
	is(users_is_email_taken($random_test_user_original_email), false, "users_is_email_taken returns false for an email that's no longer taken");

	is(users_is_username_taken($username), true, "users_is_username_taken returns true for a username that's taken");
	is(users_is_username_taken($username . '-fake'), false, "users_is_username_taken returns false for a username that's no longer taken");
	

	#
	# User delete tests
	#

	$delete_ret = users_delete_user($random_test_user);
	is($delete_ret['ok'], 1, "Deleting a user returns ok");

	$random_test_user_refetch_by_id = users_get_by_id($random_test_user['id']);
	isnt($random_test_user_refetch_by_id['deleted'], 0, "After deletion, the user has a date deleted set");

	$random_test_user_refetch_by_login = users_get_by_login($random_test_user_new_email, $random_test_user_new_password);
	isnt($random_test_user_refetch_by_login['id'], $random_test_user['id'], "users_get_by_login does not return the user after they've been deleted");

	$random_test_user_refetch_by_login = users_get_by_login($random_test_user_new_email . '.DELETED', $random_test_user_new_password);
	isnt($random_test_user_refetch_by_login['id'], $random_test_user['id'], "users_get_by_login does not return the user after they've been deleted even with the right email address");


	#
	# Password reset code tests
	#

	$password_reset_code = users_generate_password_reset_code($test_user_ret['user']);
	$test_user_refetch_by_password_reset_code = users_get_by_password_reset_code($password_reset_code);
	is($test_user_refetch_by_password_reset_code['id'], $test_user_ret['user']['id'], "users_get_by_password_reset_code returns the correct user");

	is(users_purge_password_reset_codes($test_user_ret['user']), 1, "users_purge_password_reset_codes returns ok");
	$test_user_refetch_by_password_reset_code = users_get_by_password_reset_code($password_reset_code);
	isnt($test_user_refetch_by_password_reset_code['id'], $test_user_ret['user']['id'], "users_get_by_password_reset_code does not return the user after purging codes");


	#
	# Misc other tests
	#