<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'PATH_BASE', dirname( '../../index.php' ) );
error_reporting( E_ALL );
ini_set( 'error_log', PATH_BASE . DS . 'logs' . DS . 'api.scanner.log.txt' );
session_start();

require_once PATH_BASE . DS . 'define.php';
require_once X_PATH_LIBRARIES . DS . 'x.php';
require_once PATH_BASE . DS . 'multi.php';
include(dirname( __FILE__ ) . DS . 'helper.php');
include(dirname( __FILE__ ) . DS . 'response.php');
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

$Query = 'select trim(pp.permit_id)  permit_id
  from slf_persons w
	left join  rel_person_permit pp on pp.person = w.id
 where w.active = 1
   and w.permit_id is not null 
UNION ALL 
  SELECT
	t.CODE
FROM
	lib_visitors t
WHERE
	t.active >-1 '
;

$Data = DB::LoadList( $Query );
$requestContentType = 'application/json';
if ( count( $Data ) )
{
	$StatusCode = 200;
	$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
	echo json_encode( $Data );
}
else
{
	$StatusCode = 404;
	$RestResponse->setHttpHeaders( $requestContentType, $StatusCode );
	echo '[]';
}
