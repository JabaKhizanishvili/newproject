<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'PATH_BASE', dirname( '../../index.php' ) );
error_reporting( E_ALL );
ini_set( 'error_log', PATH_BASE . DS . 'logs' . DS . 'api.scanner.log.txt' );
session_start();
include(dirname( __FILE__ ) . DS . 'helper.php');
include(dirname( __FILE__ ) . DS . 'response.php');
require_once PATH_BASE . DS . 'define.php';
require_once X_PATH_LIBRARIES . DS . 'x.php';

require_once PATH_BASE . DS . 'multi.php';

//Init User
// logging
$log = new Logger();
$log->SetLogFile( 'RestRequests' );
$log->SetLogDir( PATH_LOGS . DS . 'Reader' . DS );
$logBuffer = "\n";
$logBuffer .= print_r( $_POST, true );
$logBuffer .= '= = = = = = = = = = = = = = = = = = = = = = = =' . "\n";
$log->lwrite( $logBuffer );
$RestResponse = new RestResponse();
$Key = '315a48ba-52bf-4a0a-936e-e67032df85ed';
$requestContentType = 'text/html';
define( 'TIME_DELAY_LIMIT', 60 * 3 );
$StatusCode = 500;
$Data = Request::get( 'post', array() );
$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
if ( count( $Data ) )
{
	$Hash = C::_( 'hash', $Data );
	if ( empty( $Hash ) )
	{
		$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
		exit;
	}
	$RowData = trim( C::_( 'data', $Data ) );

	if ( empty( $RowData ) )
	{
		$StatusCode = 200;
		$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
		exit;
	}
	$CalculatedHash = md5( $RowData . '|' . $Key );

	if ( $CalculatedHash != $Hash )
	{
		$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
		exit;
	}

	$Rows = explode( '|', $RowData );
	foreach ( $Rows as $Row )
	{
		$Content = explode( ',', $Row );
		APIReaderHelper::insertRecord( $Content );
	}
	$StatusCode = 200;
	$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
	echo 'Done!';
}
else
{
	$StatusCode = 404;
	$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
	?>
	<html>
		<head>
			<title>REST Server</title>
		</head>
		<body class="btn-primary">
			<h1>REST Server</h1>
			<p>It's Works!!!</p>
		</body>
	</html>
	<?php
}