<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( 1 );

$Config = Helper::getConfig( 'graph_autoovertime_on' );
if ( !$Config )
{
	die( 'Run Logout Disabled By Config!' );
}
$Confirm = intval( trim( Helper::getConfig( 'graph_autoovertime_auto_confirm' ) ) );
$AfterDay = intval( trim( Helper::getConfig( 'graph_autoovertime_after_day' ) ) );
$AlertTemplate = trim( Helper::getConfig( 'graph_autoovertime_alert' ) );
$WeekStart = trim( Helper::getConfig( 'graph_autoovertime_week_start_day' ) );

$Now = PDate::Get();
$Start = PDate::Get( $Now->toUnix() - 60 * 60 * 24 * ($AfterDay ) );

$Query = 'select *
  from (select m.*,
               to_char(m.StartTime, \'yyyy-mm-dd hh24:mi:ss\') start_time,
               to_char(m.EndTime, \'yyyy-mm-dd hh24:mi:ss\') end_time,
               nvl(round((m.endtime - m.starttime) * 1 * 24, 2), 0) as diff
          from (select e.event_date,
                       w.id,
                       w.firstname,
                       w.lastname,
				w.org,
                       w.mobile_phone_number,
                       e.event_date StartTime,
                       getnextdate(e.staff_id, e.event_date, 2) endtime,
                       e.time_id,
                       GetPrevDate(e.staff_id, e.event_date, 1) bin,
                       GetPrevDate(e.staff_id, e.event_date, 2) bout,
                       GetNextDate(e.staff_id, e.event_date, 1) ain,
                       GetNextDate(e.staff_id, e.event_date, 2) aout
                  from HRS_STAFF_EVENTS e
                  left join hrs_applications a
                    on a.worker = e.staff_id
                   and trunc(a.start_date) = trunc(e.event_date)
                   and a.status > -1
                   and a.type = 13
                  left join lib_graph_times gt
                    on gt. id = e.time_id
                  left join hrs_workers w
                    on w.id = e.staff_id
                 WHERE e.event_date >= to_date(\'' . $Start->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\')
                   AND e.event_date <=
                       to_date(\'' . $Start->toFormat( '%Y-%m-%d' ) . ' 23:59:59\', \'yyyy-mm-dd hh24:mi:ss\')
                   AND (e.real_type_id = 3500)
                   and w.Auto_Overtime = 1
                   and a.id is null) m) k
 where k.diff >' . $MinHour
				. '  and k.bout < k.bin
   and k.bin < k.StartTime '
;

$items = DB::LoadObjectList( $Query );

echo '<pre>';
echo 'Run Auto Overtime Script - ';
if ( !empty( $items ) )
{
	$Table = AppHelper::getTable();
	$Now = new PDate();
	$NowUnix = $Now->toUnix();
	foreach ( $items as $Worker )
	{
		$Table->resetAll();
		$Hour = floatval( C::_( 'DIFF', $Worker ) );
		if ( $Hour > $MaxHour )
		{
			$Hour = $MaxHour;
		}
		$BaseDate = new PDate( C::_( 'STARTTIME', $Worker ) );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );
		$Table->ORG = C::_( 'ORG', $Worker );
		$Table->WORKER = C::_( 'ID', $Worker );
		$Table->TYPE = APP_AUTO_OVERTIME;
		$Table->START_DATE = $StartDate->toFormat();
		$Table->END_DATE = $EndDate->toFormat();
		$Table->REC_DATE = $Now->toFormat();
		$Table->DAY_COUNT = $Hour;
		$Table->STATUS = 0;
		$Table->APPROVE = 0;
		$Table->SYNC = 0;
		$Table->DEL_USER = 0;
		$Table->INFO = Text::_( 'AutoOvertime' );
		$Table->check();
		$Table->store();
		echo C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker ) . ' - Done!' . PHP_EOL;
	}
}
$tm = round( microtime( true ) - $st, 10 );
echo ' Time Elapsed: ' . $tm . ' Sec';
