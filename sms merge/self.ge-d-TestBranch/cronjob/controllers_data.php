<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');

require_once PATH_BASE . DS . 'libraries/x.php';
$st = microtime( true );
if ( Request::getVar( 'SERVER_ADDR', 0, 'server' ) != Request::getVar( 'REMOTE_ADDR', 1, 'server' ) )
{
	die( 'Remote Call' );
}
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
	flush();
	flush();
} );
foreach ( $Configs as $Conf )
{
	$N = file::stripExt( $Conf );
	$Domain = 'https://' . $N . '.self.ge/';
	$URL = $Domain . 'cron/controllers_data.php';
	$CURL->addGet( $URL );
}
$CURL->start();

echo '</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;

