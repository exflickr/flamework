<?php
	###############################################################################

	#
	# we can call this function anywhere from code where we want to make
	# the current request 404. that can be inside 404.php which gets requests
	# that apache can't map, or within a page handler that has checked QS
	# variables and can't find a matching object. or whatever else you want.
	#

	function error_404($msg=null){

		$url  = $_SERVER['REQUEST_URI'];
		$orig = $_SERVER['REDIRECT_URL'];


		#
		# try removing a slash at the end if:
		# 1) we've not already mapped it through a RewriteRule
		# 2) it currently has a slash at the end
		#

		list($url_path, $url_qs) = explode('?', $url, 2);

		if ($url_path == $orig){
			if (substr($url_path, -1) == '/'){
				if (strlen($url_qs)) $url_qs = '?'.$url_qs;
				header("location: ".substr($url_path, 0, -1).$url_qs);
				exit;
			}
		}


		#
		# static redirect map. add things here if you know you moved them.
		#

		if ($redir = $GLOBALS['cfg']['rewrite_static_urls'][$url]){
			header("location: {$redir}");
			exit;
		}


		#
		# give up
		#

		$GLOBALS['cfg']['no_cache'] = 1;


		#
		# build debug block
		#

		$debug_block = '';
		if (!is_null($msg)){
			$debug_block .= "Message:\n";
			$debug_block .= error_format_pre($msg)."\n\n";
		}

		$debug_block .= "Args:\n";
		$args = array(
			'SERVER_REQUEST_URI'	=> $_SERVER['REQUEST_URI'],
			'SERVER_REDIRECT_URL'	=> $_SERVER['REDIRECT_URL'],
		);
		$debug_block .= error_format_hash($args)."\n\n";

		$debug_block .= "Backtrace:\n";
		$debug_block .= error_format_indent(error_smart_trace());

		$GLOBALS['smarty']->assign('debug_block', $debug_block);


		#
		# output
		#

		$GLOBALS['smarty']->display('page_error_404.txt');
		exit;
	}

	###############################################################################

	function error_403($msg=null){

		$GLOBALS['cfg']['no_cache'] = 1;


		#
		# build debug block
		#

		$debug_block = '';
		if (!is_null($msg)){
			$debug_block .= "Message:\n";
			$debug_block .= error_format_pre($msg)."\n\n";
		}

		$debug_block .= "Args:\n";
		$args = array(
			'SERVER_REQUEST_URI'	=> $_SERVER['REQUEST_URI'],
			'SERVER_REDIRECT_URL'	=> $_SERVER['REDIRECT_URL'],
		);
		$debug_block .= error_format_hash($args)."\n\n";

		$debug_block .= "Backtrace:\n";
		$debug_block .= error_format_indent(error_smart_trace());

		$GLOBALS['smarty']->assign('debug_block', $debug_block);


		#
		# output
		#

		$GLOBALS['smarty']->display('page_error_403.txt');
		exit;
	}

	###############################################################################

	function error_410($msg=''){

		$GLOBALS['smarty']->assign("message", $msg);
		$GLOBALS['smarty']->display('page_error_410.txt');
		exit;
	}

	###############################################################################

	function error_500($msg=null){

		$GLOBALS['cfg']['no_cache'] = 1;


		#
		# build debug block
		#

		$debug_block = '';
		if (!is_null($msg)){
			$debug_block .= "Message:\n";
			$debug_block .= error_format_pre($msg)."\n\n";
		}

		$debug_block .= "Args:\n";
		$args = array(
			'SERVER_REQUEST_URI'	=> $_SERVER['REQUEST_URI'],
			'SERVER_REDIRECT_URL'	=> $_SERVER['REDIRECT_URL'],
		);
		$debug_block .= error_format_hash($args)."\n\n";

		$debug_block .= "Backtrace:\n";
		$debug_block .= error_format_indent(error_smart_trace());

		$GLOBALS['smarty']->assign('debug_block', $debug_block);


		#
		# output
		#

		$GLOBALS['smarty']->display('page_error_500.txt');
		exit;
	}

	###############################################################################

	# TO DO: work out how to use this for when the site is disabled
	# in include/init.php (

	function error_disabled($feature=''){

		header("HTTP/1.1 503 Service Temporarily Unavailable");
		header("Status: 503 Service Temporarily Unavailable");

		$GLOBALS['smarty']->assign("feature", $feature);

		$GLOBALS['smarty']->display("page_feature_disabled.txt");
		exit();
	}

	###############################################################################

	function error_smart_trace(){

		$root_path = realpath(dirname(__FILE__)."/..");

		$trace = debug_backtrace();
		$pairs = array();

		foreach ($trace as $item){

			$args = array();
			foreach ($item['args'] as $arg){
				if (is_object($arg)){
					$args[] = "Object()";
				}else{
					# this will just string-ify the arg.
					# var_export() would be great here if it didn't crash on
					# circular references :(
					$args[] = "$arg";
				}
			}
			$args = implode(', ', $args);

			$function = "{$item['function']}($args)";

			if (preg_match('!^error_!', $item['function'])){
				$pairs = array();
				$function = "ERROR";
			}

			$file = str_replace($root_path, '', $item['file']);

			$pairs[] = array(
				$function,
				"$file:$item[line]",
			);
		}

		return error_format_table($pairs, 4);
	}

	###############################################################################

	function error_format_pre($data){

		if (is_string($data)) return error_format_indent($data);

		return error_format_indent(var_export($data, 1));
	}

	function error_format_indent($data){

		$lines = explode("\n", trim(HtmlSpecialChars($data)));

		$out = '';

		foreach ($lines as $line){
			$out .= "\t$line\n";
		}

		return $out;
	}

	function error_format_table($pairs, $padding=4){

		#
		# get section lengths
		#

		$lengths = array();
		foreach ($pairs as $pair){
			foreach ($pair as $k => $str){
				$lengths[$k] = max(intval($lengths[$k]), strlen($str));
			}
		}


		#
		# build lines
		#

		$pad = str_repeat(' ', $padding);
		$out = '';

		foreach ($pairs as $kp => $pair){
			foreach ($lengths as $k => $len){
				$pairs[$kp][$k] = str_pad($pairs[$kp][$k], $lengths[$k], ' ', STR_PAD_RIGHT);
			}
			$out .= implode($pad, $pairs[$kp])."\n";
		}

		return $out;
	}

	function error_format_hash($hash){

		$pairs = array();
		foreach ($hash as $k => $v){
			$pairs[] = array($k, $v);
		}

		return error_format_indent(error_format_table($pairs));
	}

	###############################################################################
