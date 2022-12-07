<?php

	function utf8_headers($mimetype = 'text/html'){

		header("Content-Type: $mimetype; charset=utf-8");

		if ($GLOBALS['cfg']['no_cache'] ?? false){

			# Date in the past
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

			# always modified
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			# HTTP/1.1
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);

			# HTTP/1.0
			header("Pragma: no-cache");
		}else{

			if ($GLOBALS['cfg']['user']['id'] ?? false){

				  header("Cache-Control: private");
			}
		}
	}

	function utf8_headers_smarty_comp($tag_attrs, $compiler){
		# TODO: How to do this in modern smarty? Even necessary?
		/*$_params = $compiler->_parse_attrs($tag_attrs);
		#if ($_params['mimetype']){
		#	return "<?php utf8_headers($_params[mimetype]); ?>";
		}else{*/
			return "<?php utf8_headers(); ?>";
		#}
	}

	$GLOBALS['smarty']->registerPlugin('compiler', 'utf8_headers', 'utf8_headers_smarty_comp');

