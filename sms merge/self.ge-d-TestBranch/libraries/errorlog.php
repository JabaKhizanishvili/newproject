<?php
defined( 'SEND_ERROR_EMAIL' ) or define( 'SEND_ERROR_EMAIL', 1 );
require_once 'file.php';
require_once 'folder.php';
error_reporting( E_ALL );
function my_error_handler( $number, $message, $file, $line, $vars )
{
	$ignore = array(
//			2048 => 2048,
	);
	if ( isset( $ignore[$number] ) )
	{
		return;
	}
	$data = '[' . date( "Y-m-d H:i:s" ) . '] Number: ' . $number . ' ' . $message . ' in ' . $file . ' on line ' . $line . PHP_EOL;
	file_put_contents( PATH_LOGS . DS . 'my.errors.log', $data, FILE_APPEND );
	SendErrorData( 'Number: ' . $number . ' ' . $message . ' in ' . $file . ' on line ' . $line . "\n" );

}

// We should use our custom function to handle errors.  
set_error_handler( 'my_error_handler' );
//trigger_error( 'asdadas' );
/**
 * 
 * @param type $message
 */
function SendErrorData( $message )
{
	if ( SEND_ERROR_EMAIL != 1 )
	{
		return true;
	}
	$Hash = md5( $message );
	$Folder = PATH_LOGS . DS . 'errors';
	if ( !Folder::exists( $Folder ) )
	{
		Folder::create( $Folder, 0777 );
	}
	$File = $Folder . DS . $Hash;
	if ( !is_file( $File ) )
	{
		$to = 't.kevlishvili@self.ge';
		$Subject = 'Error In SMS System';
		Cmail( $to, $Subject, $message );
		file_put_contents( $File, $message );
	}
	return true;

}
