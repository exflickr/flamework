<?php
	if (!@include('PHP/CodeCoverage/Autoload.php')) exit;

	$dir = dirname(__FILE__);
	$file = "$dir/coverage.state";

	if (!file_exists($file)) exit;

	$coverage = unserialize(file_get_contents($file));

	$filter = $coverage->filter();

	$root_path = dirname(__FILE__).'/..';

	$filter->addFilesToWhitelist(glob("$root_path/www/include/*.php"));
	$filter->addFilesToWhitelist(glob("$root_path/extras/*.php"));
	$filter->addFilesToBlacklist(glob("$root_path/tests/*.t"));
	$filter->addFilesToBlacklist(glob("$root_path/tests/*.php"));

	$writer = new PHP_CodeCoverage_Report_HTML;
	$writer->process($coverage, './coverage');
