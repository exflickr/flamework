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

	if (!$GLOBALS['cfg']['flamework_skip_init_config']){
		include(FLAMEWORK_INCLUDE_DIR."/config.php");
	}

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

	loadlib('log');		# logging comes first, so that other modules can log during startup
	loadlib('smarty');	# smarty comes next, since other libs register smarty modules
	loadlib('error');
	loadlib('sanitize');
	loadlib('filter');
	loadlib('db');
	loadlib('dbtickets');
	#loadlib('cache');
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
	# Smarty stuff
	#

	$GLOBALS['error'] = array();
	$GLOBALS['smarty']->assign_by_ref('error', $error);

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
	
?>
