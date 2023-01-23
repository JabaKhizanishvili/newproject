<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'error_log', PATH_BASE . DS . 'logs' . DS . 'Installators.log.txt' );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
require_once PATH_BASE . DS . 'libraries' . DS . 'live.php';
include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';
$st = microtime( 1 );
$Config = Helper::getConfig( 'run_sms' );
if ( !$Config )
{
	die( 'Run SMS Disabled By Config!' );
}
$Query = 'select t.id,
       t.firstname,
       t.lastname,
       t.org_name,
       t.parent_id,
       t.mobile_phone_number,
--	t.counting_type,
       to_char(ed.event_date, \'hh24:mi:ss dd-mm-yyyy\') event_date,
       ed.real_type_id,
       nvl(to_char(ede.event_date, \'hh24:mi:ss dd-mm-yyyy\'),
           to_char(trunc(sysdate), \'hh24:mi:ss dd-mm-yyyy\')) status_date,
       nvl(ede.real_type_id, 2) status_id,
       ap.type
  from hrs_workers_sch t
  left join (select *
               from hrs_staff_events ee
              where (ee.staff_id, ee.event_date) in
                    (select e.staff_id, max(e.event_date) event_date
                       from hrs_staff_events e
                      where e.event_date between sysdate - 10 and sysdate
                        and e.real_type_id in (1500, 2000, 2500, 3000, 3500)
                      group by e.staff_id)) ed
    on ed.staff_id = t.id
   and ed.real_type_id in (1500, 2000, 2500, 3000, 3500)
  left join (select *
               from hrs_staff_events ee
              where (ee.staff_id, ee.event_date) in
                    (select e.staff_id, max(e.event_date) event_date
                       from hrs_staff_events e
                      where e.event_date between sysdate - 10 and sysdate
                        and e.real_type_id in (1, 2, 10, 11)
                      group by e.staff_id)
                and ee.real_type_id in (1, 2, 10, 11)) ede
    on ede.staff_id = t.id
  left join hrs_applications ap
    on ap.worker = t.orgpid
   and sysdate between ap.start_date and ap.end_date
   and ap.status > 0
 WHERE (t.sms_reminder = 1)
   AND (t.active = 1)
   AND T.MOBILE_PHONE_NUMBER is not null
  -- AND ede.real_type_id =2
--	 and t.livelist = 1
--   and  ap.type is null
 order by t.parent_id ';
$items = DB::LoadObjectList( $Query );
echo '<pre>' . PHP_EOL . PHP_EOL;
if ( !empty( $items ) )
{
	$Now = new PDate();
	$NowUnix = $Now->toUnix();
	$Workers = [];
	foreach ( $items as $Worker )
	{
		$Status = LiveHelper::CalculateStatus( $Worker );
		if ( $Status == 'st_not_in' )
		{
			$Times = AppHelper::GetUserDayDates( $Worker->ID );
			if ( empty( C::_( 'DayStart', $Times ) ) )
			{
				continue;
			}
			if ( empty( C::_( 'DayEnd', $Times ) ) )
			{
				continue;
			}
			$DayStart = LiveHelper::GetDateUnix( C::_( 'DayStart', $Times ) );
			$DayEnd = LiveHelper::GetDateUnix( C::_( 'DayEnd', $Times ) );
			$DayBStart = LiveHelper::GetDateUnix( C::_( 'BreakStart', $Times ) );
			$DayBEnd = LiveHelper::GetDateUnix( C::_( 'BreakEnd', $Times ) );
			if ( !($DayStart < $NowUnix && $NowUnix < $DayEnd ) )
			{
				continue;
			}
			if ( $DayBStart < $NowUnix && $NowUnix < $DayBEnd )
			{
				continue;
			}
			$EventDate = new PDate( C::_( 'EVENT_DATE', $Worker, 'now' ) );
			$StatusDateStr = C::_( 'STATUS_DATE', $Worker, null );
			if ( empty( $StatusDateStr ) )
			{
				$StatusDate = new PDate( '- 5 hours' );
			}
			else
			{
				$StatusDate = new PDate( $StatusDateStr );
			}
			$TimeLastDate = new PDate();
			if ( $EventDate->toUnix() > $StatusDate->toUnix() )
			{
				$TimeLastDate = $EventDate;
			}
			else
			{
				$TimeLastDate = $StatusDate;
			}
			$Minutes = intval( ($Now->toUnix() - $TimeLastDate->toUnix()) / 60 );
			if ( $Minutes )
			{
				$IDx = C::_( 'PARENT_ID', $Worker );
				$Worker->MINUTES = $Minutes;
				$Workers[$IDx] = C::_( $IDx, $Workers, array() );
				$Workers[$IDx][] = $Worker;
			}
		}
	}
	foreach ( $Workers as $Key => $Items )
	{
		$Minute = C::_( '0.MINUTES', $Items );
		LiveHelper::SendLatenesSMS( $Items );
		echo C::_( '0.FIRSTNAME', $Items ) . ' ' . C::_( '0.LASTNAME', $Items ) . ' ' . $Minute . ' - Done!' . PHP_EOL;
	}
}


echo '</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;

