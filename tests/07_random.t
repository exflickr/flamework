<?php
	include(dirname(__FILE__).'/wrapper.php');

	loadlib('random');

	plan(4);

	$random = random_string(5);
	is(strlen($random), 5, "Made a random string of 5 characters");

	$random = random_string(30);
	is(strlen($random), 30, "Made a random string of 30 characters");

	$random = random_string(50);
	is(strlen($random), 50, "Made a random string of 50 characters");

	$random = random_string(70);
	is(strlen($random), 70, "Made a random string of 70 characters");

