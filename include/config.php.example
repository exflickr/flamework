<?
	#
	# $Id$
	#

	$GLOBALS['cfg'] = array();

	$GLOBALS['cfg']['site_disabled'] = 0;
	$GLOBALS['cfg']['site_disabled_retry_after'] = 0;	# seconds; if set will return HTTP Retry-After header

	#
	# Things you'll certainly need to tweak
	#

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

	$GLOBALS['cfg']['abs_root_url']		= 'http://'.($_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'fake.com').'/';
	$GLOBALS['cfg']['safe_abs_root_url']	= $GLOBALS['cfg']['abs_root_url'];

	$GLOBALS['cfg']['smarty_template_dir'] = realpath(dirname(__FILE__) . '/../templates/');
	$GLOBALS['cfg']['smarty_compile_dir'] = realpath(dirname(__FILE__) . '/../templates_c/');


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

	$GLOBALS['cfg']['email_from_name']	= 'flamework app';
	$GLOBALS['cfg']['email_from_email']	= 'admin@ourapp.com';
	$GLOBALS['cfg']['auto_email_args']	= '-fadmin@ourapp.com';


	#
	# Set this to 'prod' for production use
	#

	$GLOBALS['cfg']['environment'] = 'dev';


	#
	# Things you can probably not worry about
	#

	$GLOBALS['cfg']['user'] = null;

	$GLOBALS['cfg']['smarty_compile'] = 1;

	$GLOBALS['cfg']['http_timeout'] = 3;

	$GLOBALS['cfg']['check_notices'] = 1;

	$GLOBALS['cfg']['db_profiling'] = 0;


	#
	# db_enable_poormans_*
	#
	# If enabled, then the relevant database configs and handles
	# will be automagically prepopulated using the relevant information
	# in 'db_main'
	#

	#
	# You should enable/set these flags if you want to
	# use flamework in a setting where you only have access
	# to a single database.
	#

	$GLOBALS['cfg']['db_enable_poormans_federation'] = 0;

	$GLOBALS['cfg']['db_enable_poormans_slaves'] = 0;

	$GLOBALS['cfg']['db_poormans_slaves_user'] = '';
	$GLOBALS['cfg']['db_poormans_slaves_pass'] = '';


	#
	# For when you want to use tickets but can't tweak
	# your my.cnf file or set up a dedicated ticketing
	# server. flamework does not use tickets as part of
	# core (yet) so this is really only necessary if your
	# application needs db tickets.
	#

	$GLOBALS['cfg']['db_enable_poormans_ticketing'] = 0;


	#
	# This will assign $pagination automatically for smarty
	#
	
	$GLOBALS['cfg']['pagination_assign_smarty_variable'] = 1;

	$GLOBALS['cfg']['pagination_per_page'] = 10;
	$GLOBALS['cfg']['pagination_spill'] = 2;
	$GLOBALS['cfg']['pagination_style'] = 'pretty';

	#
	# Feature flags
	#

	$GLOBALS['cfg']['enable_feature_signup'] = 1;
	$GLOBALS['cfg']['enable_feature_signin'] = 1;
	$GLOBALS['cfg']['enable_feature_persistent_login'] = 1;
	$GLOBALS['cfg']['enable_feature_account_delete'] = 1;
	$GLOBALS['cfg']['enable_feature_password_retrieval'] = 1;


	#
	# enable this flag to show a full call chain (instead of just the
	# immediate caller) in database query log messages and embedded in
	# the actual SQL sent to the server.
	#

	$GLOBALS['cfg']['db_full_callstack'] = 0;

	$GLOBALS['cfg']['allow_prefetch'] = 0;

?>