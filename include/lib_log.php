<?php
	#
	# $Id$
	#

	$GLOBALS[lib_log_colors] = array(
		'db'		=> '#eef',
		'smarty'	=> '#efe',
		'http'		=> '#ffe',
	);

	$GLOBALS[lib_log_handlers] = array(
		'notice'	=> array('html'),
		'error'		=> array('error_log'),
	);
	
	function log_fatal($msg){
		die("FATAL ERROR: ".$msg);
	}

	function log_error($msg){
		_log_dispatch('error', $msg);
	}
	
	function log_notice($type, $msg, $time=-1){
		_log_dispatch('notice', $msg, array(type => $type, time => $time));
	}
	
	
	function _log_dispatch($level, $msg, $more = array()) {
		if ($GLOBALS[lib_log_handlers][$level]) {
			foreach ($GLOBALS[lib_log_handlers][$level] as $handler) {
				call_user_func("_log_handler_$handler", $level, $msg, $more);
			}
		}
	}
	
	function _log_handler_error_log($level, $msg, $more = array()) {
		$page = $GLOBALS[HTTP_SERVER_VARS][REQUEST_URI];
		
		if ($more[type]) {
			$msg = "[$more[type]] $msg";
		}
		
		$msg = str_replace("\n", ' ', $msg);
		
		error_log("[$level] $msg ($page)");
	}
	
	function _log_handler_html($level,$msg, $more = array()) {
		if (!$_GET[debug]) return;
		
		$type = $more[type] ? $more[type] : '';
		
		$color = $GLOBALS[log_colors][$type] ? $GLOBALS[lib_log_colors][$type] : '#eee';

		echo "<div style=\"background-color: $color; margin: 1px 1px 0 1px; border: 1px solid #000; padding: 4px; text-align: left\">";

		if ($type) {
			$msg = "[$type] $msg";
		}
		
		echo "$msg";
		if ($more[time] > -1){
			echo " ($more[time] ms)";
		}

		echo "</div>\n";
		
	}
?>