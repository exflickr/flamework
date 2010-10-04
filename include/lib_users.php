<?php

	#
	# $Id$
	#

	loadlib('users_email');

	function users_create_user(&$user){

		foreach ($user as $k => $v){
			$user[$k] = db_quote($v);
		}

		$enc_pass = login_encrypt_password($password);

		$user['password'] = db_quote($enc_pass);
		$user['created'] = time();

		$rsp = db_insert('Users', $user);
			
		if (! $rsp['ok']){
			return null;
		}
		
		$user['user_id'] = $rsp['insert_id'];

		# do something with $conf_code here...

		$is_primary = 1;
		$conf_code = users_email_add_address($user, $email, $is_primary);

		$user['conf_code'] = $conf_code;
		return $user;
	}

	function users_delete_user(&$user){

		$enc_id = db_quote($user['user_id']);

		# UsersEmail

		$update = array(
			'deleted' => time(),
		);

		$where = array(
			'user_id' => $enc_id,
		);

		$rsp = db_update('Users', $update, $where);

		if (! $rsp['ok']){
			return null;
		}

		return 1;
	}

	function users_get_by_id($id){

		$enc_id = db_quote($id);
		$sql = "SELECT * FROM Users WHERE user_id='{$enc_id}'";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	function users_get_by_email($email){

		$enc_email = db_quote($email);
		$sql = "SELECT * FROM Users WHERE email='{$enc_email}'";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

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
?>