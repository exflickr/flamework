<?php
	#
	# $Id$
	#

	$GLOBALS[log_colors] = array(
		'db'		=> '#eef',
		'smarty'	=> '#efe',
		'http'		=> '#ffe',
	);

	function log_fatal($msg){

		die("FATAL ERROR: ".$msg);
	}

	function log_error($msg){
		if (!$_GET[debug]) return;

	}
	
	function log_notice($type, $msg, $time=-1){
		if (!$_GET[debug]) return;


		$color = $GLOBALS[log_colors][$type] ? $GLOBALS[log_colors][$type] : '#eee';

		echo "<div style=\"background-color: $color; margin: 1px 1px 0 1px; border: 1px solid #000; padding: 4px; text-align: left\">";

		echo "$msg";
		if ($time > -1){
			echo " ($time ms)";
		}

		echo "</div>\n";
	}

?>
