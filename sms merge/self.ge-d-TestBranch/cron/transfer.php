<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

//Configuration
$Config = Helper::getConfig( 'transfer_activate' );
if ( !$Config )
{
	die( 'Transfer Process Disabled By Config!' );
}
$Hour = (int) Helper::getConfig( 'transfer_hour' );
$HourNow = PDate::Get()->toFormat( '%H' );
if ( $Hour > $HourNow )
{
	die( 'Not In Hour!' );
}

$MSGto_Creator = (int) Helper::getConfig( 'transfer_msg_to_creator' );
$MSGto_Receiver = (int) Helper::getConfig( 'transfer_msg_to_receiver' );
$MSGto_Worker = (int) Helper::getConfig( 'transfer_msg_to_worker' );

//Get Hrs_workers_ORG Table
include(dirname( __FILE__ ) . DS . 'tables' . DS . 'workerOrgTable.php');
$OrgTable = new workerOrgTable();

//Get Hrs_transfers Table
include(dirname( __FILE__ ) . DS . 'tables' . DS . 'transferTable.php');
$transferTable = new transferTable();

$where = [];
$where[] = ' trunc(tr.transfer_date) <= trunc(sysdate) ';
$where[] = ' tr.status = 1 ';

$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
$Q = 'select * from HRS_TRANSFERS tr '
				. $whereQ
;
$Result = DB::LoadObjectList( $Q );

foreach ( $Result as $one )
{
	$transferTable->bind( $one );

	unset( $one->ID );
	unset( $one->INFO );
	unset( $one->REC_USER );
	unset( $one->REC_DATE );
	unset( $one->STATUS );
	unset( $one->APPROVE );
	unset( $one->APPROVE_DATE );
	unset( $one->DEL_USER );
	unset( $one->DEL_DATE );
	unset( $one->TRANSFER_DATE );
	unset( $one->CCOMMENT );

	$OrgTable->resetAll();
	$OrgTable->load( C::_( 'WORKER', $one ) );
	$OrgTable->bind( $one );

	$OrgTable->store();
	SaveAccountingOfficesRel( C::_( 'ACCOUNTING_OFFICE', $transferTable ), C::_( 'WORKER', $transferTable ) );
	SaveWorkersRel( C::_( 'WORKER', $transferTable ), C::_( 'CHIEF', $transferTable ), C::_( 'ORG', $transferTable ) );
	SaveGroupRel( C::_( 'GRAPHTYPE', $transferTable ), C::_( 'GRAPHGROUP', $transferTable ), C::_( 'WORKER', $transferTable ), C::_( 'ORG', $transferTable ) );

	$transferTable->STATUS = 2;
	$transferTable->store();
}
function SaveAccountingOfficesRel( $Offices, $worker )
{
	$DelQuery = 'delete '
					. ' from  rel_accounting_offices cp '
					. ' where '
					. ' cp.worker = ' . (int) $worker;

	$offices = explode( '|', $Offices );
	if ( !count( $offices ) )
	{
		return;
	}

	DB::Delete( $DelQuery );
	$query = 'Begin '
					. ' INSERT ALL ';
	foreach ( $offices as $office )
	{
		$query .= ' into rel_accounting_offices '
						. ' (worker, office) '
						. 'values '
						. '('
						. (int) $worker
						. ','
						. (int) $office
						. ')';
	}
	$query .= ' SELECT * FROM dual;'
					. 'end;';
	$Result = DB::InsertAll( $query );
	return $Result;

}

function SaveWorkersRel( $worker, $chief, $org )
{
	$DelQuery = 'delete '
					. ' from  rel_worker_chief cp '
					. ' where '
					. ' cp.WORKER = ' . (int) $worker
					. ' and cp.org= ' . (int) $org
	;

	DB::Delete( $DelQuery );
	$query = 'Begin '
					. ' INSERT ALL ';
	foreach ( $worker as $worker )
	{
		if ( empty( $worker ) )
		{
			continue;
		}
		$query .= ' into rel_worker_chief '
						. ' (worker, chief, org) '
						. 'values '
						. '('
						. (int) $worker
						. ','
						. (int) $chief
						. ','
						. (int) $org
						. ')';
	}
	$query .= ' SELECT * FROM dual;'
					. 'end;';

	$Result = DB::InsertAll( $query );
	return $Result;

}

function SaveGroupRel( $graphtype, $graphgroup, $worker, $org )
{
	$DelQuery = 'delete '
					. ' from  rel_workers_groups wp '
					. ' where '
					. ' wp.worker = ' . $worker
					. ' and wp.org = ' . $org
	;
	DB::Delete( $DelQuery );
	if ( $graphgroup < 1 )
	{
		return;
	}
	if ( $graphtype == '0' )
	{
		$query = ' insert into rel_workers_groups '
						. ' (group_id,worker,ordering, org) '
						. 'values '
						. '('
						. (int) $graphgroup . ','
						. (int) $worker . ','
						. ' 9999' . ','
						. (int) $org
						. ')';
		$Result = DB::Insert( $query );
		DB::callProcedure( 'updategroups' );
		return $Result;
	}
	return;

}
