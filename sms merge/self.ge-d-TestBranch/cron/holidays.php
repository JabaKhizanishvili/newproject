<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
$st = microtime( 1 );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

echo 'Run Holidays Set Script<pre>' . PHP_EOL;
$LimitsTable = new HolidayLimitsTable();

$LimitsTable->Generate();
$LimitsTable->Generate( -1, true );
$LimitsTable->SyncHolidayLimits();
echo '</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;

