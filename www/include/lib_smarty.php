<?php

	$GLOBALS['timings']['smarty_comp_count']	= 0;
	$GLOBALS['timings']['smarty_comp_time']	= 0;

	define('FLAMEWORK_SMARTY_DIR', FLAMEWORK_INCLUDE_DIR.'/smarty-4.2.1/');
	require(FLAMEWORK_SMARTY_DIR . 'Smarty.class.php');

	$GLOBALS['smarty'] = new Smarty();

	$GLOBALS['smarty']->template_dir = $GLOBALS['cfg']['smarty_template_dir'];
	$GLOBALS['smarty']->compile_dir  = $GLOBALS['cfg']['smarty_compile_dir'];
	$GLOBALS['smarty']->compile_check = $GLOBALS['cfg']['smarty_compile'];
	$GLOBALS['smarty']->force_compile = $GLOBALS['cfg']['smarty_force_compile'];

	$GLOBALS['smarty']->assignByRef('cfg', $GLOBALS['cfg']);

	#######################################################################################

	function smarty_timings(){

		$GLOBALS['timings']['smarty_timings_out'] = microtime_ms();

		echo "<div class=\"admin-timings-wrapper\">\n";
		echo "<table class=\"admin-timings\">\n";

		# we add this one last so it goes at the bottom of the list
		$GLOBALS['timing_keys']['smarty_comp'] = 'Templates Compiled';

		foreach ($GLOBALS['timing_keys'] as $k => $v){
			$c = intval($GLOBALS['timings']["{$k}_count"]);
			$t = intval($GLOBALS['timings']["{$k}_time"]);
			echo "<tr><td>$v</td><td class=\"tar\">$c</td><td class=\"tar\">$t ms</td></tr>\n";
		}

		$map2 = array(
			array("Startup &amp; Libraries", $GLOBALS['timings']['init_end'] - $GLOBALS['timings']['execution_start']),
			array("Page Execution", $GLOBALS['timings']['smarty_start_output'] - $GLOBALS['timings']['init_end']),
			array("Smarty Output", $GLOBALS['timings']['smarty_timings_out'] - $GLOBALS['timings']['smarty_start_output']),
			array("<b>Total</b>", $GLOBALS['timings']['smarty_timings_out'] - $GLOBALS['timings']['execution_start']),
		);

		foreach ($map2 as $a){
			echo "<tr><td colspan=\"2\">$a[0]</td><td class=\"tar\">$a[1] ms</td></tr>\n";
		}

		echo "</table>\n";
		echo "</div>\n";
	}

	$GLOBALS['smarty']->registerPlugin('function', 'timings', 'smarty_timings');

	#######################################################################################

	#
	# Attempt to capture the start of Smarty template logic. This currently doesn't work,
	# since it is only run right at compilation time and can't inject more logic into the page
	#

	function smarty_timings_start_output($tpl_output, Smarty_Internal_Template $_template){
		$GLOBALS['timings']['smarty_start_output'] = microtime_ms();
		return $tpl_output;
	}

	$GLOBALS['smarty']->registerFilter('pre', 'smarty_timings_start_output');

	#######################################################################################