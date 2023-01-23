<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

//Configuration
$Hour = (int) Helper::getConfig( 'assignment_hour' );
$HourNow = PDate::Get()->toFormat( '%H' );
if ( $Hour > $HourNow )
{
	die( 'Not In Hour!' );
}

if ( Job::Assignment([]) )
{
	echo '<span style="color:green;font-weight:bold;">Assignment Done!</span>';
}

if ( Job::Change() )
{
	echo '<span style="color:green;font-weight:bold;">Changes Done!</span>';
}

if ( Job::ScheduleChange() )
{
	echo '<span style="color:green;font-weight:bold;">Schedule Changes Done!</span>';
}

if ( Job::Release() )
{
	echo '<span style="color:green;font-weight:bold;">Releases Done!</span>';
}
