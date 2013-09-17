Extras
--

## lib_cache_memcache.php

In order to use the `memcache` plugin you will need to make sure that you have
installed the `php5-memcache` client.

You will also need to add a `memcache_pool`  config to your `config.php`
file. Memcache can bucket cache requests across a number of hosts hence the list
of lists. For example:

	$GLOBALS['cfg']['memcache_pool'] = array(
                array('host' => 'localhost', 'port' => 11211)
	);

You will need to explicitly load the `cache_memcache` library in your code or
enabled it using the `autoload_lib` array in your config file. Like this:

	$GLOBALS['cfg']['autoload_libs'] = array(
		'cache_memcache',
	);
