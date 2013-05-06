<?php
	include(dirname(__FILE__).'/wrapper.php');

	plan(9);


	#
	# set
	#

	$a = array('a' => 4);

	$ret = cache_set('test1', $a);

	is($ret['ok'], 1, "Local cache set");
	is($ret['local'], 1, "Set worked locally");
	isnt($ret['remote'], 1, "Set failed remote");


	#
	# get
	#

	$ret = cache_get('test1');

	is($ret['ok'], 1, "Local cache get");
	is($ret['source'], 'local', "Cache get came from local cache");
	is_deeply($ret['data'], $a, "Data matches original");


	#
	# unset
	#

	$ret = cache_unset('test1');
	is($ret['ok'], 1, "Local cache unset");
	is($ret['local'], 1, "Unset worked locally");
	isnt($ret['remote'], 1, "Unset failed remote");

