<?php
	include('include/init.php');


	#
	# are we submitting the form?
	#

	if (post_str('contact')){

		$ok = 1;

		$name		= post_str('name');
		$email		= post_str('email');
		$message	= post_str_multi('message');

		$smarty->assign('name', $name);
		$smarty->assign('email', $email);
		$smarty->assign('message', $message);


		#
		# all fields are in order?
		#

		if ((!strlen($name)) || (!strlen($email)) || (!strlen($message))){

			$smarty->assign('error_missing', 1);
			$ok = 0;
		}


		#
		# send it
		#

		if ($ok){

			loadlib('email');

			email_send(array(
				'from_name'	=> $name,
				'from_email'	=> $email,
				'to_email'	=> $GLOBALS['cfg']['email_from_email'],
				'template'	=> 'email_contact.txt',
			));

			header('location: /contact/?sent=1');
			exit;
		}
	}

	#
	# output
	#

	$smarty->display('page_contact.txt');
