<?php

	$sanitizers = array(
		'addslashes',
		'dbx_escape_string',
		'db2_escape_string',
		'ingres_escape_string',
		'maxdb_escape_string',
		'maxdb_real_escape_string',
		'mysql_escape_string',
		'mysql_real_escape_string',
		'mysqli_escape_string',
		'mysqli_real_escape_string',
		'pg_escape_string',
		'pg_escape_bytea',
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
	);




	$sources = array(
		'$_GET',#array
		'$_POST',#array
		'$_COOKIE',#array
		'$_REQUEST',#array
		'$_FILES',#array
		'$_SERVER',#array
		'$HTTP_GET_VARS',#array
		'$HTTP_POST_VARS',#array
		'$HTTP_COOKIE_VARS',#array
		'$HTTP_REQUEST_VARS',#array
		'$HTTP_POST_FILES',#array
		'$HTTP_SERVER_VARS',#array
		'$HTTP_RAW_POST_DATA',#array
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

