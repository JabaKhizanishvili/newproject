<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( dirname( dirname( __FILE__ ) ) ) );
global $GLOBALTIME;
$GLOBALTIME = microtime( true );
define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( true );

$Config = Helper::getConfig( 'before_autoovertime_on' );
if ( $Config )
{
	die( 'Before Overtimes Enabled By Config!' );
}
echo '<pre>';
$Query = 'delete hrs_applications a where a.type = ' . APP_BEFORE_AUTO_OVERTIME;
echo 'Delete Overtime Applications : ';
var_dump( DB::CallStatement( $Query ) );
echo PHP_EOL;

echo '</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
