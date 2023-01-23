<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

$day_back = Helper::getConfig( 'sub_graph_fill_day_back', 2 );
if ( GraphJob::fill_sub_graph( $day_back ) )
{
	echo '<span style="color:green;font-weight:bold;">Sub Graph Filled!</span>';
}