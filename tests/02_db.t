<?php
	include(dirname(__FILE__).'/wrapper.php');

	plan(2);

	$ret = db_fetch("SHOW TABLES");

	is($ret['ok'], 1, "Database fetch returned ok");
	is(count($ret['rows']), 2, "Expected number of tables in DB");
