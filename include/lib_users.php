<?php

	#
	# $Id$
	#

	#################################################################

	$GLOBALS['users_local_cache'] = array();

	#################################################################

	#
	# create a user record. the fields pass in $user
	# ARE NOT ESCAPED.
	#

	function users_create_user($user){

		#
		# set up some extra fields first
		#

		loadlib('random');

		$user['password'] = login_encrypt_password($user['password']);
		$user['created'] = time();
		$user['conf_code'] = random_string(24);


		#
		# now create the escaped version
		#

		$hash = array();
		foreach ($user as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$rsp = db_insert('Users', $hash);

		if (!$rsp['ok']){
			return null;
		}


		#
		# cache the unescaped version
		#

		$user['id'] = $rsp['insert_id'];

		$GLOBALS['user_local_cache'][$user['id']] = $user;
		return $user;
	}

	#################################################################

	#
	# update multiple fields on an user record. the hash passed
	# in $update IS NOT ESCAPED.
	#

	function users_update_user(&$user, $update){

		foreach ($update as $k => $v){
			$update[$k] = AddSlashes($v);
		}

		$rsp = db_update('Users', $update, "id=$user[id]");

		if (!$rsp['ok']){
			return null;
		}

		unset($GLOBALS['user_local_cache'][$user['id']]);
		return 1;
	}

	#################################################################

	function users_update_password(&$user, $new_password){

		$enc_password = login_encrypt_password($new_password);

		return users_update_user($user, array(
			'password' => AddSlashes($enc_password),
		));
	}

	#################################################################

	function users_delete_user(&$user){

		return users_update_user($user, array(
			'deleted'	=> time(),
			'email'		=> $user['email'] . '.DELETED',

			# reset the password here ?
		));
	}

	#################################################################

	function users_reload_user(&$user){

		$user = users_get_by_id($user['id']);
	}

	#################################################################

	function users_get_by_id($id){

		$user = db_single(db_fetch("SELECT * FROM Users WHERE id=".intval($id)));

		$GLOBALS['user_local_cache'][$id] = $user;

		return $user;
	}

	#################################################################

	function users_get_by_email($email){

		$enc_email = AddSlashes($email);

		return db_single(db_fetch("SELECT * FROM Users WHERE email='{$enc_email}'"));
	}

	#################################################################

	function users_get_by_login($email, $password){

		$user = users_get_by_email($email);

		if (!$user){
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

		$enc_email = AddSlashes($email);

		return db_single(db_fetch("SELECT id FROM Users WHERE email='{$enc_email}' AND deleted != 0"));
	}

	#################################################################

	function users_is_username_taken($username){

		$enc_username = AddSlashes($username);

		return db_single(db_fetch("SELECT id FROM Users WHERE username='{$enc_username}' AND deleted != 0"));
	}

	#################################################################

	function users_get_by_password_reset_code($code){

		$enc_code = AddSlashes($code);

		$row = db_single(db_fetch("SELECT * FROM UsersPasswordReset WHERE reset_code='{$enc_code}'"));

		if (!$row){
			return null;
		}

		return users_get_by_id($row['user_id']);
	}

	#################################################################

	function users_purge_password_reset_codes(&$user){

		$rsp = db_write("DELETE FROM UsersPasswordReset WHERE user_id=$user[id]");

		return $rsp['ok'];
	}

	#################################################################

	function users_generate_password_reset_code(&$user){

		users_purge_password_reset_codes($user);

		$code = '';

		while (!$code){

			$code = random_string(32);
			$enc_code = AddSlashes($code);

			if (db_single(db_fetch("SELECT 1 FROM UsersPasswordReset WHERE reset_code='{$enc_code}'"))){
				$code = '';
			}

			break;
		}

		$rsp = db_insert('UsersPasswordReset', array(
			'user_id'	=> $user['id'],
			'reset_code'	=> $enc_code,
			'created'	=> time(),
		));

		if (!$rsp['ok']){
			return null;
		}

		return $code;
	}

	#################################################################
?>
