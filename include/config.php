<?
	$GLOBALS[cfg] = array();

	$GLOBALS[cfg][db_main] = array(
		'host'	=> 'localhost',
		'user'	=> 'www-rw',
		'pass'	=> 'PASSWORD',
		'name'	=> 'NAME',
	);

	$GLOBALS[cfg][db_users] = array(
		'host' => array(
			1 => 'localhost',
			2 => 'localhost',
		),
		'user' => 'www-rw',
		'pass' => 'PASSWORD',
		'name' => array(
			1 => 'user1',
			2 => 'user2',
		),
	);


	$GLOBALS[cfg][abs_root_url]		= 'http://www.ourapp.com/';
	$GLOBALS[cfg][safe_abs_root_url]	= $GLOBALS[cfg][abs_root_url];


	$GLOBALS[cfg][smarty_compile] = 1;
?>