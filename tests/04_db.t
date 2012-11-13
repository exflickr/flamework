<?
	include(dirname(__FILE__).'/wrapper.php');

	plan(25);



	#
	# create a test table
	#

	$name = "test_table_".time();

	$sql = "CREATE TABLE `{$name}` (";
	$sql .= "`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, ";
	$sql .= "`data` VARCHAR( 255 ) NOT NULL, ";
	$sql .= "INDEX ( `data` )) ENGINE = INNODB";

	$ret = db_write($sql);

	is($ret['ok'], 1, "Created test table");



	#
	# insert a row
	#

	$ret = db_insert($name, array(
		'data'	=> AddSlashes('foo'),
	));

	is($ret['ok'], 1, "Insert a row");
	is($ret['affected_rows'], 1, "Insert: affected rows");
	is($ret['insert_id'], 1, "Insert: insert ID");


	#
	# update row
	#

	$ret = db_update($name, array(
		'data'	=> AddSlashes('bar'),
	), "id=9");

	is($ret['ok'], 1, "Update a missing row");
	is($ret['affected_rows'], 0, "Update no rows");


	$ret = db_update($name, array(
		'data'  => AddSlashes('baz'),
	), "id=1");

	is($ret['ok'], 1, "Update a real row");
	is($ret['affected_rows'], 1, "Update one row");


	#
	# bulk insert
	#

	$rows = array();
	$rows[] = array( 'data' => 'a1' );
	$rows[] = array( 'data' => 'a2' );
	$rows[] = array( 'data' => 'a3' );
	$rows[] = array( 'data' => 'a4' );
	$rows[] = array( 'data' => 'a5' );

	$ret = db_insert_bulk($name, $rows, 3);

	is($ret['ok'], 1, "Bulk insert ok");
	is($ret['affected_rows'], 5, "Inserted 5 rows");


	#
	# simple read
	#

	$ret = db_fetch("SELECT * FROM {$name} ORDER BY data DESC");

	is($ret['ok'], 1, "Fetch ok");
	is($ret['cluster'], 'main', "Expected cluster");
	is(count($ret['rows']), 6, "Six rows returned");
	is(count($ret['rows'][0]), 2, "Two fields per row");
	is($ret['rows'][0]['data'], 'baz', "Expected data");


	#
	# db_list reads
	#

	list($num) = db_list(db_fetch("SELECT COUNT(*) FROM {$name}"));
	is($num, 6, "DB fetch in list context");


	#
	# db_single read
	#

	$row = db_single(db_fetch("SELECT * FROM {$name} ORDER BY data ASC"));

	is(is_array($row), true, "Array is returned by db_single()");
	is(count($row), 2, "Two fields returned");
	is($row['id'], 2, "ID of row");
	is($row['data'], 'a1', "Row data");


	#
	# bulk insert correctly discards extra fields after row 1
	#

	$ret = db_insert_bulk($name, array(
		array( 'data' => 'a6' ),
		array( 'woo' => 'yay' ),
	));

	is($ret['ok'], 1, "Bulk field filtering test");
	is($ret['affected_rows'], 2, "Two rows inserted");

	$row = db_single(db_fetch("SELECT * FROM {$name} ORDER BY id DESC LIMIT 1"));

	is($row['id'], 8, "Second bulk row is #8");
	is($row['data'], '', "Data field is empty");


	#
	# cleanup
	#

	$ret = db_write("DROP TABLE {$name}");
	is($ret['ok'], 1, "Dropped test table");

	


# TO BE ADDED:
# ignore mode
# insert_dupe
# escaping functions
# fetch paginated
# logging stuff (counters, timers, actual log lines?)
# _db_comment_query
# things that really sohuld fail
