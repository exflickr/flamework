<?php
	#
	# Generates the bits of the apache configuration needed for production
	# assumes that you don't allow .htaccess files and have everything
	# in your virtualhosts/site.conf
	#

	chdir(dirname(dirname(__FILE__)));
	$files = split("\n", trim(`find . -type f -name '.htaccess' | sed 's/^.\///g'`));

	$conf = file_get_contents(dirname(dirname(__FILE__)) . '/.htaccess') . "\n";
	foreach ($files as $file) {
		$dir = explode("/", $file);
		array_pop($dir);
		$dir = implode("/", $dir);
		$conf .= "<Location \"${dir}\">\n" . trim(file_get_contents(dirname(dirname(__FILE__)) . "/" . $file)) . "\n</Location>\n";
	}
	file_put_contents(dirname(__FILE__) . "/flamework.directives.conf", $conf);