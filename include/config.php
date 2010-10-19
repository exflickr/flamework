<?

	#
	# $Id$
	#

	$GLOBALS['cfg'] = array();

	#
	# Things you'll certainly need to tweak
	#

	$GLOBALS['cfg'] = array();

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

	$GLOBALS['cfg']['smarty_template_dir'] = dirname(__FILE__) . '/../templates/';
	$GLOBALS['cfg']['smarty_compile_dir'] = dirname(__FILE__) . '/../templates_c/';


	# No, seriously. Change these...

	$GLOBALS['cfg']['crypto_cookie_secret'] = '';
	$GLOBALS['cfg']['crypto_password_secret'] = '';
	$GLOBALS['cfg']['crypto_crumb_secret'] = '';

	#
	# Things you may need to tweak
	#

	$GLOBALS['cfg']['auth_cookie_domain'] = parse_url($GLOBALS['cfg']['abs_root_url'], 1);
	$GLOBALS['cfg']['auth_cookie_name'] = 'a';

	$GLOBALS['cfg']['crumb_ttl_default'] = 300;	# seconds

	$GLOBALS['cfg']['rewrite_static_urls'] = array(
		# '/foo' => '/bar/',
	);

	#
	# Things you can probably not worry about
	#

	$GLOBALS['cfg']['user'] = null;
	$GLOBALS['cfg']['user_ok'] = 0;

	$GLOBALS['cfg']['smarty_compile'] = 1;

	$GLOBALS['cfg']['http_timeout'] = 3;

	$GLOBALS['cfg']['check_notices'] = 1;

	$GLOBALS['cfg']['db_profiling'] = 0;
?>
