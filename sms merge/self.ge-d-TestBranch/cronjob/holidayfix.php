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
$CURL = new XCurl();
$CURL->setTimeout( 2 );

foreach ( $Configs as $Conf )
{
	$N = file::stripExt( $Conf );
	$Domain = 'https://' . $N . '.self.ge/';
	$URL = $Domain . 'cron/fix/holiday.php';
	$CURL->get( $URL );
	echo strtoupper( $N ) . ' Response : ';
	echo trim( $CURL->getResponse() );
	echo PHP_EOL . PHP_EOL;
}