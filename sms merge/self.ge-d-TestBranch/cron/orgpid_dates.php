<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

//Configuration
//$Hour = (int) str_replace( ':', '', Helper::getConfig( 'SalarySheet_work_start' ) );
//$HourNow = (int) PDate::Get()->toFormat( '%H00' );
//if ( $Hour > $HourNow )
//{
//	die( 'Not In Hour!' );
//}

$query = ' select '
				. ' w.orgpid, '
				. ' min(cc.change_date) assignment_date, '
				. ' max(rr.change_date) release_date, '
				. ' nvl2(max(rr.change_date), \'-2\', null) as active '
				. ' from '
				. ' slf_worker w '
				. ' left join (select c.* from slf_changes c where c.change_type = 1) cc on cc.id = w.change_id '
				. ' left join (select r.* from slf_changes r where r.change_type = 3) rr on rr.id = w.change_id '
				. ' left join rel_person_org rel on rel.id = w.orgpid '
				. ' where '
				. ' rel.release_date is null '
				. ' and rel.assignment_date is null '
				. ' group by w.orgpid '
;
$lines = DB::LoadObjectList( $query, 'ORGPID' );

if ( !count( $lines ) )
{
	return false;
}

$Slf_worker_relTable = new TableRel_person_orgInterface( 'rel_person_org', 'ID', 'sqs_rel_person_org.nextval' );
foreach ( $lines as $id => $data )
{
	if ( empty( $data->ASSIGNMENT_DATE ) && empty( $data->RELEASE_DATE ))
	{
		continue;
	}

	if ( !$Slf_worker_relTable->load( $id ) )
	{
		continue;
	}
	unset( $data->ORGPID );

	$Slf_worker_relTable->bind( $data );
	$Slf_worker_relTable->store();
}

echo '<span style="color:green;font-weight:bold;">ORGPID Dates inserted!</span>';
