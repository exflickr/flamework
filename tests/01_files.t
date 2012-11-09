<?
	include(dirname(__FILE__).'/testmore.php');

	$path = dirname(__FILE__).'/../www';

	$libs = glob("$path/include/*.php");
	$views = glob("$path/*.php");

	$all = array_merge($libs, $views);

	plan(2* count($all));

	foreach ($all as $path){
		$content = implode('', file($path));
		$file = basename($path);

		is(substr($content, 0, 2), '<?', "$file starts with an opening php tag");

		$idx = strpos($content, '?'.'>');
		cmp_ok($idx, '===', false, "No closing tag in $file");
	}
