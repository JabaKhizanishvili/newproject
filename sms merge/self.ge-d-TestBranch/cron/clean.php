<?php
set_time_limit( 0 );
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
defined( 'PATH_BASE' ) or define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'error_log', PATH_BASE . DS . 'logs' . DS . 'certificates.log.txt' );
require_once (PATH_BASE . DS . 'define.php');

require_once(PATH_BASE . DS . 'include.php');
global $count;
$count = 0;
$UserLogDir = X_PATH_LOGS . DS . X_DOMAIN;

class XClean
{
	/**
	 * Wrapper for the standard file_exists function
	 *
	 * @param string XClean name relative to installation dir
	 * @return boolean True if path is a folder
	 * @since 1.5
	 */
	function exists( $path )
	{
		return is_dir( self::clean( $path ) );

	}

	function clean( $pathIN, $ds = DS )
	{
		// Remove double slashes and backslahses and convert all slashes and backslashes to DS
		$path = preg_replace( '#[/\\\\]+#', $ds, trim( $pathIN ) );
		return $path;

	}

	function CleanFiles( $PathIn, $filter = '.', $exclude = array( '.svn', 'CVS' ) )
	{
		$arr = array();
		global $count;
		// Check to make sure the path valid and clean
		$Path = self::clean( $PathIn );
		// Is the path a folder?
		if ( !is_dir( $Path ) )
		{
			return false;
		}
		// read the source directory
		$handle = opendir( $Path );
		while ( ($file = readdir( $handle )) !== false )
		{
			if ( ($file != '.') && ($file != '..') && (!in_array( $file, $exclude )) )
			{
				$dir = $Path . DS . $file;
				$isDir = is_dir( $dir );
				if ( $isDir )
				{
					$arr = XClean::CleanFiles( $dir, $filter, $exclude );
					rmdir( $Path . DS . $file );
				}
				else
				{
					if ( preg_match( "/$filter/", $file ) )
					{
						unlink( $Path . DS . $file );
					}
				}
			}
			flush();
			$count++;
		}
		closedir( $handle );
		$count++;

	}

	function makeSafe( $path )
	{
		$ds = (DS == '\\') ? '\\' . DS : DS;
		$regex = array( '#[^A-Za-z0-9:\_\-' . $ds . ' ]#' );
		return preg_replace( $regex, '', $path );

	}

}

XClean::CleanFiles( $UserLogDir, 'Login\.log' );
flush();
XClean::CleanFiles( $UserLogDir, 'index\.log\.txt' );
flush();
XClean::CleanFiles( $UserLogDir . DS . 'IndexTableLock' );
flush();
$DitExclude = PDate::Get()->toFormat( '%Y%m%d' );
XClean::CleanFiles( $UserLogDir . DS . 'LatenessSMS', '.', [ $DitExclude ] );
flush();
XClean::CleanFiles( $UserLogDir . DS . 'Worker' );
flush();
XClean::CleanFiles( $UserLogDir . DS . 'Reader' );
flush();
XClean::CleanFiles( $UserLogDir . DS . 'logs' );
flush();
XClean::CleanFiles( $UserLogDir . DS . 'APIRequests' );
flush();
//lock-2022-10-03
$LockExclude = 'lock-' . PDate::Get()->toFormat( '%Y-%m-%d' );
XClean::CleanFiles( $UserLogDir . DS . 'ContractsAlertsLock', '.', [ $LockExclude ] );

//XClean::CleanFiles( PATH_BASE . DS . 'img' );
echo 'Data Cleaned!' . PHP_EOL;
