<?php

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

		$user['password'] = passwords_encrypt_password($user['password']);
		$user['created'] = time();
		$user['conf_code'] = random_string(24);

		$user['cluster_id'] = users_assign_cluster_id();


		#
		# now create the escaped version
		#

		$hash = array();
		foreach ($user as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$ret = db_insert('users', $hash);

		if (!$ret['ok']) return $ret;


		#
		# cache the unescaped version
		#

		$user['id'] = $ret['insert_id'];

		cache_set("USER-{$user['id']}", $user);

		return array(
			'ok'	=> 1,
			'user'	=> $user,
		);
	}

	#################################################################

	#
	# update multiple fields on an user record. the hash passed
	# in $update IS NOT ESCAPED.
	#

	function users_update_user(&$user, $update){

		$hash = array();
		foreach ($update as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$ret = db_update('users', $hash, "id={$user['id']}");

		if (!$ret['ok']) return $ret;

		cache_unset("USER-{$user['id']}");

		return array(
			'ok' => 1,
		);
	}

	#################################################################

	function users_update_password(&$user, $new_password){

		$enc_password = passwords_encrypt_password($new_password);

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

		$user = db_single(db_fetch("SELECT * FROM users WHERE id=".intval($id)));

		cache_set("USER-{$user['id']}", $user);

		return $user;
	}

	#################################################################

	function users_get_by_email($email){

		$enc_email = AddSlashes($email);

		return db_single(db_fetch("SELECT * FROM users WHERE email='{$enc_email}'"));
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

		if (! passwords_validate_password_for_user($password, $user)){
			return null;
		}

		return $user;
	}

	#################################################################

	function users_is_email_taken($email){

		$enc_email = AddSlashes($email);

		$row = db_single(db_fetch("SELECT id FROM users WHERE email='{$enc_email}' AND deleted=0"));

		return $row['id'] ? 1 : 0;
	}

	#################################################################

	function users_is_username_taken($username){

		$enc_username = AddSlashes($username);

		$row = db_single(db_fetch("SELECT id FROM users WHERE username='{$enc_username}' AND deleted=0"));
		return $row['id'] ? 1 : 0;
	}

	#################################################################

	function users_get_by_password_reset_code($code){

		$enc_code = AddSlashes($code);

		$row = db_single(db_fetch("SELECT * FROM users_password_reset WHERE reset_code='{$enc_code}'"));

		if (!$row){
			return null;
		}

		return users_get_by_id($row['user_id']);
	}

	#################################################################

	function users_purge_password_reset_codes(&$user){

		$rsp = db_write("DELETE FROM users_password_reset WHERE user_id=$user[id]");

		return $rsp['ok'];
	}

	#################################################################

	function users_send_password_reset_code(&$user){

		$code = users_generate_password_reset_code($user);
		if (!$code) return 0;

		$GLOBALS['smarty']->assign('code', $code);

		email_send(array(
			'to_email'	=> $user['email'],
			'template'	=> 'email_password_reset.txt',
		));

		return 1;
	}

	#################################################################

	function users_generate_password_reset_code(&$user){

		loadlib('random');

		users_purge_password_reset_codes($user);

		$code = '';

		while (!$code){

			$code = random_string(32);
			$enc_code = AddSlashes($code);

			if (db_single(db_fetch("SELECT 1 FROM users_password_reset WHERE reset_code='{$enc_code}'"))){
				$code = '';
			}

			break;
		}

		$rsp = db_insert('users_password_reset', array(
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

	function users_assign_cluster_id(){

		if ($GLOBALS['cfg']['db_enable_poormans_federation']){
			return 1;
		}

		# TO DO: an actual cluster ID if federated

		return 1;
	}

	#################################################################
