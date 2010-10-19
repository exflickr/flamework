<?php

	#
	# $Id$
	#

	include("include/init.php");

	if (! login_is_loggedin()){

		if (get_isset('signedout')){

			$smarty->assign('signedout', 1);
			$smarty->display('page_signout.txt');
			exit();
		}

		header("location: /");
		exit();
	}

	if (post_str('signout')){

		$crumb = post_str('crumb');

		if (! crumb_validate_crumb($crumb, $GLOBALS['cfg']['user'])){

			$GLOBALS['error']['badcrumb'] = 1;
			$smarty->display("page_signout.txt");
			exit();
		}

		login_do_logout('/signout?signedout=1');
	}

	$new_crumb = crumb_generate_crumb($GLOBALS['cfg']['user']);
	$smarty->assign("crumb", $new_crumb);

	$smarty->display("page_signout.txt");
	exit();
?>