<?php
	include(dirname(__FILE__).'/wrapper.php');

	plan(23);

	loadlib('http');

	function test_http_method($ret, $method, $gets, $posts){

		is($ret['ok'], 1, "Ok for $method/$gets/$posts");
		is($ret['code'], 200, "Code 200 for $method/$gets/$posts");

		$args = array();
		parse_str($ret['body'], $args);

		if ($method == 'HEAD'){

			is($ret['body'], "", "Empty body for $method/$gets/$posts");

		}else{
			is($args['method'], $method, "Method for for $method/$gets/$posts");
			is($args['gets'], $gets, "Get arg count for for $method/$gets/$posts");
			is($args['posts'], $posts, "Post arg count for $method/$gets/$posts");
		}
	}


	$ret = http_get("http://www.iamcal.com/misc/test/method.php");
	test_http_method($ret, 'GET', 0, 0);

	$ret = http_get("http://www.iamcal.com/misc/test/method.php?a=1&b=2");
	test_http_method($ret, 'GET', 2, 0);

	$ret = http_head("http://www.iamcal.com/misc/test/method.php");
	test_http_method($ret, 'HEAD', 0, 0);

	$ret = http_post("http://www.iamcal.com/misc/test/method.php", array());
	test_http_method($ret, 'POST', 0, 0);

	$ret = http_post("http://www.iamcal.com/misc/test/method.php?a=1", array('b' => 2, 'c' => 3));
	test_http_method($ret, 'POST', 1, 2);
