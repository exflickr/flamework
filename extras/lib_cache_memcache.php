<?php
	#
	# this library works by hooking into lib_cache - load it after
	# lib_cache to enable remote caching.
	#

	$GLOBALS['_cache_hooks']['set'] = 'cache_memcache_set';
	$GLOBALS['_cache_hooks']['get'] = 'cache_memcache_get';
	$GLOBALS['_cache_hooks']['unset'] = 'cache_memcache_unset';

	$GLOBALS['_cache_memcache_conn'] = null;

	#################################################################

	function cache_memcache_connect(){

		#
		# existing connection?
		#

		if ($GLOBALS['_cache_memcache_conn']){

			return $GLOBALS['_cache_memcache_conn'];
		}


		#
		# set up a new one
		#

		$host = $GLOBALS['cfg']['memcache_host'];
		$port = $GLOBALS['cfg']['memcache_port'];

		if (!$host){
			log_error("No host for memcache server");
			return null;
		}

		if (!$port){
			log_error("No port for memcache server");
			return null;
		}

		$start = microtime_ms();

		$memcache = new Memcache();

		if (!$memcache){
			log_error("Failed to create Memcache object");
			return null;
		}

		if (!@$memcache->connect($host, $port)){
			log_error("Connection to memcache server {$host}:{$port} failed - $php_errormsg");
			return null;
		}


		$end = microtime_ms();
		$time = $end - $start;

		log_notice("cache", "connect to memcache {$host}:{$port} ({$time}ms)");


		$GLOBALS['timings']['memcache_conns_count']++;
		$GLOBALS['timings']['memcache_conns_time'] += $time;


		$GLOBALS['_cache_memcache_conn'] = $memcache;
		return $memcache;
	}

	#################################################################

	function cache_memcache_get($key){

		$memcache = cache_memcache_connect();

		if (!$memcache){
			log_error('Failed to connect to memcache for get');
			return array(
				'ok'	=> 0,
				'error'	=> 'memcache_cant_connect',
			);
		}

		$rsp = $memcache->get($key);

		if (!$rsp){
			log_notice("cache", "remote get {$key} - miss");
			return array(
				'ok'	=> 0,
				'error'	=> 'memcache_miss',
			);
		}

		log_notice("cache", "remote get {$key} - hit");

		return array(
			'ok'		=> 1,
			'source'	=> 'memcache',
			'data'		=> unserialize($rsp),
		);
	}

	#################################################################

	function cache_memcache_set($key, $data){

		$memcache = cache_memcache_connect();

		if (!$memcache){
			log_error('Failed to connect to memcache for set');
			return array(
				'ok'		=> 0,
				'local'		=> 1,
				'remote'	=> 0,
				'error'		=> 'memcache_cant_connect',
			);
		}

		$ok = $memcache->set($key, serialize($data));

		if (!$ok){
			log_error("Failed to set memcache key {$key}");
			return array(
				'ok'		=> 0,
				'local'		=> 1,
				'remote'	=> 0,
				'error'		=> 'memcache_set_failed',
			);
		}

		log_notice("cache", "remote set {$key}");
		return array(
			'ok'		=> 1,
			'local'		=> 1,
			'remote'	=> 1,
		);
	}

	#################################################################

	function cache_memcache_unset($key){

		$memcache = cache_memcache_connect();

		if (!$memcache){
			log_error('Failed to connect to memcache for unset');
			return array(
				'ok'		=> 0,
				'local'		=> 1,
				'remote'	=> 0,
				'error'		=> 'memcache_cant_connect',
			);
		}

		$ok = $memcache->delete($key);

		if (!$ok){
			log_error("Failed to unset memcache key {$key}");
			return array(
				'ok'		=> 0,
				'local'		=> 1,
				'remote'	=> 0,
				'error'		=> 'memcache_unset_failed',
			);
		}

		log_notice("cache", "remote unset {$key}");
		return array(
			'ok'		=> 1,
			'local'		=> 1,
			'remote'	=> 1,
		);
	}

	#################################################################
