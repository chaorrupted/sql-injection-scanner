<?php

	$sanitizers = array(
		'addslashes', #f
		'dbx_escape_string', #f
		'db2_escape_string', #f
		'ingres_escape_string', #f
		'maxdb_escape_string', #f
		'maxdb_real_escape_string', #f
		'mysql_escape_string', #f
		'mysql_real_escape_string', #f
		'mysqli_escape_string', #f
		'mysqli_real_escape_string', #f
		'pg_escape_string', #f
		'pg_escape_bytea', #f
		'sqlite_escape_string',
		'sqlite_udf_encode_binary',
		'cubrid_real_escape_string',
	);




	$sinks = array(
		'dba_open',						
		'dba_popen',						
		'dba_insert',					
		'dba_fetch',						
		'dba_delete',					
		'dbx_query',						
		'odbc_do',						
		'odbc_exec',						
		'odbc_execute',					
		'db2_exec', 						
		'db2_execute',					
		'msql_db_query',					
		'msql_query',					
		'msql',							
		'mssql_query',					
		'mssql_execute',					
		'mysql_db_query',				
		'mysql_query',					
		'mysql_unbuffered_query',		
		'mysqli_stmt_execute',			
		'mysqli_query',					
		'mysqli_real_query',				
		'mysqli_master_query',
	        #mysql num rows	
	);




	$sources = array(
		'$_GET',#array
		'$_POST',#array
		'$_COOKIE',#array
		'$_REQUEST',#array
		'$_FILES',#array
		'$_SERVER',#array
		#'$argc',
		#'$argv',
		#'pg_fetch_all',
		#'pg_fetch_array',
		#'pg_fetch_assoc',
		#'pg_fetch_object',
		#'pg_fetch_result',
		#'pg_fetch_row',
		#'sqlite_fetch_all',
		#'sqlite_fetch_array',
		#'sqlite_fetch_object',
		#'sqlite_fetch_single',
		#'sqlite_fetch_string',
		#'get_headers',
		#'getallheaders',
		#'get_browser',
		#'getenv',
		#'gethostbyaddr',
		#'runkit_superglobals',
		#'import_request_variables',
	);

