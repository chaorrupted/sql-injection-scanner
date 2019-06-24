<?php


#--sanitizers--
	$F_SECURING_SQL = array(
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




#--sinks--

$NAME_DATABASE = 'SQL Injection';
	$F_DATABASE = array(
	// Abstraction Layers
		'dba_open'						=> array(array(1), array()),
		'dba_popen'						=> array(array(1), array()),
		'dba_insert'					=> array(array(1,2), array()),
		'dba_fetch'						=> array(array(1), array()),
		'dba_delete'					=> array(array(1), array()),
		'dbx_query'						=> array(array(2), $F_SECURING_SQL),
		'odbc_do'						=> array(array(2), $F_SECURING_SQL),
		'odbc_exec'						=> array(array(2), $F_SECURING_SQL),
		'odbc_execute'					=> array(array(2), $F_SECURING_SQL),
	// Vendor Specific
		'db2_exec' 						=> array(array(2), $F_SECURING_SQL),
		'db2_execute'					=> array(array(2), $F_SECURING_SQL),
		'fbsql_db_query'				=> array(array(2), $F_SECURING_SQL),
		'fbsql_query'					=> array(array(1), $F_SECURING_SQL),
		'ibase_query'					=> array(array(2), $F_SECURING_SQL),
		'ibase_execute'					=> array(array(1), $F_SECURING_SQL),
		'ifx_query'						=> array(array(1), $F_SECURING_SQL),
		'ifx_do'						=> array(array(1), $F_SECURING_SQL),
		'ingres_query'					=> array(array(2), $F_SECURING_SQL),
		'ingres_execute'				=> array(array(2), $F_SECURING_SQL),
		'ingres_unbuffered_query'		=> array(array(2), $F_SECURING_SQL),
		'msql_db_query'					=> array(array(2), $F_SECURING_SQL),
		'msql_query'					=> array(array(1), $F_SECURING_SQL),
		'msql'							=> array(array(2), $F_SECURING_SQL),
		'mssql_query'					=> array(array(1), $F_SECURING_SQL),
		'mssql_execute'					=> array(array(1), $F_SECURING_SQL),
		'mysql_db_query'				=> array(array(2), $F_SECURING_SQL),
		'mysql_query'					=> array(array(1), $F_SECURING_SQL),
		'mysql_unbuffered_query'		=> array(array(1), $F_SECURING_SQL),
		'mysqli_stmt_execute'			=> array(array(1), $F_SECURING_SQL),
		'mysqli_query'					=> array(array(2), $F_SECURING_SQL),
		'mysqli_real_query'				=> array(array(1), $F_SECURING_SQL),
		'mysqli_master_query'			=> array(array(2), $F_SECURING_SQL),
		'oci_execute'					=> array(array(1), array()),
		'ociexecute'					=> array(array(1), array()),
		'ovrimos_exec'					=> array(array(2), $F_SECURING_SQL),
		'ovrimos_execute'				=> array(array(2), $F_SECURING_SQL),
		'ora_do'						=> array(array(2), array()),
		'ora_exec'						=> array(array(1), array()),
		'pg_query'						=> array(array(2), $F_SECURING_SQL),
		'pg_send_query'					=> array(array(2), $F_SECURING_SQL),
		'pg_send_query_params'			=> array(array(2), $F_SECURING_SQL),
		'pg_send_prepare'				=> array(array(3), $F_SECURING_SQL),
		'pg_prepare'					=> array(array(3), $F_SECURING_SQL),
		'sqlite_open'					=> array(array(1), $F_SECURING_SQL),
		'sqlite_popen'					=> array(array(1), $F_SECURING_SQL),
		'sqlite_array_query'			=> array(array(1,2), $F_SECURING_SQL),
		'arrayQuery'					=> array(array(1,2), $F_SECURING_SQL),
		'singleQuery'					=> array(array(1), $F_SECURING_SQL),
		'sqlite_query'					=> array(array(1,2), $F_SECURING_SQL),
		'sqlite_exec'					=> array(array(1,2), $F_SECURING_SQL),
		'sqlite_single_query'			=> array(array(2), $F_SECURING_SQL),
		'sqlite_unbuffered_query'		=> array(array(1,2), $F_SECURING_SQL),
		'sybase_query'					=> array(array(1), $F_SECURING_SQL),
		'sybase_unbuffered_query'		=> array(array(1), $F_SECURING_SQL)
	);


/*

#--sources--
	public static $V_USERINPUT = array(
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
		'$argv'
	);

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

	// database content as input
	public static $F_DATABASE_INPUT = array(
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
		'sqlite_fetch_string'
	);

	// other functions as input
	public static $F_OTHER_INPUT = array(
		'get_headers',
		'getallheaders',
		'get_browser',
		'getenv',
		'gethostbyaddr',
		'runkit_superglobals',
		'import_request_variables'
	);
 */
print_r($F_DATABASE['dbx_query']);
