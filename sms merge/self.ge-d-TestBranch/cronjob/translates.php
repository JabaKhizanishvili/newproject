<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');

require_once PATH_BASE . DS . 'libraries/x.php';
$Start = microtime( true );
$Translations = Folder::files( X_PATH_TRANSLATE, '.', true, true );

foreach ( $Translations as $File )
{
	$Content = json_decode( file_get_contents( $File ) );
	$Translate = XTranslate::GetAPITranslate( C::_( 'Input', $Content ), C::_( 'To', $Content ), 'langfile' );
	XTranslate::SetLocalTranslate( C::_( 'Hash', $Content ), C::_( 'To', $Content ), $Translate );
	echo C::_( 'Hash', $Content ) . PHP_EOL;
}
echo 'Done!' . PHP_EOL;
$End = microtime( true ) - $Start;
echo 'Exec Time: ' . $End . PHP_EOL;
