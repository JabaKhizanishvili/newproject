<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

//Configuration
//$Hour = (int) Helper::getConfig( 'assignment_hour' );
//$HourNow = PDate::Get()->toFormat( '%H' );
//if ( $Hour > $HourNow )
//{
//	die( 'Not In Hour!' );
//}

$config_days = Helper::getConfig( 'standard_graph_days', 7 );

if ( GraphJob::standard_to_standard_change( $config_days ) )
{
	echo '<pre><pre>';
	print_r( 'Graph Data Updated!' );
	echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
}

if ( GraphJob::insert_standard_graph_data( $config_days ) )
{
	echo '<pre><pre>';
	print_r( 'Standard Graphs Inserted!' );
	echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
}

if ( GraphJob::updateGraphTimeData() )
{
	echo '<pre><pre>';
	print_r( 'Graph Data Updated!' );
	echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
}

if ( GraphJob::updateStandardGraphData( $config_days ) )
{
	echo '<pre><pre>';
	print_r( 'Graph Data Updated!' );
	echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
}

if ( GraphJob::dinamic_to_standard_change( $config_days ) )
{
	echo '<pre><pre>';
	print_r( 'Graph Data Updated!' );
	echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
}

if ( GraphJob::calculusTypeRegime_CheckUpdate() )
{
	echo '<pre><pre>';
	print_r( 'Graph Data Updated!' );
	echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
}