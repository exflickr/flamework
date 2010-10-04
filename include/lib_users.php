<?php

	#
	# $Id$
	#

	#######################################################################

	loadlib('users_email');

	#######################################################################

	function users_create_user(&$acct){

		foreach ($acct as $k => $v){
			$acct[$k] = db_quote($v);
		}

		$enc_pass = login_encrypt_password($password);

		$acct['password'] = db_quote($enc_pass);
		$acct['created'] = time();

		$rsp = db_insert('Users', $acct);
			
		if (! $rsp['ok']){
			return null;
		}
		
		$acct['user_id'] = $rsp['insert_id'];

		# do something with $conf_code here...

		$is_primary = 1;
		$conf_code = users_email_add_address($acct, $email, $is_primary);

		$acct['conf_code'] = $conf_code;
		return $acct;
	}

	#######################################################################

	function users_delete_user(&$acct){

		$enc_id = db_quote($acct['user_id']);

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

	#######################################################################

	function users_get_by_id($id){

		$enc_id = db_quote($id);
		$sql = "SELECT * FROM Users WHERE user_id='{$enc_id}'";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#######################################################################

	function users_get_by_email($email){

		$enc_email = db_quote($email);
		$sql = "SELECT * FROM Users WHERE email='{$enc_email}'";

		$rsp = db_fetch($sql);
		return db_single($rsp);
	}

	#######################################################################

	function users_get_by_login($email, $password){

		$acct = users_get_by_email($email);

		if (! $acct){
			return null;
		}

		if ($acct['deleted']){
			return null;
		}

		if ($acct['password'] != login_encrypt_password($password)){
			return null;
		}

		return $acct;
	}

	#######################################################################
?>