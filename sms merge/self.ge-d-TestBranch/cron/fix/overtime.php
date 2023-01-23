<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( dirname( dirname( __FILE__ ) ) ) );

define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

echo '<pre><pre>';

$st = microtime( 1 );
$Query = 'select a.id, a.worker,
       (select min(w. id)
          from slf_worker w
         where w.orgpid = a.worker
           and w.active = 1
         group by w.orgpid) worker_id
  from hrs_applications a
  left join  (select min(w. id) idx, w.orgpid
          from slf_worker w
         where w.active = 1
         group by w.orgpid)  k on k.idx = a.worker
 where a.type in (11, 13, 14)
   and a.worker_id = 0
';
$Data = DB::LoadObjectList( $Query );
echo '<pre>';
foreach ( $Data as $App )
{
	$ID = C::_( 'ID', $App );
	$ORGPID = C::_( 'WORKER_ID', $App );
	if ( empty( $ORGPID ) )
	{
		continue;
	}
	$Query2 = 'update '
					. ' hrs_applications a '
					. ' set '
					. ' a.worker_id = ' . $ORGPID
					. ' where a.id = ' . $ID
	;
	DB::Update( $Query2 );
	echo $ID . ' - Processed!' . PHP_EOL;
}
echo PHP_EOL . 'All Done!</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
