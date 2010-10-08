<?php

	#
	# $Id$
	#

	#################################################################

	loadlib('users_email');

	#################################################################

	function users_create_user(&$user){

		$enc_pass = login_encrypt_password($user['password']);

		foreach ($user as $k => $v){
			$user[$k] = db_quote($v);
		}

		$user['password'] = db_quote($enc_pass);
		$user['created'] = time();

		$rsp = db_insert('Users', $user);
			
		if (! $rsp['ok']){
			return null;
		}
		
		$user['user_id'] = $rsp['insert_id'];

		# do something with $conf_code here...

		$is_primary = 1;
		$conf_code = users_email_add_address($user, $user['email'], $is_primary);

		$user['conf_code'] = $conf_code;
		return $user;
	}

	#################################################################

	function users_update_user(&$user, &$update){

		$enc_id = db_quote($user['user_id']);
		$where = "user_id='{$enc_id}'";

		foreach ($update as $k => $v){
			$update[$k] = db_quote($v);
		}

		$rsp = db_update('Users', $update, $where);

		if (! $rsp['ok']){
			return null;
		}

		return 1;
	}

	#################################################################

	function users_update_password(&$user, $new_password){

		$update = array(
			'password' => login_encrypt_password($new_password),
		);

		return users_update_user($user, $update);
	}

	#################################################################

	function users_delete_user(&$user){

		$now = time();

		$update = array(
			'deleted' => $now,
			# reset the password here ?
		);

		return users_update_user($user, $update);
	}

	#################################################################

	function users_get_by_id($id){

		$enc_id = db_quote($id);
		$sql = "SELECT * FROM Users WHERE user_id='{$enc_id}'";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#################################################################

	function users_get_by_email($email){

		$enc_email = db_quote($email);
		$sql = "SELECT * FROM Users WHERE email='{$enc_email}'";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#################################################################

	function users_get_by_login($email, $password){

		$user = users_get_by_email($email);

		if (! $user){
			return null;
		}

		if ($user['deleted']){
			return null;
		}

		if ($user['password'] != login_encrypt_password($password)){
			return null;
		}

		return $user;
	}

	#################################################################

	function users_is_email_taken($email){

		$enc_email = db_quote($email);

		$sql = "SELECT user_id FROM Users WHERE email='{$enc_email}' AND deleted != 0";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#################################################################

	function users_is_username_taken($username){

		$enc_username = db_quote($username);

		$sql = "SELECT user_id FROM Users WHERE username='{$enc_username}' AND deleted != 0";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#################################################################

?>