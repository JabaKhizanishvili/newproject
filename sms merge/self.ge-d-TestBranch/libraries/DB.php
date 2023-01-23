<?php

/**
 * Description of DB
 *
 * @author Teimuraz Kevlishvili
 */
class DB
{
	public static $debug = array();
	public static $debugTime = array();
	//private static $instance = false;
	// OCI Database Instance
	public static $instance = false;
	public static $_slow_log = true;
	public static $_slow_log_time = 0.5;
	public static $Results = array();

	/*
	 * Returns instance of OCI Database , creates if necesary
	 */
	public static function getInstance()
	{
		static $First = true;
		if ( DB::$instance === false )
		{
			try
			{
				DB::$instance = oci_connect( DB_USER, DB_PASS, DB_CONN_STRING, 'UTF8' );
			}
			catch ( Exception $e )
			{
				die( $e->getMessage() );
			}
			if ( !DB::$instance )
			{
				$e = oci_error();
				die( $e['message'] );
			}
		}
		if ( $First )
		{
			$Q = 'ALTER SESSION SET NLS_DATE_FORMAT = \'YYYY-MM-DD HH24:MI:SS\'';
			$start = microtime( true );
			self::$debug[] = $Q;
			$stmt = oci_parse( DB::$instance, $Q );
			try
			{
				$Result = oci_execute( $stmt, OCI_DEFAULT );
				DB::$Results[] = $Result;
			}
			catch ( Exception $exc )
			{
				echo '<pre>';
				print_r( $Q );
				echo '</pre>';
				echo '<pre>';
				print_r( $exc->getTraceAsString() );
				echo '</pre>';
				self::$debugTime[] = microtime( true ) - $start;
				return false;
			}
			self::$debugTime[] = microtime( true ) - $start;
			$First = false;
		}
		return DB::$instance;

	}

	/*
	 * 
	 * Takes $sql statement and $values containing key => value binding params
	 * Returns Array (succes,data)
	 *  success - boolean - true/false , if query executed
	 *  data- mixed - array of rows on success-true , error info on succes-false
	 */
	public static function qin( $sql, $values = Array() )
	{
		DB::$debug[] = $sql;
		$database = DB::getInstance();
		$query = oci_parse( $database, $sql );
		foreach ( $values as $key => $val )
		{
			oci_bind_by_name( $query, $key, $val, 512 );
		}
		if ( !oci_execute( $query ) )
		{
			$errors = oci_error( $query );
			return Array(
					'success' => false,
					'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message']
			);
		}
		$result = Array();
		oci_fetch_all( $query, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW );
		return Array( 'success' => true, 'data' => $result );

	}

	/*
	 * Takes sql statement and $values , 
	 * returns boolean , succes/failure of execute 
	 * ( eg UPDATE , INSERT ... )
	 */
	public static function qout( $sql, $values = Array() )
	{
		$database = DB::getInstance();
		$query = oci_parse( $database, $sql );
		foreach ( $values as $key => $val )
		{
			oci_bind_by_name( $query, $key, $val, 512 );
		}
		if ( !oci_execute( $query ) )
		{
			$errors = oci_error( $query );
			return Array(
					'success' => false,
					'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message']
			);
		}
		return Array( 'success' => true, 'data' => '' );

	}

	/*
	 * Call the procedure , 
	 * return array of success/true , 
	 * and params filled up with OUT data ( if available )
	 */
	public static function callProcedure( $procedure, $values = Array() )
	{
		DB::$debug[] = $procedure;
		$database = DB::getInstance();
		$sql = '';
		$keys = array_keys( $values );
		if ( sizeof( $values ) > 0 )
		{
			$sql = 'BEGIN ' . $procedure . '(' . implode( ',', $keys ) . '); END;';
		}
		else
		{
			$sql = 'BEGIN ' . $procedure . '; END;';
		}
		$query = oci_parse( $database, $sql );
		foreach ( $keys as $key )
		{
			oci_bind_by_name( $query, $key, $values[$key], 512 );
		}
		if ( !oci_execute( $query ) )
		{
			$errors = oci_error( $query );
			echo '<pre>';
			print_r( $errors );
			echo '</pre>';
			die;
		}
		return Array( 'success' => true, 'data' => '', 'params' => $values );

	}

	/*
	 * Call the procedure , 
	 * return array of success/true , 
	 * and params filled up with OUT data ( if available )
	 */
	public static function CallStatement( $Statement )
	{
		$start = microtime( true );
		DB::$debug[] = $Statement;
		$database = DB::getInstance();
		$query = oci_parse( $database, $Statement );
		$Result = oci_execute( $query );
		self::$debugTime[] = microtime( true ) - $start;
		if ( !$Result )
		{
			$errors = oci_error( $query );
			echo '<pre>';
			print_r( $errors );
			echo '</pre>';
			return false;
		}
		return true;

	}

	/*
	 * Call the function and , 
	 * return arra of success/true , 
	 * data returned by function and params filled up with OUT data ( if available )
	 */
	public static function callFunction( $procedure, $values = Array() )
	{
		$start = microtime( true );
		DB::$debug[] = $procedure;
		$database = DB::getInstance();
		$sql = '';
		$keys = array_keys( $values );
		$result = NULL;
		if ( sizeof( $values ) > 0 )
		{
			$sql = 'BEGIN :callFunctionRes := '
							. $procedure
							. '(' . implode( ',', $keys )
							. '); END;';
		}
		else
		{
			$sql = 'BEGIN :callFunctionRes := '
							. $procedure
							. '; END;';
		}

		$query = oci_parse( $database, $sql );

		oci_bind_by_name( $query, ':callFunctionRes', $result, 512 );

		foreach ( $keys as $key )
		{
			oci_bind_by_name( $query, $key, $values[$key], 512 );
		}

		if ( !oci_execute( $query ) )
		{
			return false;
		}
		self::$debugTime[] = microtime( true ) - $start;
		return $result;

	}

	/*
	 * Call the function and , 
	 * return arra of success/true , 
	 * data returned by function and params filled up with OUT data 
	 * ( if available )
	 */
	public static function callCursorFunction( $procedure, $values = Array(), $Rkey = null )
	{
		$start = microtime( true );
		DB::$debug[] = $procedure;
		$database = DB::getInstance();
		$sql = '';
		$keys = array_keys( $values );
		$p_cursor = oci_new_cursor( $database );
		if ( sizeof( $values ) > 0 )
		{
			$sql = 'BEGIN :callFunctionRes := '
							. $procedure
							. '(' . implode( ',', $keys )
							. '); END;';
		}
		else
		{
			$sql = 'BEGIN :callFunctionRes := ' . $procedure . '; END;';
		}
		$query = oci_parse( $database, $sql );
		oci_bind_by_name( $query, ':callFunctionRes', $p_cursor, -1, OCI_B_CURSOR );
		foreach ( $keys as $key )
		{
			oci_bind_by_name( $query, $key, $values[$key], 512 );
		}
		if ( !oci_execute( $query ) )
		{
			$errors = oci_error( $query );
			return Array(
					'success' => false,
					'data' => 'Error : ' . $errors['code'] . ' => ' . $errors['message'],
					'params' => $values
			);
		}
		oci_execute( $p_cursor );
		$result = Array();
		while ( $row = oci_fetch_array( $p_cursor, OCI_ASSOC + OCI_RETURN_NULLS ) )
		{
			if ( $Rkey && isset( $row[$Rkey] ) )
			{
				$result[$row[$Rkey]] = (object) $row;
			}
			else
			{
				array_push( $result, (object) $row );
			}
		}
		self::$debugTime[] = microtime( true ) - $start;
		return Array( 'success' => true, 'data' => $result, 'params' => $values );

	}

	public static function LoadObjectList( $query, $key = false )
	{
		if ( $key )
		{
			$key = strtoupper( $key );
		}
		$database = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		$res_arr = array();

		$s = oci_parse( $database, $query );
		try
		{
			oci_execute( $s, OCI_DEFAULT );
		}
		catch ( Exception $exc )
		{
			echo '<pre>';
			print_r( $exc );
			echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . PHP_EOL;
			die;
			echo $exc->getTraceAsString();
		}
		while ( $row = oci_fetch_array( $s, OCI_ASSOC + OCI_RETURN_NULLS ) )
		{
			if ( $key && isset( $row[$key] ) )
			{
				$res_arr[$row[$key]] = (object) $row;
			}
			else
			{
				array_push( $res_arr, (object) $row );
			}
		}

		self::$debugTime[] = microtime( true ) - $start;
		return $res_arr;

	}

	public static function LoadObject( $query )
	{
		$database = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		$s = oci_parse( $database, $query );
		oci_execute( $s, OCI_DEFAULT );
		$row = oci_fetch_array( $s, OCI_ASSOC + OCI_RETURN_NULLS );
		self::$debugTime[] = microtime( true ) - $start;
		if ( $row )
		{
			return (object) $row;
		}
		return false;

	}

	/**
	 * 
	 * @param String $query
	 * @param Boolean $key
	 * @return Array
	 */
	public static function LoadList( $query, $key = false )
	{
		$database = DB::getInstance();
		$start = microtime( true );
		$res_arr = array();
		DB::$debug[] = $query;
		$s = oci_parse( $database, $query );
		oci_execute( $s, OCI_DEFAULT );
		while ( $row = oci_fetch_row( $s ) )
		{
			if ( isset( $row[0] ) )
			{
				if ( $key )
				{
					$res_arr[$row[0]] = $row[0];
				}
				else
				{
					array_push( $res_arr, $row[0] );
				}
			}
		}
		self::$debugTime[] = microtime( true ) - $start;
		return $res_arr;

	}

	public static function Update( $query )
	{
		$db_conn = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		try
		{
			$stmt = oci_parse( $db_conn, $query );
			$r = oci_execute( $stmt, OCI_DEFAULT );
			if ( !$r )
			{
				oci_rollback( $db_conn );
				return false;
			}
			oci_commit( $db_conn );
			self::$debugTime[] = microtime( true ) - $start;
			return true;
		}
		catch ( Exception $exc )
		{
			echo '<pre>';
			print_r( $query );
			echo '</pre>';
			echo $exc->getTraceAsString();
		}
		self::$debugTime[] = microtime( true ) - $start;
		return false;

	}

	public static function Delete( $query )
	{
		$db_conn = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		$stmt = oci_parse( $db_conn, $query );
		if ( oci_execute( $stmt, OCI_DEFAULT ) )
		{
			oci_commit( $db_conn );
			self::$debugTime[] = microtime( true ) - $start;
			return true;
		}
		self::$debugTime[] = microtime( true ) - $start;
		return false;

	}

	public static function Insert( $query, $key = false )
	{
		$db_conn = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		$return = '';
		$id = true;
		if ( $key )
		{
			$return = ' returning ' . $key . ' into :new_id';
		}
		try
		{
			$stmt = oci_parse( $db_conn, $query . $return );
			if ( $key )
			{
				oci_bind_by_name( $stmt, ":NEW_ID", $id, 32 );
			}
			$r = oci_execute( $stmt, OCI_DEFAULT );
			if ( !$r )
			{
				oci_rollback( $db_conn );
				return false;
			}
		}
		catch ( Exception $exc )
		{
			echo '<pre>';
			print_r( $query );
			echo '</pre>';
			echo '<pre>';
			print_r( $exc->getTraceAsString() );
			echo '</pre>';
			self::$debugTime[] = microtime( true ) - $start;
			die;
			return false;
		}
		oci_commit( $db_conn );
		self::$debugTime[] = microtime( true ) - $start;
		return $id;

	}

	public static function Query( $query )
	{
		$db_conn = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		$stmt = oci_parse( $db_conn, $query );
		try
		{
			$Result = oci_execute( $stmt, OCI_DEFAULT );
			DB::$Results[] = $Result;
		}
		catch ( Exception $exc )
		{
			echo '<pre>';
			print_r( $query );
			echo '</pre>';
			echo '<pre>';
			print_r( $exc->getTraceAsString() );
			echo '</pre>';
			self::$debugTime[] = microtime( true ) - $start;
			return false;
		}
		self::$debugTime[] = microtime( true ) - $start;
		return true;

	}

	public static function LoadResult( $query )
	{
		$database = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		try
		{
			$s = oci_parse( $database, $query );
			oci_execute( $s, OCI_DEFAULT );
			$row = oci_fetch_row( $s );
		}
		catch ( Exception $exc )
		{
			$row = array();
		}
		self::$debugTime[] = microtime( true ) - $start;
		if ( isset( $row[0] ) )
		{
			return $row[0];
		}
		return false;

	}

	public static function Quote( $data )
	{
		return '\'' . str_replace( "'", "\'", $data ) . '\'';

	}

	public static function InsertAll( $query )
	{
		$db_conn = DB::getInstance();
		$start = microtime( true );
		DB::$debug[] = $query;
		$Result = false;
		try
		{
			$stmt = oci_parse( $db_conn, $query );
			$Result = oci_execute( $stmt, OCI_COMMIT_ON_SUCCESS );
		}
		catch ( Exception $exc )
		{
			echo '<pre>';
			print_r( $query );
			echo '</pre>';
			echo '<pre>';
			print_r( $exc->getTraceAsString() );
			echo '</pre>';
			self::$debugTime[] = microtime( true ) - $start;
			return false;
		}
		self::$debugTime[] = microtime( true ) - $start;
		return $Result;

	}

	public function __destruct()
	{
		$db_conn = DB::getInstance();
		oci_close( $db_conn );

	}

	public static function DB_Destruct()
	{
		if ( !array_key_exists( 'REQUEST_METHOD', $_SERVER ) )
		{
			die();
		}
		if ( DB::$_slow_log )
		{
			$Logger = new XLogger();
			$Logger->SetLogDir( X_PATH_BASE . DS . 'logs' . DS . 'SlowLog' );
			$Logger->SetLogFile( 'Slow' );
			$URI = URI::getInstance();
			$URL = $URI->toString( array( 'path', 'query', 'fragment' ) );
			foreach ( DB::$debugTime as $Key => $Time )
			{
				if ( $Time >= DB::$_slow_log_time )
				{
					$Logger->Logwrite( $Time . ' |$| ' . $URL . ' |$| ' . preg_replace( '/[\s]+/i', ' ', C::_( $Key, DB::$debug ) ) );
				}
			}
		}

	}

}

register_shutdown_function( 'DB_Destruct' );
function DB_Destruct()
{
	if ( !array_key_exists( 'REQUEST_METHOD', $_SERVER ) )
	{
		die();
	}
	if ( DB::$_slow_log )
	{
		$Logger = new XLogger();
		$Logger->SetLogDir( X_PATH_BASE . DS . 'logs' . DS . 'SlowLog' );
		$Logger->SetLogFile( 'Slow' );
		$URI = URI::getInstance();
		$URL = $URI->toString( array( 'path', 'query', 'fragment' ) );
		foreach ( DB::$debugTime as $Key => $Time )
		{
			if ( $Time >= DB::$_slow_log_time )
			{
				$Logger->Logwrite( $Time . ' |$| ' . $URL . ' |$| ' . preg_replace( '/[\s]+/i', ' ', C::_( $Key, DB::$debug ) ) );
			}
		}
	}

}
