<?
<<<<<<< HEAD

	#
	# $Id$
	#

	$GLOBALS['cfg'] = array();

	#
	# Things you'll certainly need to tweak
	#

=======
	$GLOBALS['cfg'] = array();

>>>>>>> 7f3ca545bc4a9243e7b3c8849b4c8f1138928c38
	$GLOBALS['cfg']['db_main'] = array(
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'name'	=> 'flamework',
		'auto_connect' => 0,
	);

	$GLOBALS['cfg']['db_users'] = array(
		'host' => array(
			1 => 'localhost',
			2 => 'localhost',
		),
		'user' => 'root',
		'pass' => 'root',
		'name' => array(
			1 => 'user1',
			2 => 'user2',
		),
	);


	$GLOBALS['cfg']['abs_root_url']		= 'http://www.ourapp.com/';
	$GLOBALS['cfg']['safe_abs_root_url']	= $GLOBALS['cfg']['abs_root_url'];
<<<<<<< HEAD

	$GLOBALS['cfg']['smarty_template_dir'] = dirname(__FILE__) . '/../templates/';
	$GLOBALS['cfg']['smarty_compile_dir'] = dirname(__FILE__) . '/../templates_c/';

	# No, seriously. Change this...

	$GLOBALS['cfg']['crypto_secret'] = rand(time(), time() * rand(2, 10));

	#
	# Things you may need to tweak
	#

	$GLOBALS['cfg']['auth_cookie_domain'] = parse_url($GLOBALS['cfg']['abs_root_url'], 1);
	$GLOBALS['cfg']['auth_cookie_name'] = 'a';

	#
	# Things you can probably not worry about
	#

	$GLOBALS['cfg']['user'] = array();
	$GLOBALS['cfg']['user_ok'] = 0;

	$GLOBALS['cfg']['smarty_compile'] = 1;
	$GLOBALS['cfg']['http_timeout'] = 3;

?>
=======

	$GLOBALS['cfg']['smarty_compile'] = 1;
	$GLOBALS['cfg']['check_notices'] = 1;

	$GLOBALS['cfg']['http_timeout'] = 3;
?>
>>>>>>> 7f3ca545bc4a9243e7b3c8849b4c8f1138928c38
