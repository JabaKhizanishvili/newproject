<?php
define( 'X_MINIMUM_PHP', '5.3.10' );
if ( version_compare( PHP_VERSION, X_MINIMUM_PHP, '<' ) )
{
	die( 'Your host needs to use PHP ' . X_MINIMUM_PHP . ' or higher to run this version of XCMS!' );
}
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
define( 'DS', DIRECTORY_SEPARATOR );
$Base = str_replace( DS . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
$Admin = basename( dirname( __FILE__ ) );
define( 'X_PATH_ROOT', $Base );
define( 'X_PATH_BASE', $Base );
define( 'PATH_BASE', $Base );
// Saves the start time and memory usage.
$StartTime = microtime( 1 );
$StartMemory = memory_get_usage();
require_once X_PATH_ROOT . DS . 'define.php';
require_once X_PATH_LIBRARIES . DS . 'x.php';
require_once X_PATH_ROOT . DS . 'multi.php';
require_once X_PATH_ROOT . DS . 'components' . DS . 'helper.php';

/** @var XLogger $Logger */
$Logger = XLogger::GetInstance();
$Logger->SetLogDir( PATH_LOGS . DS . 'APIRequests' );
$Logger->SetLogFile( 'API' );
$Post = Request::get( 'post' );
if ( isset( $Post['PICTURE'] ) )
{
	$Post['PICTURE'] = '';
}

$Logger->Logwrite( URI::getInstance()->toString() );
$Logger->Logwrite( print_r( $Post, true ) );
$Logger->Logwrite( print_r( Request::get( 'get' ), true ) );

ignore_user_abort( true );
ob_start();
$Controler = new XApiHelper();
$Controler->execute();
$GLOBAL_CONTENT = ob_get_contents();
ob_clean();
echo $GLOBAL_CONTENT;
