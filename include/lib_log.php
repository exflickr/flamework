<?php
	#
	# $Id$
	#

	$GLOBALS[log_html_colors] = array(
		'db'		=> '#eef,#000',
		'smarty'	=> '#efe,#000',
		'http'		=> '#ffe,#000',
		'_error'	=> '#fcc,#000',
		'_fatal'	=> '#800,#fff',
	);

	$GLOBALS[log_handlers] = array(
		'notice'	=> array('html'),
		'error'		=> array('html', 'error_log'),
		'fatal'		=> array('html', 'error_log'),
	);


	###################################################################################################################

	#
	# public api
	#

	function log_fatal($msg){
		_log_dispatch('fatal', $msg);
		exit;
	}

	function log_error($msg){
		_log_dispatch('error', $msg);
	}


	function log_notice($type, $msg, $time=-1){
		_log_dispatch('notice', $msg, array(type => $type, time => $time));
	}


	function _log_dispatch($level, $msg, $more = array()){

		if ($GLOBALS[log_handlers][$level]){

			foreach ($GLOBALS[log_handlers][$level] as $handler){

				call_user_func("_log_handler_$handler", $level, $msg, $more);
			}
		}
	}

	###################################################################################################################

	#
	# log handlers
	#

	function _log_handler_error_log($level, $msg, $more = array()){
		$page = $GLOBALS[HTTP_SERVER_VARS][REQUEST_URI];

		if ($more[type]){
			$msg = "[$more[type]] $msg";
		}

		$msg = str_replace("\n", ' ', $msg);

		error_log("[$level] $msg ($page)");
	}

	function _log_handler_html($level, $msg, $more = array()){
		if (!$_GET[debug]) return;

		$type = $more[type] ? $more[type] : '';

		$colors = $GLOBALS[log_html_colors]['_'.$level];
		if (!$colors) $colors = $GLOBALS[log_html_colors][$type];
		if (!$colors) $colors = '#eee,#000';

		list($bgcolor, $color) = explode(',', $colors);

		echo "<div style=\"background-color: $bgcolor; color: $color; margin: 1px 1px 0 1px; border: 1px solid #000; padding: 4px; text-align: left; font-family: sans-serif;\">";

		if ($type) echo "[$type] ";

		echo HtmlSpecialChars($msg);

		if ($more[time] > -1) echo " ($more[time] ms)";

		echo "</div>\n";
	}

	###################################################################################################################
?>