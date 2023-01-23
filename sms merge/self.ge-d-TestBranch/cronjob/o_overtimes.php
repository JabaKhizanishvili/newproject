<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
require_once PATH_BASE . DS . 'libraries/x.php';
if ( Request::getVar( 'SERVER_ADDR', null, 'server' ) != Request::getVar( 'REMOTE_ADDR', null, 'server' ) )
{
	die( 'Remote Call' );
}
$st = microtime( 1 );
$ConfigPath = PATH_BASE . DS . 'config';
$Configs = Folder::files( $ConfigPath, '\.php$' );
$CURL = new XMultiCurl();
$CURL->setTimeout( 3 );
echo '<pre>';
$CURL->complete( function ( XCurl $instance )
{
	/** @var XCurlCaseInsensitiveArray $requestHeaders */
	$requestHeaders = $instance->requestHeaders;
	echo 'After Overtimes Response : ';
	echo strtoupper( $requestHeaders->offsetGet( 'host' ) ) . ' - ';
	echo trim( $instance->getResponse() );
	echo PHP_EOL . PHP_EOL;
	flush();
} );
foreach ( $Configs as $Conf )
{
	$N = file::stripExt( $Conf );
	$Domain = 'https://' . $N . '.self.ge/';
	$URL = $Domain . 'cron/o_overtimes.php';
	$CURL->addGet( $URL );
}
$CURL->start();

sleep( 2 );

$CURL2 = new XMultiCurl();
$CURL2->setTimeout( 3 );
echo '<pre>';
$CURL2->complete( function ( XCurl $instance )
{
	/** @var XCurlCaseInsensitiveArray $requestHeaders */
	$requestHeaders = $instance->requestHeaders;
	echo 'Before Overtimes Response : ';
	echo strtoupper( $requestHeaders->offsetGet( 'host' ) ) . ' - ';
	echo trim( $instance->getResponse() );
	echo PHP_EOL . PHP_EOL;
	flush();
} );
foreach ( $Configs as $Conf )
{
	$N = file::stripExt( $Conf );
	$Domain = 'https://' . $N . '.self.ge/';
	$URL = $Domain . 'cron/b_overtimes.php';
	$CURL2->addGet( $URL );
}
$CURL2->start();
echo '</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;

