<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');

require_once PATH_BASE . DS . 'libraries/x.php';
if ( Request::getVar( 'SERVER_ADDR', null, 'server' ) != Request::getVar( 'REMOTE_ADDR', null, 'server' ) )
{
//	die( 'Remote Call' );
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
	echo 'After Sequence Fix Response : ';
	echo strtoupper( $requestHeaders->offsetGet( 'host' ) ) . ' - ';
	echo trim( $instance->getResponse() );
	echo PHP_EOL . PHP_EOL;
	flush();
} );
foreach ( $Configs as $Conf )
{
	$N = file::stripExt( $Conf );
	$Domain = 'https://' . $N . '.self.ge/';
	$URL = $Domain . 'cron/fix/sequencefix.php';
	$CURL->addGet( $URL );
}
$CURL->start();
