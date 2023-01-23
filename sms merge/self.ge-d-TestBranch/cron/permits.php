<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

/**
 * Clear Button Status Data
 */
$LogPath = PATH_LOGS . DS . 'PermitData';
if ( Folder::exists( $LogPath ) )
{
	$Files = Folder::files( $LogPath );
	foreach ( $Files as $File )
	{
		unlink( $LogPath . DS . $File );
	}
}
### End Clear Button Status Data
$st = microtime( 1 );

$Query = 'select p.id, p.permit_id
  from SLF_PERSONS p
  left join rel_person_permit pp
    on pp.person = p.id
 where p.permit_id is not null
   and p.active = 1
   and pp.person is null  '
;

$items = DB::LoadObjectList( $Query );
echo '<pre><pre>';
echo 'Run Logout Script - ';
if ( !empty( $items ) )
{
	foreach ( $items as $Item )
	{
		if ( empty( $Item ) )
		{
			continue;
		}
		$query = ' insert into rel_person_permit '
						. ' (person, permit_id) '
						. 'values '
						. '('
						. DB::Quote( C::_( 'ID', $Item ) )
						. ','
						. DB::Quote( C::_( 'PERMIT_ID', $Item ) )
						. ')';

		$Result = DB::Insert( $query );
		echo C::_( 'ID', $Item ) . ' ' . C::_( 'PERMIT_ID', $Item ) . ' - Done!' . PHP_EOL;
	}
}

$Query2 = ' select pp.permit_id, t.worker, t.device_id
  from REL_WORKER_DEVICE_ID t
  left join rel_person_permit pp
    on pp.person = t.worker
  left join slf_persons p
    on p.id = pp.person
 where t.permit_id is null
   and p.active = 1 '
;

$items2 = DB::LoadObjectList( $Query2 );
if ( !empty( $items2 ) )
{
	foreach ( $items2 as $Item )
	{
		$query = ' update rel_worker_device_id t set t.permit_id = ' . DB::Quote( C::_( 'PERMIT_ID', $Item ) )
						. ' where'
						. '  t.worker = ' . DB::Quote( C::_( 'WORKER', $Item ) )
						. ' and t.device_id = ' . DB::Quote( C::_( 'DEVICE_ID', $Item ) )
		;

		$Result = DB::Update( $query );
		echo C::_( 'ID', $Item ) . ' ' . C::_( 'PERMIT_ID', $Item ) . ' - Done!' . PHP_EOL;
	}
}

$tm = round( microtime( true ) - $st, 10 );
echo ' Time Elapsed: ' . $tm . ' Sec';
