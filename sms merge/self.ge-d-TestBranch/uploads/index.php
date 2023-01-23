<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'PATH_BASE', dirname( dirname( __FILE__ ) ) );
define( 'NO_CLEAN', 1 );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
global $DisableSF;
$DisableSF = true;

//Init User
// A list of permitted file extensions
$disabled = array( 'php', 'html' );
$Response = new stdClass();
file_put_contents( PATH_LOGS . DS . 'UPData.log.txt', print_r( $_FILES, 1 ) );
if ( isset( $_FILES['upl'] ) && $_FILES['upl']['error'] == 0 )
{
	$extension = pathinfo( $_FILES['upl']['name'], PATHINFO_EXTENSION );
	if ( in_array( mb_strtolower( $extension ), $disabled ) )
	{
		$Response->status = 'error';
		$Response->alert = Text::_( 'File Type Not Alowed!' );
		echo json_encode( $Response );
		exit;
	}
	$FileName = Helper::makeSafe( md5( microtime() . '|||dsasdasdasdas' ) . '_' . Collection::get( 'upl.name', $_FILES ) );
	$TMPFileName = Collection::get( 'upl.tmp_name', $_FILES );
	if ( move_uploaded_file( $TMPFileName, PATH_UPLOADS . DS . $FileName ) )
	{
//		rename
		$Response->status = 'success';
		$Response->file = $FileName;
		echo json_encode( $Response );
		exit;
	}
}
$Response->status = 'error';
$Response->alert = Text::_( 'System Error!' );
echo json_encode( $Response );
exit;
