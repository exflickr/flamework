<?php
	#
	# This module is designed to flexible, with pluggable handlers for different levels.
	# By default, 3 levels are defined:
	#
	# * fatal - Give up now and show an error page
	# * error - Log an error and move on
	# * notice - Some action has happened that's useful for debugging
	#
	# By default, errors and fatals are always shown, but notices are only shown when
	# `debug=1` is passed in the querystring or `$cfg['admin_flags_show_notices']` is
	# set. Messages are only shown (on webpages) for callers with appropriate auth
	# (see lib_auth.php for more details).
	#
	# The 'html' and 'plain' handlers are smart and will only show output where appropriate - the
	# html version for web pages and the plain version for CLI scripts.
	#

	$GLOBALS['log_handlers'] = array(
		'notice'	=> array('html', 'plain'),
		'error'		=> array('html', 'plain', 'error_log'),
		'fatal'		=> array('html', 'plain', 'error_log'),
	);

	$GLOBALS['log_html_colors'] = array(
		'db'		=> '#eef,#000',
		'cache'		=> '#fdd,#000',
		'smarty'	=> '#efe,#000',
		'http'		=> '#ffe,#000',
		'_error'	=> '#fcc,#000',
		'_fatal'	=> '#800,#fff',
	);


	#
	# log a startup notice so we know what page this is and what env
	#

	log_notice('init', "this is $_SERVER[SCRIPT_NAME] on {$GLOBALS['cfg']['environment']}");

	###################################################################################################################

	#
	# public api
	#

	function log_fatal($msg){
		_log_dispatch('fatal', $msg);
		error_500();		
		exit;
	}

	function log_error($msg){
		_log_dispatch('error', $msg);
	}


	function log_notice($type, $msg, $time=-1){
		_log_dispatch('notice', $msg, array('type' => $type, 'time' => $time));
	}

	###################################################################################################################

	function _log_dispatch($level, $msg, $more = array()){

		if ($GLOBALS['log_handlers'][$level]){

			foreach ($GLOBALS['log_handlers'][$level] as $handler){

				call_user_func("_log_handler_$handler", $level, $msg, $more);
			}
		}
	}


	###################################################################################################################

	#
	# print messages to the error log
	#

	function _log_handler_error_log($level, $msg, $more = array()){

		# if this is a CLI request, don't try and write to the error
		# log, since that's just STDERR

		if ($GLOBALS['this_is_shell']) return;

		$page = $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'];

		if ($more['type']){
			$msg = "[$more[type]] $msg";
		}

		$msg = str_replace("\n", ' ', $msg);

		error_log("[$level] $msg");
	}


	#
	# display messages in the browser
	#

	function _log_handler_html($level, $msg, $more = array()){

		# only show in-browser messages to staff
		if (!auth_has_role('staff')) return;

		# if this isn't a webpage, the `plain` handler will display the error
		if (!$GLOBALS['this_is_webpage']) return;

		# only shows notices if we asked to see them
		if ($level == 'notice' && !$GLOBALS['cfg']['admin_flags_show_notices']) return;

		$type = $more['type'] ? $more['type'] : '';

		$colors = $GLOBALS['log_html_colors']['_'.$level];
		if (!$colors) $colors = $GLOBALS['log_html_colors'][$type];
		if (!$colors) $colors = '#eee,#000';

		list($bgcolor, $color) = explode(',', $colors);

		echo "<div style=\"background-color: $bgcolor; color: $color; margin: 1px 1px 0 1px; border: 1px solid #000; padding: 4px; text-align: left; font-family: sans-serif;\">";

		if ($type) echo "[$type] ";

		echo HtmlSpecialChars($msg);

		if ($more['time'] > -1) echo " ($more[time] ms)";

		echo "</div>\n";
	}
	
	
	#
	# boring plaintext output (for scripts)
	#

	function _log_handler_plain($level, $msg, $more = array()){

		# if this isn't the shell, the `html` handler will take care of this
		if (!$GLOBALS['this_is_shell']) return;

		# only shows notices if we asked to see them
		if ($level == 'notice' && !$GLOBALS['cfg']['admin_flags_show_notices']) return;

		$type = $more['type'] ? $more['type'] : $level;

		$out = "";

		if ($type) $out .= "![$type] ";

		$out .= $msg;

		if ($more['time'] > -1) $out .= " ($more[time] ms)";

		$out .= "\n";

		fwrite(STDERR, $out);
	}

	###################################################################################################################
