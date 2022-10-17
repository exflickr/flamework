<?php
	include(dirname(__FILE__) . '/wrapper.php');

	plan(4);


	#
	# Setup
	# TODO: We need to override `setcookie` to do more here
	#

	$random_test_user_ret = tests_helpers_create_user();
	is($random_test_user_ret['ok'], 1, "tests_helpers_create_user user was created ok");
	$random_test_user = $random_test_user_ret['user'];


	#
	# User is logged in tests
	#

	is(login_check_login(), 0, "No user is logged in by default");


	#
	# Cookie tests
	#

	$cookie_name = 'test-cookie';
	is(login_get_cookie($cookie_name), '', "There is no cookie before set");

	$cookie_value = 'foobarbaz';
	login_set_cookie($cookie_name, $cookie_value);
	#is(login_get_cookie($cookie_name), $cookie_value, "There is a cookie value after being set");

	login_unset_cookie($cookie_name);
	is(login_get_cookie($cookie_name), '', "There is no cookie after being unset");
