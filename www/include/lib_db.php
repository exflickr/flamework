<?php

	$GLOBALS['db_conns'] = array();

	$GLOBALS['timings']['db_conns_count']	= 0;
	$GLOBALS['timings']['db_conns_time']	= 0;
	$GLOBALS['timings']['db_queries_count']	= 0;
	$GLOBALS['timings']['db_queries_time']	= 0;
	$GLOBALS['timings']['db_rows_count']	= 0;
	$GLOBALS['timings']['db_rows_time']	= 0;

	$GLOBALS['timing_keys']['db_conns']	= 'DB Connections';
	$GLOBALS['timing_keys']['db_queries']	= 'DB Queries';
	$GLOBALS['timing_keys']['db_rows']	= 'DB Rows Returned';

	#################################################################

	function db_init(){

		if (!function_exists('mysql_connect')){
			die("[lib_db] requires the mysql PHP extension\n");
		}

		#
		# connect to the main cluster immediately so that we can show a
		# downtime notice it's it's not available? you might not want to
		# so this - depends on whether you can ever stand the main cluster
		# being down.
		#

		if ($GLOBALS['cfg']['db_main']['auto_connect']){
			_db_connect('main', null);
		}
	}

	#################################################################

	#
	# These are just shortcuts to the real functions which allow
	# us to skip passing the cluster name. these are the only functions
	# we should call from outside the library.
	#
	# In this example we have 2 cluster - one monolith called 'main' and
	# one partitioned/sharded cluster called 'users' When making calls
	# to the sharded cluster, we need to pass the shard number as the first
	# argument.
	#

	function db_insert($tbl, $hash){			return _db_insert($tbl, $hash, 'main', null); }
	function db_insert_users($k, $tbl, $hash){		return _db_insert($tbl, $hash, 'users', $k); }

	function db_insert_bulk($tbl, $rows, $batch=100){	return _db_insert_bulk($tbl, $rows, $batch, 'main', null); }
	function db_insert_bulk_users($tbl, $rows, $batch=100){	return _db_insert_bulk($tbl, $rows, $batch, 'users', $k); }

	function db_insert_dupe($tbl, $hash, $hash2, $more=array()){		return _db_insert_dupe($tbl, $hash, $hash2, 'main', null, $more); }
	function db_insert_dupe_users($k, $tbl, $hash, $hash2, $more=array()){	return _db_insert_dupe($tbl, $hash, $hash2, 'users', $k, $more); }

	function db_update($tbl, $hash, $where){		return _db_update($tbl, $hash, $where, 'main', null); }
	function db_update_users($k, $tbl, $hash, $where){	return _db_update($tbl, $hash, $where, 'users', $k); }

	function db_fetch($sql){				return _db_fetch($sql, 'main', null); }
	function db_fetch_slave($sql){				return _db_fetch_slave($sql, 'main_slaves'); }
	function db_fetch_users($k, $sql){			return _db_fetch($sql, 'users', $k); }

	function db_fetch_paginated($sql, $args){		return _db_fetch_paginated($sql, $args, 'main', null); }
	function db_fetch_paginated_users($k, $sql, $args){	return _db_fetch_paginated($sql, $args, 'users', $k); }

	function db_write($sql){				return _db_write($sql, 'main', null); }
	function db_write_users($k, $sql){			return _db_write($sql, 'users', $k); }

	function db_tickets_write($sql){			return _db_write($sql, 'tickets', null); }

	#################################################################

	function _db_connect($cluster, $shard){

		$cluster_key = _db_cluster_key($cluster, $shard);

		$host = $GLOBALS['cfg']["db_{$cluster}"]["host"];
		$user = $GLOBALS['cfg']["db_{$cluster}"]["user"];
		$pass = $GLOBALS['cfg']["db_{$cluster}"]["pass"];
		$name = $GLOBALS['cfg']["db_{$cluster}"]["name"];

		if ($shard){
			$host = $host[$shard];
			$name = $name[$shard];
		}

		if (!$host){
			log_fatal("no such cluster: ".$cluster);
		}


		#
		# try to connect
		#

		$start = microtime_ms();

		$GLOBALS['db_conns'][$cluster_key] = @mysql_connect($host, $user, $pass, 1);

		if ($GLOBALS['db_conns'][$cluster_key]){

			@mysql_select_db($name, $GLOBALS['db_conns'][$cluster_key]);
			@mysql_query("SET character_set_results='utf8', character_set_client='utf8', character_set_connection='utf8', character_set_database='utf8', character_set_server='utf8'", $GLOBALS['db_conns'][$cluster_key]);
		}

		$end = microtime_ms();


		#
		# log
		#

		log_notice('db', "DB-$cluster_key: Connect", $end-$start);

		if (!$GLOBALS['db_conns'][$cluster_key] || $GLOBALS['cfg']['admin_flags_no_db']){

			log_fatal("Connection to database cluster '$cluster_key' failed");
		}

		$GLOBALS['timings']['db_conns_count']++;
		$GLOBALS['timings']['db_conns_time'] += $end-$start;

		#
		# profiling?
		#

		if ($GLOBALS['cfg']['db_profiling']){
			@mysql_query("SET profiling = 1;", $GLOBALS['db_conns'][$cluster_key]);
		}
	}

	#################################################################

	function _db_query($sql, $cluster, $shard){

		$cluster_key = _db_cluster_key($cluster, $shard);

		if (!$GLOBALS['db_conns'][$cluster_key]){
			_db_connect($cluster, $shard);
		}

		$trace = _db_callstack();
		$use_sql = _db_comment_query($sql, $trace);

		$start = microtime_ms();
		$result = @mysql_query($use_sql, $GLOBALS['db_conns'][$cluster_key]);
		$end = microtime_ms();

		$GLOBALS['timings']['db_queries_count']++;
		$GLOBALS['timings']['db_queries_time'] += $end-$start;

		log_notice('db', "DB-$cluster_key: $sql ($trace)", $end-$start);


		#
		# profiling?
		#

		$profile = null;

		if ($GLOBALS['cfg']['db_profiling']){
			$profile = array();
			$p_result = @mysql_query("SHOW PROFILE ALL", $GLOBALS['db_conns'][$cluster_key]);
			while ($p_row = mysql_fetch_array($p_result, MYSQL_ASSOC)){
				$profile[] = $p_row;
			}
		}


		#
		# build result
		#

		if (!$result){
			$error_msg	= mysql_error($GLOBALS['db_conns'][$cluster_key]);
			$error_code	= mysql_errno($GLOBALS['db_conns'][$cluster_key]);

			log_error("DB-$cluster_key: $error_code ".HtmlSpecialChars($error_msg));

			$ret = array(
				'ok'		=> 0,
				'error'		=> $error_msg,
				'error_code'	=> $error_code,
				'sql'		=> $sql,
				'cluster'	=> $cluster,
				'shard'		=> $shard,
			);
		}else{
			$ret = array(
				'ok'		=> 1,
				'result'	=> $result,
				'sql'		=> $sql,
				'cluster'	=> $cluster,
				'shard'		=> $shard,
			);
		}

		if ($profile) $ret['profile'] = $profile;

		return $ret;
	}

	#################################################################

	function _db_insert($tbl, $hash, $cluster, $shard){

		$fields = array_keys($hash);

		return _db_write("INSERT INTO $tbl (`".implode('`,`',$fields)."`) VALUES ('".implode("','",$hash)."')", $cluster, $shard);
	}

	#################################################################

	function _db_insert_dupe($tbl, $hash, $hash2, $cluster, $shard, $more=array()){

		$fields = array_keys($hash);

		$bits = array();

		# This bit ensures that the correct value gets stuck in to
		# MySQL's 'insert_id' slot. This might be important if you
		# are using a ticket server to generate an ID before you've
		# tried to stick anything in the database. It is left as an
		# exercise to the calling function to check/update the row's
		# ID by reading $rsp['insert_id']. For example:
		# 
		# $widget = ...
		# $insert == ...
		# $dupe == ...
		#		
		# $more = array(
		# 	'ensure_insert_id' => 'id'
		# );
		# 
		# $rsp = db_insert_dupe("Widgets", $insert, $dupe, $more);
		# 
		# if ($rsp['ok']){
		# 
		# 	if ($id = $rsp['insert_id']){
		# 		$widget['id'] = $id;
		# 	}
		# 
		# 	$rsp['widget'] = $widget;
		# }
		# 
		# (20140618/straup)
		#
		# See also:
		# http://mikefenwick.com/blog/insert-into-database-or-return-id-of-duplicate-row-in-mysql/

		if (isset($more['ensure_insert_id'])){
			$id = $more['ensure_insert_id'];
			$bits[] = "`{$id}`=LAST_INSERT_ID(`{$id}`)";
		}

		foreach(array_keys($hash2) as $k){
			$bits[] = "`$k`='$hash2[$k]'";
		}

		return _db_write("INSERT INTO $tbl (`".implode('`,`',$fields)."`) VALUES ('".implode("','",$hash)."') ON DUPLICATE KEY UPDATE ".implode(', ',$bits), $cluster, $shard);
	}

	#################################################################

	function _db_insert_bulk($tbl, $hashes, $batch_size, $cluster, $shard){

		$a = array_keys($hashes);
		$a = array_shift($a);

		$first_row = $hashes[$a];
		$fields = array_keys($first_row);

		$flags = $GLOBALS['db_flags']['insert_ignore'] ? ' IGNORE' : '';

		$acc_rows = 0;

		while (count($hashes)){

			$use = array_slice($hashes, 0, $batch_size);
			$hashes = array_slice($hashes, $batch_size);

			$all_values = array();

			foreach ($use as $hash){
				$values = array();
				foreach ($fields as $k) $values[] = "'" . $hash[$k] . "'";
				$all_values[] = '('.implode(',', $values).')';
			}

			$all_values = implode(', ', $all_values);

			$ret = _db_write("INSERT{$flags} INTO $tbl (`".implode('`,`',$fields)."`) VALUES $all_values", $cluster, $shard);

			if (!$ret['ok']) return $ret;

			$acc_rows += $ret['affected_rows'];
		}

		return array(
			'ok'		=> 1,
			'affected_rows'	=> $acc_rows,
		);
	}

	#################################################################

	function _db_update($tbl, $hash, $where, $cluster, $shard){

		$bits = array();
		foreach(array_keys($hash) as $k){
			$bits[] = "`$k`='$hash[$k]'";
		}

		return _db_write("UPDATE $tbl SET ".implode(', ',$bits)." WHERE $where", $cluster, $shard);
	}

	#################################################################

	function db_escape_like($string){
		return str_replace(array('%','_'), array('\\%','\\_'), $string);
	}

	function db_escape_rlike($string){
		return preg_replace("/([.\[\]*^\$()])/", '\\\$1', $string);
	}

	#################################################################

	function _db_fetch_slave($sql, $cluster){

		$cluster_key = _db_cluster_key($cluster, null);

		$slaves = array_keys($GLOBALS['cfg'][$cluster_key]['host']);

		shuffle($slaves);
		shuffle($slaves);

		return _db_fetch($sql, $cluster, $slaves[0]);
	}

	#################################################################

	function _db_fetch($sql, $cluster, $shard){

		$ret = _db_query($sql, $cluster, $shard);

		if (!$ret['ok']) return $ret;

		$out = $ret;
		$out['ok'] = 1;
		$out['rows'] = array();
		unset($out['result']);

		$start = microtime_ms();
		$count = 0;
		while ($row = mysql_fetch_array($ret['result'], MYSQL_ASSOC)){
			$out['rows'][] = $row;
			$count++;
		}
		$end = microtime_ms();
		$GLOBALS['timings']['db_rows_count'] += $count;
		$GLOBALS['timings']['db_rows_time'] += $end-$start;

		return $out;
	}

	#################################################################

	function _db_fetch_paginated($sql, $args, $cluster, $shard){

		#
		# Setup some defaults
		#

		$page		= isset($args['page'])		? max(1, $args['page'])		: 1;
		$per_page	= isset($args['per_page'])	? max(1, $args['per_page'])	: $GLOBALS['cfg']['pagination_per_page'];
		$spill		= isset($args['spill'])		? max(0, $args['spill'])	: $GLOBALS['cfg']['pagination_spill'];

		if ($spill >= $per_page) $spill = $per_page - 1;


		#
		# If we're using the 2-query method, get the count first
		#

		$calc_found_rows = !!$args['calc_found_rows'];

		if (!$calc_found_rows){

			$count_sql = _db_count_sql($sql, $args);
			$ret = _db_fetch($count_sql, $cluster, $shard);
			if (!$ret['ok']) return $ret;

			$total_count = intval(array_pop($ret['rows'][0]));
			$page_count = ceil($total_count / $per_page);
		}


		#
		# generate limit values
		#

		$start = ($page - 1) * $per_page;
		$limit = $per_page;

		if ($calc_found_rows){

			$limit += $spill;

		}else{

			$last_page_count = $total_count - (($page_count - 1) * $per_page);

			if ($last_page_count <= $spill && $page_count > 1){
				$page_count--;
			}

			if ($page == $page_count){
				$limit += $spill;
			}

			if ($page > $page_count){
				# we do this to ensure we fetch no rows if we're asking for the
				# page after the last one, else we might end up with some spill
				# being returned.
				$start = $total_count + 1;
			}
		}


		#
		# build sql
		#

		$sql .= " LIMIT $start, $limit";

		if ($calc_found_rows){

			$sql = preg_replace('/^\s*SELECT\s+/', 'SELECT SQL_CALC_FOUND_ROWS ', $sql);
		}

		$ret = _db_fetch($sql, $cluster, $shard);


		#
		# figure out paging if we're using CALC_FOUND_ROWS
		#

		if ($calc_found_rows){

			$ret2 = _db_fetch("SELECT FOUND_ROWS()", $cluster, $shard);

			$total_count = intval(array_pop($ret2['rows'][0]));
			$page_count = ceil($total_count / $per_page);

			$last_page_count = $total_count - (($page_count - 1) * $per_page);

			if ($last_page_count <= $spill && $page_count > 1){
				$page_count--;
			}

			if ($page > $page_count){
				$ret['rows'] = array();
			}
			if ($page < $page_count){
				$ret['rows'] = array_slice($ret['rows'], 0, $per_page);
			}
		}


		#
		# add pagination info to result
		#

		$ret['pagination'] = array(
			'total_count'	=> $total_count,
			'page'		=> $page,
			'per_page'	=> $per_page,
			'page_count'	=> $page_count,
			'first'		=> $start+1,
			'last'		=> $start+count($ret['rows']),
		);

		if (!count($ret['rows'])){
			$ret['pagination']['first'] = 0;
			$ret['pagination']['last'] = 0;
		}
		
		if ($GLOBALS['cfg']['pagination_assign_smarty_variable']){
			$GLOBALS['smarty']->assign('pagination', $ret['pagination']);
		}

		return $ret;
	}

	#################################################################

	function _db_count_sql($sql, $args){

		# remove any ORDER'ing & LIMIT'ing
		$sql = preg_replace('/ ORDER BY .*$/', '', $sql);
		$sql = preg_replace('/ LIMIT .*$/', '', $sql);

		# transform the select portion
		if (isset($args['count_fields'])){

			$sql = preg_replace('/^SELECT (.*?) FROM/i', "SELECT COUNT({$args['count_fields']}) FROM", $sql);
		}else{
			$sql = preg_replace_callback('/^SELECT (.*?) FROM/i', '_db_count_sql_from', $sql);
		}

		return $sql;
	}

	function _db_count_sql_from($m){

		return "SELECT COUNT($m[1]) FROM";
	}

	#################################################################

	function _db_write($sql, $cluster, $shard){

		$cluster_key = _db_cluster_key($cluster, $shard);

		$ret = _db_query($sql, $cluster, $shard);

		if (!$ret['ok']) return $ret;

		return array(
			'ok'		=> 1,
			'affected_rows'	=> mysql_affected_rows($GLOBALS['db_conns'][$cluster_key]),
			'insert_id'	=> mysql_insert_id($GLOBALS['db_conns'][$cluster_key]),
		);
	}

	#################################################################

	function _db_comment_query($sql, $trace){

		$debug = $_SERVER['SCRIPT_NAME'].": ".$trace;
		$debug = str_replace('*', '?', $debug); # just incase there is '*/' in the debug message

		return "/* $debug */ $sql";
	}

	#################################################################

	function _db_callstack(){

		#
		# get the backtrace, minus any functions that starts with db_ or _db_
		#

		$trace = debug_backtrace();

		while (substr($trace[0]['function'], 0, 3) == 'db_' || substr($trace[0]['function'], 0, 4) == '_db_'){
			array_shift($trace);
		}


		#
		# full stack?
		#

		if ($GLOBALS['cfg']['db_full_callstack']){

			$items = array();

			foreach($trace as $t){
				$items[] = $t['function'].'()';
			}

			if (!count($items)) return '_global_';

			return implode(' -> ', array_reverse($items));
		}


		#
		# single
		#

		return $trace[0]['function'] ? $trace[0]['function'].'()' : '_global_';
	}

	#################################################################

	function db_single($ret){
		return $ret['ok'] && count($ret['rows']) ? $ret['rows'][0] : FALSE;
	}

	function db_list($ret){
		return $ret['ok'] && count($ret['rows']) ? array_values($ret['rows'][0]) : FALSE;
	}

	#################################################################

	function _db_disconnect($cluster, $shard=null){

		$cluster_key = _db_cluster_key($cluster, $shard);

		if (is_resource($GLOBALS['db_conns'][$cluster_key])){
			@mysql_close($GLOBALS['db_conns'][$cluster_key]);
		}

		unset($GLOBALS['db_conns'][$cluster_key]);
	}


	function db_disconnect_all(){

		foreach ($GLOBALS['db_conns'] as $cluster_key => $conn){

			if (is_resource($conn)){
				@mysql_close($conn);
			}

			unset($GLOBALS['db_conns'][$cluster_key]);
		}
	}

	#################################################################

	function _db_cluster_key($cluster, $shard){

		return $shard ? "{$cluster}-{$shard}" : $cluster;
	}

	#################################################################

	function db_ping($cluster, $shard=null){

		$cluster_key = _db_cluster_key($cluster, $shard);

		if (is_resource($GLOBALS['db_conns'][$cluster_key])){

			$start = microtime_ms();
			$ret = @mysql_ping($GLOBALS['db_conns'][$cluster_key]);
			$end = microtime_ms();

			log_notice('db', "DB-$cluster_key: Ping", $end-$start);

			return $ret;
		}

		return FALSE;
	}

	#################################################################