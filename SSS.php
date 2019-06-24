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
		'$_GET',
		'$_POST',
		'$_COOKIE',
		'$_REQUEST',
		'$_FILES',
		'$_SERVER',
		'$HTTP_GET_VARS',
		'$HTTP_POST_VARS',
		'$HTTP_COOKIE_VARS',
		'$HTTP_REQUEST_VARS',
		'$HTTP_POST_FILES',
		'$HTTP_SERVER_VARS',
		'$HTTP_RAW_POST_DATA',
		'$argc',
		'$argv',
		'mysql_fetch_array',
		'mysql_fetch_assoc',
		'mysql_fetch_field',
		'mysql_fetch_object',
		'mysql_fetch_row',
		'pg_fetch_all',
		'pg_fetch_array',
		'pg_fetch_assoc',
		'pg_fetch_object',
		'pg_fetch_result',
		'pg_fetch_row',
		'sqlite_fetch_all',
		'sqlite_fetch_array',
		'sqlite_fetch_object',
		'sqlite_fetch_single',
		'sqlite_fetch_string',
		'get_headers',
		'getallheaders',
		'get_browser',
		'getenv',
		'gethostbyaddr',
		'runkit_superglobals',
		'import_request_variables',
	);

/*
	public static $V_SERVER_PARAMS = array(
		'HTTP_ACCEPT',
		'HTTP_ACCEPT_LANGUAGE',
		'HTTP_ACCEPT_ENCODING',
		'HTTP_ACCEPT_CHARSET',
		'HTTP_CONNECTION',
		'HTTP_HOST',
		'HTTP_KEEP_ALIVE',
		'HTTP_REFERER',
		'HTTP_USER_AGENT',
		'HTTP_X_FORWARDED_FOR',
		// all HTTP_ headers can be tainted
		'PHP_AUTH_DIGEST',
		'PHP_AUTH_USER',
		'PHP_AUTH_PW',
		'AUTH_TYPE',
		'QUERY_STRING',
		'REQUEST_METHOD',
		'REQUEST_URI', // partly urlencoded
		'PATH_INFO',
		'ORIG_PATH_INFO',
		'PATH_TRANSLATED',
		'REMOTE_HOSTNAME',
		'PHP_SELF'
	);

	// file content as input
	public static $F_FILE_INPUT = array(
		'bzread',
		'dio_read',
		'exif_imagetype',
		'exif_read_data',
		'exif_thumbnail',
		'fgets',
		'fgetss',
		'file',
		'file_get_contents',
		'fread',
		'get_meta_tags',
		'glob',
		'gzread',
		'readdir',
		'read_exif_data',
		'scandir',
		'zip_read'
	);
 */

