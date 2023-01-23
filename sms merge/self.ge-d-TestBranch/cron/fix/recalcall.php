<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( dirname( dirname( __FILE__ ) ) ) );

define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
set_time_limit( 0 );
echo '<pre><pre>';

$st = microtime( 1 );

$Date = Request::getVar( 'd', false );
if ( $Date )
{
	$Params = array(
			':p_date_start' => PDate::Get( $Date )->toFormat( '%Y-%m-%d' ),
			':p_date_end' => PDate::Get( $Date )->toFormat( '%Y-%m-%d' )
	);
	DB::callProcedure( 'ReCalcAll', $Params );
}

echo PHP_EOL . 'All Done!</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
