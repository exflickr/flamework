<?php
	include('include/init.php');

	$tests = array();

	$tests[] = array(
		'name'		=> 'Single page',
		'total_count'	=> 5,
		'page'		=> 1,
		'per_page'	=> 10,
		'page_count'	=> 1,		
	);

	$tests[] = array(
		'name'		=> 'Page one of many',
		'total_count'	=> 500,
		'page'		=> 1,
		'per_page'	=> 10,
		'page_count'	=> 50,
	);

	$tests[] = array(
		'name'		=> 'Low page of many',
		'total_count'	=> 500,
		'page'		=> 3,
		'per_page'	=> 10,
		'page_count'	=> 50,
	);

	$tests[] = array(
		'name'		=> 'Mid page of many',
		'total_count'	=> 500,
		'page'		=> 12,
		'per_page'	=> 10,
		'page_count'	=> 50,
	);

	$tests[] = array(
		'name'		=> 'High page of many',
		'total_count'	=> 500,
		'page'		=> 48,
		'per_page'	=> 10,
		'page_count'	=> 50,
	);

	$tests[] = array(
		'name'		=> 'Last page of many',
		'total_count'	=> 500,
		'page'		=> 50,
		'per_page'	=> 10,
		'page_count'	=> 50,
	);

	$smarty->assign('tests', $tests);

	$smarty->display('page_paging.txt');
