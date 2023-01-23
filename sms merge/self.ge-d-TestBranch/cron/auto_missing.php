<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$L = Request::getInt( 'l', 0 );
$on_off = (int) Helper::getConfig( 'missing_app_auto_confirm_msg' );
if ( empty( $on_off ) )
{
	return false;
}

$to_chief = (int) Helper::getConfig( 'missing_app_auto_confirm_msg_to_chief' );
$percent = (int) Helper::getConfig( 'missing_app_auto_confirm_percent' );
if ( empty( $percent ) )
{
	$percent = 80;
}

$day_back = (int) Helper::getConfig( 'missing_app_day_back' );
if ( empty( $day_back ) )
{
	$day_back = 2;
}
if ( $L )
{
	$day_back = $L;
}

if ( XMissing::auto_insert( $day_back, $percent, $to_chief ) )
{
	echo '<span style="color:green;font-weight:bold;">Auto missings registered!</span>';
}
