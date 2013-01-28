<?php
	#
	# this library provides local (in-process) caching and allows
	# for a remote cache (like memcache) to be easily plugged in.
	#
	# see lib_cache_memcache for details of remote caching.
	#


	$GLOBALS['_cache_local'] = array();

	$GLOBALS['_cache_hooks'] = array(
		'get'	=> null,
		'set'	=> null,
		'unset'	=> null,
	);

	#################################################################

	function cache_get($key){

		#
		# try and fetch from local cache first
		#

		if (isset($GLOBALS['_cache_local'][$key])){

			log_notice("cache", "get {$key} - local hit");

			return array(
				'ok'		=> 1,
				'source'	=> 'local',
				'data'		=> $GLOBALS['_cache_local'][$key],
			);
		}


		#
		# try the remote cache?
		#

		if ($GLOBALS['_cache_hooks']['get']){

			return call_user_func($GLOBALS['_cache_hooks']['get'], $key);
		}

		log_notice("cache", "get {$key} - local miss");

		return array(
			'ok' => 1,
		);
	}

	#################################################################

	function cache_set($key, $data){

		$GLOBALS['_cache_local'][$key] = $data;

		if ($GLOBALS['_cache_hooks']['set']){

			return call_user_func($GLOBALS['_cache_hooks']['set'], $key, $data);
		}

		log_notice("cache", "set {$key}");

		return array(
			'ok'	=> 1,
			'local'	=> 1,
		);
	}

	#################################################################

	function cache_unset($key){

		unset($GLOBALS['_cache_local'][$key]);

		if ($GLOBALS['_cache_hooks']['unset']){

			return call_user_func($GLOBALS['_cache_hooks']['unset'], $key);
		}

		log_notice("cache", "unset {$key}");

		return array(
			'ok'	=> 1,
			'local'	=> 1,
		);
	}

	#################################################################
