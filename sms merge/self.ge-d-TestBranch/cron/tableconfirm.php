<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'error_log', PATH_BASE . DS . 'logs' . DS . 'Positions.log.txt' );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
include(PATH_BASE . DS . 'libraries' . DS . 'Table.php');

$Config = Helper::getConfig( 'run_close_tabel' );

if ( !$Config )
{
	die( 'Run Disabled By Config!' );
}
$Day = (int) Helper::getConfig( 'run_close_tabel_day' );
$Hour = (int) Helper::getConfig( 'run_close_tabel_hour' );
$Now = PDate::Get();
$NDay = (int) $Now->toFormat( '%d' );
$NHour = (int) $Now->toFormat( '%H' );

if ( $NDay < $Day )
{
	die( 'Not In Day!' );
}
if ( $NHour < $Hour )
{
	die( 'Not In Hour!' );
}
$DefChief = Helper::getConfig( 'run_close_tabel_chief' );
if ( empty( $DefChief ) )
{
	$DefChief = '-1';
}
$XTable = new XHRSTable();
$TableObj = $XTable->getTable();
$BillID = PDate::Get( 'first day of last month' )->toFormat( '%y%m' );

$Query = 'select '
				. ' t.*, '
				. ' to_char(t.change_date, \'yyyy-mm-dd hh24:mi:ss\') change_date, '
				. ' to_char(t.approve_date, \'yyyy-mm-dd hh24:mi:ss\') approve_date '
				. ' from HRS_TABLE t '
				. ' where '
				. ' t.status =2 '
				. ' and t.bill_id <= ' . $BillID
;

$Items = DB::LoadObjectList( $Query );
if ( count( $Items ) )
{
	foreach ( $Items as $Item )
	{
		$Table = clone $TableObj;
		$Table->reset();
		$Table->bind( $Item );
		if ( $Table->BILL_ID == $BillID )
		{
			$Table->STATUS = 2;
		}
		else
		{
			$Table->STATUS = 3;
		}
		$Table->CHANGE_DATE = PDate::Get()->toFormat();
		$Table->store();
		if ( $Table->BILL_ID == $BillID )
		{
			$XTable->SendAlert( $Table->WORKER, $Table );
		}
	}
}
$Query = 'select '
				. ' t.*, '
				. ' to_char(t.change_date, \'yyyy-mm-dd hh24:mi:ss\') change_date, '
				. ' nvl((SELECT min(mc.id) FROM slf_persons mc WHERE mc.active = 1 AND mc.id IN (SELECT wc.chief_PID FROM rel_worker_chief wc WHERE wc.worker = t.worker AND wc.clevel = 0)), ' . (int) $DefChief . ') approve, '
				. ' (to_date(to_char(ADD_MONTHS(to_date(t.bill_id || \'01\', \'yymmdd\'), 1), \'yyyy-mm-dd\'), \'yyyy-mm-dd\') + 9) approve_date '
				. ' from HRS_TABLE t '
				. ' where '
				. ' t.status =1 '
				. ' and  t.bill_id <= ' . (int) $BillID
;
$CItems = DB::LoadObjectList( $Query );

if ( count( $CItems ) )
{
	foreach ( $CItems as $Item )
	{
		$Table = clone $TableObj;
		$Table->bind( $Item );
		if ( $Table->BILL_ID == $BillID )
		{
			$Table->STATUS = 2;
		}
		else
		{
			$Table->STATUS = 3;
		}
		$Table->CHANGE_DATE = PDate::Get()->toFormat();
		$Table->APPROVE_DATE = PDate::Get( C::_( 'APPROVE_DATE', $Item ) )->toFormat( '%Y-%m-%d 22:00:00' );
		if ( $Table->APPROVE == 0 )
		{
			$Table->APPROVE = $DefChief;
		}
		$Table->store();
		if ( $Table->BILL_ID == $BillID )
		{
			$XTable->SendAlert( $Table->WORKER, $Table );
		}
	}
}
