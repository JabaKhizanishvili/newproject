<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

//Configuration
$Hour = (int) str_replace( ':', '', Helper::getConfig( 'SalarySheet_work_start' ) );
$HourNow = (int) PDate::Get()->toFormat( '%H00' );
if ( $Hour > $HourNow )
{
	die( 'Not In Hour!' );
}

if ( DailySalary::GeneratePeriods() )
{
	echo '<span style="color:green;font-weight:bold;">Periods Updated!</span>';
}