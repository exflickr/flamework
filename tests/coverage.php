<?php
	if (!@include('PHP/CodeCoverage/Autoload.php')) exit;

	$dir = dirname(__FILE__);
	$file = "$dir/coverage.state";

	if (!file_exists($file)) exit;

	class MyCoverage extends PHP_CodeCoverage{
		public function resetFilter(){
			$this->filter = new PHP_CodeCoverage_Filter;
		}
		public function applyFilter(){
			 $this->applyListsFilter($this->data);
		}
	}

	$serial = file_get_contents($file);

	$old = 'O:16:"PHP_CodeCoverage"';
	$new = 'O:10:"MyCoverage"';
	$serial = str_replace($old, $new, $serial);

	$coverage = unserialize($serial);

	$root_path = dirname(__FILE__).'/..';

	$coverage->resetFilter();
	$coverage->filter()->addFilesToWhitelist(glob("$root_path/www/include/*.php"));
	$coverage->filter()->addFilesToWhitelist(glob("$root_path/extras/*.php"));
	$coverage->applyFilter();

	$writer = new PHP_CodeCoverage_Report_HTML;
	$writer->process($coverage, './coverage');
