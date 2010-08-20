<?
	#
	# $Id$
	#

	define('SMARTY_DIR', INCLUDE_DIR.'/smarty-2.6.19/');

	require(SMARTY_DIR . 'Smarty.class.php');

	$GLOBALS[smarty] = new Smarty();

	$GLOBALS[smarty]->template_dir = INCLUDE_DIR.'/../templates/';
	$GLOBALS[smarty]->compile_dir  = INCLUDE_DIR.'/../templates_c/';
	$GLOBALS[smarty]->compile_check = $GLOBALS[cfg][smarty_compile];
	$GLOBALS[smarty]->force_compile = $GLOBALS[cfg][smarty_compile];

	$GLOBALS[smarty]->assign_by_ref('cfg', $GLOBALS[cfg]);
?>