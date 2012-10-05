<?
	#
	# $Id$
	#

	include('include/init.php');

	$GLOBALS['cfg']['nav_tab'] = 'about'; // for the navbar

	#
	# output
	#

	$smarty->display('page_about.txt');
?>
