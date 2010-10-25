<?php

	#
	# $Id$
	#
	
	#################################################################
	
	loadlib("http");
	
	#################################################################

	function geo_geocode_service_map($string_keys=0){

		# 0 means 'not geocoded'

		$map = array(
			1 => 'yahoo',
		);

		if ($string_keys){
			$map = array_flip($map);
		}

		return $map;
	}
	
	#################################################################
	
	function geo_geocode_string($string){

		$service = $GLOBALS['cfg']['geo_geocoding_service'];
		$func = "geo_geocode_{$service}";

		if ((! $service) || (! is_callable($func))){

			return array(
				'ok' => 0,
				'error' => 'Unknown or undefined service',
			);
		}

		$rsp = call_user_func($func, $string);

		$map = geo_geocode_service_map('string keys');
		$rsp['service_id'] = $map[ $service ];

		return $rsp;
	}

	#################################################################
	
	function geo_geocode_yahoo($string){

		$api_key = $GLOBALS['cfg']['geo_geocoding_yahoo_apikey'];

		$url = 'http://where.yahooapis.com/geocode?q='.urlencode($string).'&flags=j&appid='.urlencode($api_key);

		$http_rsp = http_get($url);
		
		$rsp = array(
			'ok' => 0,
			'error' => 'unknown error'
		);
		
		if ($http_rsp['ok']) {
			
			# pass in a 1 to disable 'shit-mode'
			$geocode_response = json_decode($http_rsp['body'], 1);
			
			if ($geocode_response['ResultSet']['Found'] == 1) {
				
				$results = $geocode_response['ResultSet']['Results'][0];
				
				$rsp['ok'] = 1;
				$rsp['error'] = null;
				$rsp['latitude'] = (float)$results['latitude'];
				$rsp['longitude'] = (float)$results['longitude'];
				$rsp['extras']['woeid'] = (float)$results['woeid'];
			} else {
				$rsp['error'] = 'could not geocode';
			}
			
		}
		
		return $rsp;
	}
	
?>