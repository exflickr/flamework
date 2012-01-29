<?
	#
	# $Id$
	#

	include('testmore.php');
	include('../www/include/init.php');

	$ret = http_get("http://google.com");

	is($ret['ok'], 0);
	is($ret['code'], 302);
	is_cmp(strlen($ret['headers']['location']), '>', 0);

	#dumper($ret);
	exit;

	echo "<h1>http://google.com (should 304)</h1>";
	dumper(http_get("http://google.com"));

	echo "<h1>http://www.google.com (should 200)</h1>";
	dumper(http_get("http://www.google.com"));
?>
