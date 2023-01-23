<?php
/*
 * Set System Variables
 */
require_once PATH_BASE . DS . 'vendor/autoload.php';

if ( XFile::exists( X_PATH_BASE . DS . 'Sentry.php' ) )
{
	require_once X_PATH_BASE . DS . 'Sentry.php';
	$DSN = XSentry::GetDSN();
}
else
{
	$DSN = 'https://5d1b45d427d24cfaa6940d25c951b09e@logs.self.ge/22';
}
if ( XFile::exists( X_PATH_BASE . DS . 'Release.php' ) )
{
	require_once X_PATH_BASE . DS . 'Release.php';
	$Release = XRelease::GetRelease();
	if ( $Release == '{RELEASE}' )
	{
		$URI = XUri::getInstance();
		$Release = $URI->toString( [ 'host' ] );
	}
}
else
{
	$Release = 'Self.Ge';
}


Sentry\init(
				[
						'dsn' => $DSN,
						'traces_sample_rate' => 0.2,
						'release' => $Release
				]
);
$URI = URI::getInstance();
$Domain = mb_strtolower( trim( C::_( '2', array_reverse( explode( '.', $URI->getHost() ) ) ) ) );
if ( empty( $Domain ) )
{
//	\Sentry\captureMessage( 'Error Processing Domain Name!' );
	header( 'Location: https://hrms.self.ge/?ref=www.Self.Ge' );
	die;
}
$ConfigPath = CONFIG_DIR . DS . $Domain . '.php';
if ( File::exists( $ConfigPath ) )
{
	require_once $ConfigPath;
}
else
{
//	\Sentry\captureMessage( 'Config File Not Exists for Domain Name!' );
	header( 'Location: https://hrms.self.ge/?ref=www.Self.Ge' );
	die;
}

defined( 'X_DOMAIN' ) or define( 'X_DOMAIN', $Domain );
defined( 'MULTY_SYSTEM' ) or define( 'MULTY_SYSTEM', 0 );
defined( 'GRAPH_FREE_EDIT' ) or define( 'GRAPH_FREE_EDIT', 0 );

defined( 'X_PATH_TEMPLATE' ) or define( 'X_PATH_TEMPLATE', PATH_BASE . DS . 'templates' );
defined( 'X_TEMPLATE' ) or define( 'X_TEMPLATE', 'templates' );
defined( 'X_PATH_OVERRIDE' ) or define( 'X_PATH_OVERRIDE', PATH_BASE . DS . 'override' );
defined( 'X_PATH_TMP' ) or define( 'X_PATH_TMP', X_PATH_BUFFER . DS . 'tmp' . DS . $Domain );
defined( 'X_PATH_TMP_URL' ) or define( 'X_PATH_TMP_URL', 'buffer/tmp/' . $Domain . '/' );
defined( 'X_EXPORT_DIR' ) or define( 'X_EXPORT_DIR', X_PATH_BUFFER . DS . 'export' . DS . $Domain );
defined( 'X_EXPORT_URL' ) or define( 'X_EXPORT_URL', 'buffer/export/' . $Domain );

if ( !Folder::exists( PATH_UPLOAD ) )
{
	Folder::create( PATH_UPLOAD, 0777 );
}
if ( !Folder::exists( PATH_UPLOADS ) )
{
	Folder::create( PATH_UPLOADS, 0777 );
}
if ( !Folder::exists( PATH_LOGS ) )
{
	Folder::create( PATH_LOGS, 0777 );
}
if ( !Folder::exists( X_PATH_TMP ) )
{
	Folder::create( X_PATH_TMP, 0777 );
}
if ( !Folder::exists( X_EXPORT_DIR ) )
{
	Folder::create( X_EXPORT_DIR, 0777 );
}

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
ini_set( 'error_log', PATH_LOGS . DS . 'index.log.txt' );

defined( 'PAYROLL' ) or define( 'PAYROLL', 0 );
