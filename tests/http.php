<?
	#
	# $Id$
	#

	include('../include/init.php');

	echo "<h1>http://google.com (should 304)</h1>";
	dumper(http_get("http://google.com"));

	echo "<h1>http://www.google.com (should 200)</h1>";
	dumper(http_get("http://www.google.com"));
?>