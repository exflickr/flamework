<?
	#
	# $Id$
	#

	include("include/init.php");


	#
	# are we already signed out?
	#

	if (!login_is_loggedin()){

		$smarty->display('page_signout_done.txt');
		exit;
	}


	#
	# crumb key
	#

	$crumb_key = 'logout';
	$smarty->assign("crumb_key", $crumb_key);


	#
	# sign out?
	#

	if (post_isset('done') && crumb_check($crumb_key)){

		login_do_logout('/signout/?signedout=1');
	}


	#
	# output
	#

	$smarty->display("page_signout.txt");
?>