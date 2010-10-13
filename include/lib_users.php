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

		$user['conf_code'] = random_string(24);

		$rsp = db_insert('Users', $user);
			
		if (! $rsp['ok']){
			return null;
		}
		
		$user['user_id'] = $rsp['insert_id'];

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

		$enc_password = login_encrypt_password($new_password);

		$update = array(
			'password' => db_quote($enc_password),
		);

		return users_update_user($user, $update);
	}

	#################################################################

	function users_delete_user(&$user){

		$now = time();

		$email = $user['email'] . '.DELETED';

		$update = array(
			'deleted' => $now,
			'email' => $email,

			# reset the password here ?
		);

		return users_update_user($user, $update);
	}

	#################################################################

	function users_reload_user(&$user){
		$user = users_get_by_id($user['user_id']);
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

	function users_get_by_password_reset_code($code){

		$enc_code = db_quote($code);

		$sql = "SELECT * FROM UsersPasswordReset WHERE reset_code = '{$enc_code}'";

		$rsp = db_fetch($sql);
		$row = db_single($rsp);

		if (! $row){
			return null;
		}

		return users_get_by_id($row['user_id']);
	}

	#################################################################

	function users_purge_password_reset_codes(&$user){

		$enc_user_id = db_quote($user['user_id']);

		$sql = "DELETE FROM UsersPasswordReset WHERE user_id={$enc_user_id}";
		$rsp = db_write($sql);

		return $rsp['ok'];
	}

	#################################################################

	function users_generate_password_reset_code(&$user){

		users_purge_password_reset_codes($user);

		$enc_user_id = db_quote($user['user_id']);

		$code = '';

		while (! $code){

			$code = random_string(32);
			$enc_code = db_quote($code);

			$sql = "SELECT 1 FROM UsersPasswordReset WHERE reset_code='{$enc_code}'";
			$rsp = db_fetch($sql);

			if (db_single($rsp)){
				$code = '';
			}

			break;
		}

		$insert = array(
			'user_id' => $enc_user_id,
			'reset_code' => $enc_code,
			'created' => time(),
		);

		$rsp = db_insert('UsersPasswordReset', $insert);
			
		if (! $rsp['ok']){
			return null;
		}

		return $code;
	}

	#################################################################
?>