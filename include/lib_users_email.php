<?php

	#
	# $Id$
	#

	#######################################################################

	loadlib('random');

	#######################################################################

	function users_email_add_address(&$user, $email, $primary=0){

		loadlib("string");
		$conf_code = random_string(64);

		$insert = array(
			'user_id' => $user['id'],
			'address' => $email,
			'conf_code' => $conf_code,
			'is_primary' => $primary,
			'created' => time(),
		);

		$rsp = db_insert('UsersEmail', $insert);
			
		if (! $rsp['ok']){
			return null;
		}

		return $conf_code;
	}

	#######################################################################

	function users_email_confirm_address(){
		# sudo write me...
	}

	#######################################################################

	function users_email_flag_addresses_for_user(&$user){

		$enc_user_id = db_quote($user['user_id']);

		$sql = "SELECT address FROM UsersEmail WHERE user_id='$enc_user_id'";
		$rsp = db_fetch($sql);

		if (! $rsp['ok']){
			return;
		}

		foreach ($rsp['rows'] as $row){
			users_email_flag_address($row['address']);
		}

		return 1;
	}

	#######################################################################

	function users_email_flag_address($email){

		loadlib("string");

		$rand = random_string(18);
		$new_email = "{$email}_DELETED:{$rand}";

		$update = array(
			'address' => $new_email,
		);

		$where = array(
			'address' => $email,
		);

		$rsp = db_update('UsersEmail', $update, $where);

		if (! $rsp['ok']){
			return null;
		}

		return 1;		
	}

	#######################################################################

?>