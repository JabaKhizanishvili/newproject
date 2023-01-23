<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');

require_once PATH_BASE . DS . 'libraries/x.php';
$st = microtime( true );
$ConfigPath = PATH_BASE . DS . 'config';
$Configs = Folder::files( $ConfigPath, '\.php$' );
$CURL = new XMultiCurl();
$CURL->setTimeout( 3 );
$CURL->complete( function ( XCurl $instance )
{
	echo '<pre>';
	/** @var XCurlCaseInsensitiveArray $requestHeaders */
	$requestHeaders = $instance->requestHeaders;
	echo 'DB Tables Update Response : ';
	echo strtoupper( $requestHeaders->offsetGet( 'host' ) ) . ' - ';
	echo trim( $instance->getResponse() );
	echo '</pre>';
	flush();
	flush();
	flush();
} );
foreach ( $Configs as $Conf )
{
	$N = file::stripExt( $Conf );
	$Domain = 'https://' . $N . '.self.ge/';
	$URL = $Domain . 'tables/update.php?del=1';
	$CURL->addGet( $URL );
}
$CURL->start();

$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
