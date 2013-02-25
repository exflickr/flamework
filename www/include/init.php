<?
	#
	# $Id$
	#

	#
	# some startup tasks which come before anything else:
	#  * set up the timezone
	#  * record the time
	#  * set the mbstring encoding
	#

	# Also: there is running code at the bottom of this file

	error_reporting((E_ALL | E_STRICT) ^ E_NOTICE);

	putenv('TZ=PST8PDT');
	date_default_timezone_set('America/Los_Angeles');

	$GLOBALS['timings'] = array();
	$GLOBALS['timings']['execution_start'] = microtime_ms();
	$GLOBALS['timing_keys'] = array();

	mb_internal_encoding('UTF-8');


	#
	# the module loading code.
	#
	# we track which modules we've loaded ourselves instead of
	# using include_once(). we do this so that we can avoid the
	# stat() overhead involved in figuring out the canonical path
	# to a file. so long as we always load modules via this
	# method, we save some filesystem overhead.
	#
	# we can also ensure that modules don't pollute the global
	# namespace accidentally, since they are always loaded in a
	# function's private scope.
	#

	$GLOBALS['loaded_libs'] = array();

	define('FLAMEWORK_INCLUDE_DIR', dirname(__FILE__).'/');

	function loadlib($name){

		if ($GLOBALS['loaded_libs'][$name]){
			return;
		}

		$GLOBALS['loaded_libs'][$name] = 1;

		$fq_name = _loadlib_enpathify("lib_{$name}.php");
		include($fq_name);
	}

	function loadpear($name){

		if ($GLOBALS['loaded_libs']['PEAR:'.$name]){
			return;
		}

		$GLOBALS['loaded_libs']['PEAR:'.$name] = 1;

		$fq_name = _loadlib_enpathify("pear/{$name}.php");
		include($fq_name);
	}

	function _loadlib_enpathify($lib){

		# see also: http://www.php.net/manual/en/ini.core.php#ini.include-path

		$inc_path = ini_get('include_path');

		if (preg_match("/\/flamework\//", $inc_path)){
			return $lib;
		}

		return FLAMEWORK_INCLUDE_DIR . $lib;		
	}


	#
	# load config
	#

	if (!isset($GLOBALS['cfg']['flamework_skip_init_config'])){
		include(FLAMEWORK_INCLUDE_DIR."config.php");
	}

	# First, ensure that 'abs_root_url' is both assigned and properly
	# set up to run out of user's public_html directory (if need be).

	$server_url = $GLOBALS['cfg']['abs_root_url'];

	if (isset($_SERVER['SERVER_PORT'])) {
		$server_port = null;
		if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
			$server_port = $_SERVER['SERVER_PORT'];
		}

		if ($server_port) {
			$scheme = ($_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
			$server_url = "{$scheme}://{$_SERVER['SERVER_NAME']}:{$server_port}";
		}
	}
	
	if (! $server_url){
		$scheme = ($_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
		$server_url = "{$scheme}://{$_SERVER['SERVER_NAME']}";
	}

	$cwd = '';

	if ($parent_dirname = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/')){
		$parts = explode("/", $parent_dirname);
		$cwd = implode("/", array_slice($parts, 1));

		# see below
		$cwd = rtrim($cwd, '/');
	}

	# See this? We expect that abs_root_url always have a trailing slash.
	# Really it's just about being consistent. It doesn't really matter which
	# one you choose because either way it's going to be pain or a nuisance
	# at some point or another. So we choose trailing slashes.

	$GLOBALS['cfg']['abs_root_url'] = rtrim($server_url, '/') . "/";
	
	if ($cwd){
		$GLOBALS['cfg']['abs_root_url'] .= $cwd . "/";
	}

	$GLOBALS['cfg']['auth_cookie_domain'] = parse_url($GLOBALS['cfg']['abs_root_url'], 1);

	#
	# Poor man's database configs:
	# See notes in config.php
	#

	if ($GLOBALS['cfg']['db_enable_poormans_slaves']){

		$GLOBALS['cfg']['db_main_slaves'] = $GLOBALS['cfg']['db_main'];

		$GLOBALS['cfg']['db_main_slaves']['host'] = array(
			1 => $GLOBALS['cfg']['db_main']['host'],
		);

		$GLOBALS['cfg']['db_main_slaves']['name'] = array(
			1 => $GLOBALS['cfg']['db_main']['name'],
		);
	}

	if ($GLOBALS['cfg']['db_enable_poormans_ticketing']){

		$GLOBALS['cfg']['db_tickets'] = $GLOBALS['cfg']['db_main'];
	}

	if ($GLOBALS['cfg']['db_enable_poormans_federation']){

		$GLOBALS['cfg']['db_users'] = $GLOBALS['cfg']['db_main'];

		$GLOBALS['cfg']['db_users']['host'] = array(
			1 => $GLOBALS['cfg']['db_main']['host'],
		);

		$GLOBALS['cfg']['db_users']['name'] = array(
			1 => $GLOBALS['cfg']['db_main']['name'],
		);

	}

	#
	# install an error handler to check for dubious notices?
	# we do this because we only care about one of the notices
	# that gets generated. we only want to run this code in
	# devel environments. we also want to run it before any
	# libraries get loaded so that we get to check their syntax.
	#

	if ($cfg['check_notices']){
		set_error_handler('handle_error_notices', E_NOTICE);
		error_reporting(E_ALL | E_STRICT);
	}

	function handle_error_notices($errno, $errstr){
		if (preg_match('!^Use of undefined constant!', $errstr)) return false;
		return true;
	}


	#
	# figure out some global flags
	#

	$this_is_apache		= strlen($_SERVER['REQUEST_URI']) ? 1 : 0;
	$this_is_shell		= $_SERVER['SHELL'] ? 1 : 0;
	$this_is_webpage	= $this_is_apache && !$this_is_api ? 1 : 0;

	$cfg['admin_flags_no_db']		= $_GET['no_db'] ? 1 : 0;
	$cfg['admin_flags_show_notices']	= $_GET['debug'] ? 1 : 0;


	#
	# load some libraries which we will 'always' need
	#

	loadlib('features');
	loadlib('passwords');
	loadlib('auth');
	loadlib('log');		# logging comes first, so that other modules can log during startup
	loadlib('smarty');	# smarty comes next, since other libs register smarty modules
	loadlib('error');
	loadlib('sanitize');
	loadlib('filter');
	loadlib('db');
	loadlib('dbtickets');
	loadlib('cache');
	loadlib('crypto');
	loadlib('crumb');
	loadlib('login');
	loadlib('email');
	loadlib('utf8');
	#loadlib('args');
	#loadlib('calendar');
	loadlib('users');
	#loadlib('versions');
	loadlib('http');
	loadlib('paginate');

	if (($GLOBALS['cfg']['site_disabled']) && (! $this_is_shell)){

		header("HTTP/1.1 503 Service Temporarily Unavailable");
		header("Status: 503 Service Temporarily Unavailable");

		if ($retry = intval($GLOBALS['cfg']['site_disabled_retry_after'])){
			header("Retry-After: {$retry}");
		}

		$smarty->display("page_site_disabled.txt");
		exit();
	}

	#
	# general utility functions
	#

	function dumper($foo){
		echo "<pre style=\"text-align: left;\">";
		echo HtmlSpecialChars(var_export($foo, 1));
		echo "</pre>\n";
	}

	function intval_range($in, $lo, $hi){
		return min(max(intval($in), $lo), $hi);
	}

	function microtime_ms(){
		list($usec, $sec) = explode(" ", microtime());
		return intval(1000 * ((float)$usec + (float)$sec));
	}

	function filter_strict($str){

		$filter = new lib_filter();
		$filter->allowed = array();
		return $filter->go($str);
	}


	#
	# Hey look! Running code! Note that db_init will try
	# to automatically connect to the db_main database
	# (unless you've disable the 'auto_connect' flag) and
	# will blow its brains out if there's a problem.
	#
	
	db_init();

	if ($this_is_webpage){
		login_check_login();
	}

	if (StrToLower($_SERVER['HTTP_X_MOZ']) == 'prefetch'){

		if (! $GLOBALS['cfg']['allow_precache']){
			error_403();
		}
	}

	#
	# this timer stores the end of core library loading
	#

	$GLOBALS['timings']['init_end'] = microtime_ms();

