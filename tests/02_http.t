<?
	include(dirname(__FILE__).'/wrapper.php');

	plan(3);

	loadlib('http');

	$ret = http_get("http://google.com");

	is($ret['ok'], 0);
	is($ret['code'], 302);
	cmp_ok(strlen($ret['headers']['location']), '>', 0);

