<?
	#
	# $Id$
	#

	include('include/init.php');

	$GLOBALS['smarty']->assign('nav_tab', 'about');

	#
	# output
	#

	$smarty->display('page_about.txt');
?>
