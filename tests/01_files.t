<?php
	include(dirname(__FILE__).'/testmore.php');

	$root_path = dirname(__FILE__).'/../www';

	$libs = glob("$root_path/include/*.php");
	$views = glob("$root_path/*.php");

	$all = array_merge($libs, $views);

	plan(3 * count($all));

	foreach ($all as $path){
		$content = implode('', file($path));
		$file = str_replace("$root_path/", '', $path);

		is(substr($content, 0, 5), '<?php', "$file starts with an opening php tag");

		$idx = strpos($content, '?'.'>');
		cmp_ok($idx, '===', false, "No closing tag in $file");

		$idx = strpos($content, '$'.'Id$');
		cmp_ok($idx, '===', false, "No SVN keywordsin $file");
	}
