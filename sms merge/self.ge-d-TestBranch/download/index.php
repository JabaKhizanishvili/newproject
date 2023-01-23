<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'PATH_BASE', dirname( dirname( __FILE__ ) ) );
define( 'NO_CLEAN', 1 );

session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
global $DisableSF;
$DisableSF = true;
include(PATH_BASE . DS . 'libraries' . DS . 'MimeTypes.php');
$C = ob_get_clean();
//Init User
Users::InitUser();
if ( Users::isLogged() )
{
	$File = Request::getVar( 'f' );
	$FileName = urldecode( Request::getVar( 'name' ) );
	$fullPath = PATH_UPLOADS . DS . $File;
	if ( is_file( $fullPath ) )
	{
		$fsize = filesize( $fullPath );
		$path_parts = pathinfo( $fullPath );
		$ext = mb_strtolower( $path_parts["extension"] );
		if ( !$FileName )
		{
			if ( strlen( $path_parts["basename"] ) > 33 )
			{
				$FileName = substr( $path_parts["basename"], 33 );
			}
			else
			{
				$FileName = $path_parts["basename"];
			}
		}
		else
		{
			$FileName = $FileName . '.' . $ext;
		}
		$Mime = XMime::_( $ext );

		switch ( $ext )
		{
			case "pdf":
				header( 'Content-type: ' . $Mime );
				header( "Content-Disposition: inline; filename=\"" . $FileName . "\"" );
				break;
//			case "php":
//			case "html":
//			case "xml":
//				header( "Content-Disposition: attachment; filename=\"" . $FileName . "\"" );
//				header( "Content-type: application/octet-stream" );
//				break;
			default:
				header( "Content-Disposition: attachment; filename=\"" . $FileName . "\"" );
				header( 'Content-type: ' . $Mime );
				break;
		}
		if ( $fsize )
		{//checking if file size exist
			header( "Content-length: $fsize" );
		}
		readfile( $fullPath );
		exit;
	}
}
else
{
	Users::Redirect( URI::base() . '?ref=download' );
}

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
