<?php

	#################################################################

	#
	# Creates a basic user for use in tests
	# Most of the data is randomly generated so you can use it over and over without collisions
	#

	function tests_helpers_create_user(){
		loadlib('random');
	
		$username = 'testuser-'.rand(0, 999);
		$data = array(
			'username'	=> $username,
			'email'		=> "{$username}@example.com",
			'password'	=> random_string(32),
		);

		$ret = users_create_user($data);
		if (!$ret['ok']) return array();

		return array(
			'ok'	=> 1,
			'data'	=> $data,
			'user'	=> $ret['user'],
		);
	}

	#################################################################