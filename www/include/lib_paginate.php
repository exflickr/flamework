<?php
	#
	# This library handles output of paginated results, drawing pagination
	# widgets to allow jump-to-page and pre/next.
	#
	# See _db_fetch_paginated for the format of the $pagination param
	#

	$GLOBALS['_paginate_done_shortcuts'] = false;
	$GLOBALS['_paginate_shortcuts_content'] = '';

	#################################################################

	function smarty_pagination_function($params, &$smarty){
		global $smarty;

		# save state
		$old_vars = $smarty->get_template_vars();

		$pagination = $params['pagination'] ? $params['pagination'] : $old_vars['pagination'];
		
		if ($pagination['page_count'] < 2){ return; }

		paginate_calculate($pagination, $params);

		$smarty->assign('pagination', $pagination);
		$smarty->assign('params', $params);

		$style = 'pretty';
		if ($params['style']) $style = $params['style'];

		$smarty->display("inc_pagination_{$style}.txt");

		# are we adding next/prev shortcuts?
		if ($GLOBALS['cfg']['pagination_keyboard_shortcuts'] || $GLOBALS['cfg']['pagination_touch_shortcuts']){

			if (!$GLOBALS['_paginate_done_shortcuts']){

				$GLOBALS['_paginate_done_shortcuts'] = true;
				$GLOBALS['_paginate_shortcuts_content'] = $smarty->fetch("inc_pagination_shortcuts.txt");
			}
		}

		# restore state
		$smarty->_tpl_vars = $old_vars;
	}

	$GLOBALS['smarty']->register_function('pagination', 'smarty_pagination_function');


	#################################################################

	function smarty_pagination_footer_function(){

		if ($GLOBALS['_paginate_done_shortcuts']){

			echo $GLOBALS['_paginate_shortcuts_content'];
		}
	}

	$GLOBALS['smarty']->register_function('pagination_footer', 'smarty_pagination_footer_function');

	#################################################################

	#
	# build list of pages to show links to
	#

	function paginate_calculate(&$pagination, $params){

		#
		# range controls how many pages we show to each side of the
		# current page marker, before we switch to an ellipsis.
		#

		$range = 3;

		$left_range = $range;
		$right_range = $range;

		if ($pagination['page'] < 2){
			$left_range = 0;
			$right_range = $range * 2;
		}


		#
		# build list of page links
		#

		$pages = array();

		for ($i=1; $i<=$pagination['page_count']; $i++){
			if ($i >= $pagination['page'] - $left_range && $i <= $pagination['page'] + $right_range){
				$pages[] = array(
					'page'		=> $i,
					'is_current'	=> ($i == $pagination['page']) ? 1 : 0,
					'url'		=> paginate_make_url($i, $params),
				);
			}
			elseif ($i <= 2 || $i > $pagination['page_count'] - 2){
				$pages[] = array(
					'page'	=> $i,
					'url'	=> paginate_make_url($i, $params),
				);
			}
			elseif ($i == $pagination['page'] - ($left_range + 1) || $i == $pagination['page'] + ($right_range + 1)){				
				$pages[] = array(
					'is_dots'	=> 1,
				);
			}
		}

		$pagination['page_links'] = $pages;
		
		$pagination['next_url'] = paginate_make_url($pagination['page'] + 1, $params);
		$pagination['prev_url'] = paginate_make_url($pagination['page'] - 1, $params);

		$pagination['has_next'] = !!($pagination['page'] < $pagination['page_count']);
		$pagination['has_prev'] = !!($pagination['page'] > 1);
	}

	#################################################################

	#
	# build a URL to a specific page
	#

	function paginate_make_url($page, $params){

		$uri = $_SERVER['REQUEST_URI'];
		list($path, $qs) = explode('?', $uri, 2);


		#
		# if page_param is specified, use get params
		#

		if ($params['page_param']){

			$args = array();
			parse_str($qs, $args);

			if ($page == 1){
				unset($args[$params['page_param']]);
			}else{
				$args[$params['page_param']] = $page;
			}
			$pairs = array();
			foreach ($args as $k => $v){
				$pairs[] = urlencode($k).'='.urlencode($v);
			}
			if (count($pairs)){
				return $path . '?' . implode('&', $pairs);
			}
			return $path;
		}


		#
		# else we're using URL patterns
		#

		$pattern = '/page#';
		if ($params['page_pattern']) $pattern = $params['page_pattern'];


		#
		# try and determine the base URL
		#

		$match = preg_quote($pattern, '!');
		$match = str_replace('#', '\d+', $match);

		if (preg_match("!($match)$!", $path, $m)){

			$path = substr($path, 0, 0-strlen($m[1]));
		}


		#
		# add the template if we're past page 1
		#

		if ($page > 1){
			$path .= str_replace('#', $page, $pattern);
		}

		if (strlen($qs)) $path .= '?'.$qs;

		return $path;
	}

	#################################################################
