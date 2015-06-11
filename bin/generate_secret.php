<?php

	include("init_local.php");
	loadlib("random");

	$length = 32;

	echo random_string($length) . "\n";
	exit();
?>
