<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');

require_once PATH_BASE . DS . 'libraries/x.php';

$Languages = Language::getInstance( 'ka-ge' );
$Strings = $Languages->GetStrings();

$Translations = Folder::files( X_PATH_TRANSLATE, '.', true, true );
echo '<pre>Count: <pre>';
print_r( count( $Strings ) );
echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";

echo '<pre><pre>';
$K = 1;
foreach ( $Strings as $String )
{
	echo $String . ' - ';
	echo XTranslate::_( $String, 'langfile', 'ka' ) . PHP_EOL;
	if ( $K > 1000 )
	{
//		die;
	}
	$K++;
	flush();
}
echo 'Done!' . PHP_EOL;
