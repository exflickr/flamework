<?
	$GLOBALS[cfg] = array();

	$GLOBALS[cfg][db_main] = array(
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'name'	=> 'flamework',
	);

	$GLOBALS[cfg][db_users] = array(
		'host' => array(
			1 => 'localhost',
			2 => 'localhost',
		),
		'user' => 'root',
		'pass' => 'root',
		'name' => array(
			1 => 'user1',
			2 => 'user2',
		),
	);


	$GLOBALS[cfg][abs_root_url]		= 'http://www.ourapp.com/';
	$GLOBALS[cfg][safe_abs_root_url]	= $GLOBALS[cfg][abs_root_url];


	$GLOBALS[cfg][smarty_compile] = 1;
?>