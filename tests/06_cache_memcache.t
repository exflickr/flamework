<?php
	include(dirname(__FILE__).'/wrapper.php');

	include(dirname(__FILE__).'/../extras/lib_cache_memcache.php');


	#
	# see if we can even load the extension
	#

	if (!extension_loaded('memcache')){

		$try = @dl('memcache.so');

		if (!$try){

			plan('skip_all');
			diag('Skipping memcache tests - could no load PECL extension');
			exit;
		}
	}

	plan(19);


	#
	# connection failures
	#


	$GLOBALS['cfg']['memcache_pool'] = array(
                array('host' => 'localhost', 'port' => 99999)
	);

	$a = array('a' => 4);

	$ret = cache_set('test1', $a);
	is($ret['ok'], 0, "Memcache set with bad server");
	is($ret['local'], 1, "Set worked locally");
	is($ret['remote'], 0, "Set failed remotely");
	is($ret['error'], 'memcache_cant_connect', "Expected error message");

	$ret = cache_get('test2');
	is($ret['ok'], 0, "Memcache get with bad server");
	is($ret['error'], 'memcache_cant_connect', "Expected error message");

	$ret = cache_unset('test3');
	is($ret['ok'], 0, "Memcache unset with bad server");
	is($ret['local'], 1, "Unset worked locally");
	is($ret['remote'], 0, "Unset failed remotely");
	is($ret['error'], 'memcache_cant_connect', "Expected error message");


	#
	# connection success!
	#

	$GLOBALS['cfg']['memcache_pool'] = array(
                array('host' => 'localhost', 'port' => 11211)
	);

	$conn = cache_memcache_connect();

	if (!$conn){
		skip("Skipping memcache tests - can't connect to local server", 9);
		exit;
	}


	#
	# set
	#

	$a = array(
		'a' => time(),
		'b' => md5(rand()),
	);
	$key = 'test'.time();

	$ret = cache_set($key, $a);

	is($ret['ok'], 1, "Remote cache set");
	is($ret['local'], 1, "Set worked locally");
	is($ret['remote'], 1, "Set worked remotely");


	#
	# get
	#

	$GLOBALS['_cache_local'] = array();

	$ret = cache_get($key);

	is($ret['ok'], 1, "Remote cache get");
	is($ret['source'], 'memcache', "Cache get came from remote cache");
	is_deeply($ret['data'], $a, "Data matches original");


	#
	# unset
	#

	$ret = cache_unset($key);
	is($ret['ok'], 1, "Remote cache unset");
	is($ret['local'], 1, "Unset worked locally");
	is($ret['remote'], 1, "Unset worked remotely");

