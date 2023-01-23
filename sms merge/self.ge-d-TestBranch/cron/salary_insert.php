<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

$query = 'select '
				. ' w.id, '
				. ' sc.salary '
				. ' from slf_worker w '
				. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
				. ' where '
				. ' w.salary is null '
;
$result = DB::LoadObjectList( $query );

if ( empty( $result ) )
{
	return false;
}

$Slf_workerTable = new TableSlf_workerInterface( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );

$O = 0;
foreach ( $result as $data )
{
	$Slf_workerTable->resetAll();
	if ( !$Slf_workerTable->load( C::_( 'ID', $data ) ) )
	{
		continue;
	}

	$Slf_workerTable->SALARY = C::_( 'SALARY', $data );
	if ( !$Slf_workerTable->store() )
	{
		continue;
	}

	$O++;
}

if ( $O > 0 )
{
	echo '<span style="color:green;font-weight:bold;">Salary inserted!</span>';
}