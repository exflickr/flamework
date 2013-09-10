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

You will need to explicitly load the `cache_memcache` library in your code.

Because I find that sort of tiresome this is what I do (note: this is _not_
enabled by default in Flamework). In my `config.php` I add the following:

	$GLOBALS['cfg']['enable_feature_memcache'] = 1;

And then in `init.php` I do this:

	loadlib("cache");	

	if (features_is_enabled("memcache")){
		loadlib("cache_memcache");
	}
