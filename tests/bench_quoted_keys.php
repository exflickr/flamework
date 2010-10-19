<?php
	#
	# This test checks the hypothesis (by http://github.com/mihasya) that quoting
	# key names in hash accesses is a significant performance improvement. The
	# theory is that PHP needs to check if a bareword is a constant or a string,
	# and can only check this at run time. To test this, we call a function
	# repeatedly that creates a very deep array tree using quoted and non-quoted
	# keys names.
	#
	# Some results:
	#
	#	Machine		PHP	Non-Quoted	Quoted
	#	Cal's PC	5.2.9	426ms		 49ms
	#	EC2 small	5.2.6	691ms		113ms
	#	Jacques PC	5.3.0	58,498ms	35ms
	#
	# So for a ton of assignments, it can make a significant difference. However,
	# you'll be saving 400ms 280,000 referenced keys, or 1ms per ~700 keys, so this
	# is unlikely to add up to a single ms, unless you have e.g. a huge config hash
	# that you're running on every page load. And remember, it's hash keys you actually
	# dereference, not just ones in code - the constant-check happens when the code
	# runs, not when it's compiled to opcodes.
	#

	function microtime_ms(){
		list($usec, $sec) = explode(" ", microtime());
		return intval(1000 * ((float)$usec + (float)$sec));
	}



	# prime anything that can be primed?
	for ($i=0; $i<100; $i++){ try_nq(); try_qq(); }


	#
	# do iiiit
	#

	$num = 10000;

	$t1 = microtime_ms();
	for ($i=0; $i<$num; $i++){ try_nq(); }
	$t2 = microtime_ms();
	for ($i=0; $i<$num; $i++){ try_qq(); }
	$t3 = microtime_ms();


	echo "Time taken to run 8 assignements ".number_format($num)." times, using 28 key names:<br /><br />\n";

	echo "bare-word keys: ".number_format($t2-$t1)." ms<br />\n";
	echo "quoted keys: ".number_format($t3-$t2)." ms<br />\n";


	function try_nq(){
		$a = array();
		$a[b] = array();
		$a[b][c] = array();
		$a[b][c][d] = array();
		$a[b][c][d][e] = array();
		$a[b][c][d][e][f] = array();
		$a[b][c][d][e][f][g] = array();
		$a[b][c][d][e][f][g][h] = 1;
		return $a;
	}

	function try_qq(){
		$a = array();
		$a['b'] = array();
		$a['b']['c'] = array();
		$a['b']['c']['d'] = array();
		$a['b']['c']['d']['e'] = array();
		$a['b']['c']['d']['e']['f'] = array();
		$a['b']['c']['d']['e']['f']['g'] = array();
		$a['b']['c']['d']['e']['f']['g']['h'] = 1;
		return $a;
	}

?>
