<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$K = Request::GetVar( 'l', 1 );
$D = Request::GetVar( 'd', 0 );
if ( $D )
{
	DB::Query( 'truncate table slf_translate_index ' );
}
if ( TranslateIndex::insert_xml_collection( $K ) )
{
	echo '<span style="color:green;font-weight:bold;">Translations Inserted!</span>';
}