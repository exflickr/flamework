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

		$start = microtime_ms();

		$memcache = new Memcache();

		if (!$memcache->connect($host, $port)){
			$memcache = null;
		}

		if (!$memcache){
			log_error("Connection to memcache {$host}:{$port} failed");
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
			log_error('failed to connect to memcache');
			return null;
		}

		$rsp = $memcache->get($key);

		if (!$rsp){
			log_notice("cache", "remote get {$key} - miss");
			return null;
		}

		log_notice("cache", "remote get {$key} - hit");
		return unserialize($rsp);
	}

	#################################################################

	function cache_memcache_set($key, $data){

		$memcache = cache_memcache_connect();

		if (!$memcache){
			log_error('failed to connect to memcache');
			return;
		}

		$ok = $memcache->set($key, serialize($data));

		if (!$ok){
			log_error("failed to set memcache key {$key}");
			return;
		}

		log_notice("cache", "remote set {$key}");
	}

	#################################################################

	function cache_memcache_unset($key){

		$memcache = cache_memcache_connect();

		if (!$memcache){
			log_error('failed to connect to memcache');
			return;
		}

		$ok = $memcache->delete($key);

		if (!$ok){
			log_error("failed to unset memcache key {$key}");
			return;
		}

		log_notice("cache", "remote unset {$key}");
	}

	#################################################################
