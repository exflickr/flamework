<?
	$GLOBALS[cfg] = array();

	#
	# Things you'll certainly need to tweak
	#

	$GLOBALS[cfg][db_main] = array(
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'name'	=> 'flamework',
		'auto_connect' => 0,
	);

	$GLOBALS[cfg][db_users] = array(
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


	$GLOBALS[cfg][abs_root_url]		= 'http://www.ourapp.com/';
	$GLOBALS[cfg][safe_abs_root_url]	= $GLOBALS[cfg][abs_root_url];

	$GLOBALS[cfg][smarty_template_dir] = FLAMEWORK_INCLUDE_DIR.'/../templates/';
	$GLOBALS[cfg][smarty_compile_dir] = FLAMEWORK_INCLUDE_DIR.'/../templates_c/';

	#
	# Things you may need to tweak
	#

	$GLOBALS[cfg][auth_cookie_domain] = parse_url($GLOBALS[cfg][abs_root_url], 1);
	$GLOBALS[cfg][auth_cookie_name] = 'a';

	#
	# Things you can probably not worry about
	#

	$GLOBALS[cfg][user] = array();
	$GLOBALS[cfg][user_ok] = 0;

	$GLOBALS[cfg][smarty_compile] = 1;
	$GLOBALS[cfg][http_timeout] = 3;

?>