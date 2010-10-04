<?
	#
	# $Id$
	#

	$GLOBALS['timings']['http_count']	= 0;
	$GLOBALS['timings']['http_time']	= 0;
	$GLOBALS['timing_keys']['http'] = 'HTTP Requests';

	########################################################################

	function http_get($url){

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); # Get around error 417
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $GLOBALS['cfg']['http_timeout']);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_HEADER, true);


		#
		# execute request
		#

		$start = microtime_ms();

		$raw = curl_exec($ch);
		$info = curl_getinfo($ch);

		$end = microtime_ms();

		curl_close($ch);

		$GLOBALS['timings']['http_count']++;
		$GLOBALS['timings']['http_time'] += $end-$start;


		#
		# parse request & response
		#

		list($head, $body) = explode("\r\n\r\n", $raw, 2);
		list($head_out, $body_out) = explode("\r\n\r\n", $info['request_header'], 2);
		unset($info['request_header']);

		$headers_in = http_parse_headers($head, '_status');
		$headers_out = http_parse_headers($head_out, '_request');
		log_notice("http", "GET $url", $end-$start);


		#
		# return
		#

	        if ($info['http_code'] != "200"){

			return array(
				'ok'		=> 0,
				'error'		=> 'http_failed',
				'code'		=> $info['http_code'],
				'url'		=> $url,
				'info'		=> $info,
				'req_headers'	=> $headers_out,
				'headers'	=> $headers_in,
				'body'		=> $body,
			);
		}

		return array(
			'ok'		=> 1,
			'url'		=> $url,
			'info'		=> $info,
			'req_headers'	=> $headers_out,
			'headers'	=> $headers_in,
			'body'		=> $body,
		);
	}

	########################################################################

	function http_parse_headers($raw, $first){

		#
		# first, deal with folded lines
		#

		$raw_lines = explode("\r\n", $raw);

		$lines = array();
		$lines[] = array_shift($raw_lines);

		foreach ($raw_lines as $line){
			if (preg_match("!^[ \t]!", $line)){
				$lines[count($lines)-1] .= ' '.trim($line);
			}else{
				$lines[] = trim($line);
			}
		}


		#
		# now split them out
		#

		$out = array(
			$first => array_shift($lines),
		);

		foreach ($lines as $line){
			list($k, $v) = explode(':', $line, 2);
			$out[StrToLower($k)] = trim($v);
		}

		return $out;
	}

	########################################################################
?>
