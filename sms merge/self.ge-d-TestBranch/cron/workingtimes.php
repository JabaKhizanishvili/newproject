<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( 1 );

$Q = ' update '
				. ' HRS_STAFF_EVENTS e '
				. ' SET e.TIME_ID = 39914 '
				. ' WHERE '
				. ' e.EVENT_DATE  > sysdate - 9 '
				. ' AND e.TIME_ID  = 0 '
;
DB::Update( $Q );

$Query = 'SELECT
	k.*,
	(trunc(k.end_date, \'MI\') - trunc(k.start_date, \'MI\'))*24 worked_time,
	tin.ACCESS_POINT_CODE, 
	tin.CARDNAME start_pic,
	tout.CARDNAME end_pic,
	din.OFFICE
FROM
	(
	SELECT
		e.EVENT_DATE start_date,
    GETNEXTDATE(e.STAFF_ID, e.EVENT_DATE, 2) end_date,
		e.STAFF_ID worker,
		e.TIME_ID,
		sw.ORG ,
		lss.ORG_PLACE,
		0 status
	FROM
		HRS_STAFF_EVENTS e
	LEFT JOIN SLF_WORKER sw ON
		sw.ID = e.STAFF_ID
	LEFT JOIN LIB_STAFF_SCHEDULES lss ON
		lss.ID = sw.STAFF_SCHEDULE
	WHERE
		e.TIME_ID IN (
		SELECT
			gt.id
		FROM
			LIB_GRAPH_TIMES gt
		WHERE
			gt.TYPE = 1
)
		AND e.EVENT_DATE BETWEEN trunc(sysdate - 7) AND TRUNC(SYSDATE-2)
		AND e.REAL_TYPE_ID = 1
) k
LEFT JOIN HRS_TRANSPORTED_DATA tin ON
	tin.REC_DATE = k.start_date
	AND tin.USER_ID = k.worker
	AND tin.DOOR_TYPE = 1
LEFT JOIN hrs_transported_data tout ON
	tout.rec_date = k.end_date
	AND tout.user_id = k.worker
	AND tout.door_type = 2
LEFT JOIN LIB_DOORS din ON
	din.CODE = tin.ACCESS_POINT_CODE 
	LEFT JOIN HRS_WORKED_TIMES wt ON
	wt.WORKER = k.worker
	AND wt.START_DATE = k.start_date
WHERE wt.ID IS null '
;
$items = DB::LoadObjectList( $Query );
echo '<pre>';
echo 'Run Worked Times Script - ';
if ( !empty( $items ) )
{
	$Table = new TableHrs_worked_timesInterface( 'Hrs_worked_times', 'ID', 'sqs_hrs_worked_times.nextval' );
	$Now = new PDate();
	$Calculator = new XHRSTable();

	foreach ( $items as $Item )
	{
		$Table->resetAll();
		$Table->bind( $Item );
		$Table->WORKED_TIME = Helper::FormatBalance( $Table->WORKED_TIME );
		if ( empty( $Table->START_DATE ) || empty( $Table->END_DATE ) )
		{
			$Table->NIGHT_WORKED_TIME = Helper::FormatBalance( 0 );
			$Table->DAY_WORKED_TIME = Helper::FormatBalance( 0 );
		}
		else
		{
			$Table->NIGHT_WORKED_TIME = Helper::FormatBalance( $Calculator->NightHourCalculator( $Table->START_DATE, $Table->END_DATE ) );
			$Table->DAY_WORKED_TIME = Helper::FormatBalance( $Table->WORKED_TIME - $Table->NIGHT_WORKED_TIME );
		}
		$Table->CREATE_DATE = $Now->toFormat();
		$Table->MODIFY_USER = 0;
		$Table->DEL_USER = 0;
		$Table->MODIFY_DATE = $Now->toFormat();
		$Table->check();
		$Table->store();
	}
}
$tm = round( microtime( true ) - $st, 10 );
echo ' Time Elapsed: ' . $tm . ' Sec';
