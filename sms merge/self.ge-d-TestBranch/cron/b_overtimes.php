<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( 1 );

$Config = Helper::getConfig( 'before_autoovertime_on' );
if ( !$Config )
{
	die( 'Run Before Overtimes Disabled By Config!' );
}
$MinHour = floatval( trim( Helper::getConfig( 'before_autoovertime_min_hour' ) ) );
$MaxHour = floatval( trim( Helper::getConfig( 'before_autoovertime_max_hour' ) ) );
$AfterDay = intval( trim( Helper::getConfig( 'before_autoovertime_after_day' ) ) );
$AlertTemplate = trim( Helper::getConfig( 'before_autoovertime_alert' ) );

$Now = PDate::Get();
$Start = PDate::Get( $Now->toUnix() - 60 * 60 * 24 * ($AfterDay ) );

$Query = 'select *
  from (select m.*,
               to_char(m.StartTime, \'yyyy-mm-dd hh24:mi:ss\') start_time,
               to_char(m.EndTime, \'yyyy-mm-dd hh24:mi:ss\') end_time,
               nvl(round((m.endtime - m.starttime) * 1 * 24, 2), 0) as diff
          from (select e.event_date,
                       ow.id,
                       w.firstname,
                       w.lastname,
				ow.org,
				ow.orgpid,
                       w.mobile_phone_number,
                      getprevdate(e.staff_id, e.event_date, 1)  StartTime,
                       e.event_date endtime,
                       e.time_id 
                  from HRS_STAFF_EVENTS e
                  left join hrs_applications a
                    on a.worker_id = e.staff_id
                   and trunc(a.start_date) = trunc(e.event_date)
                   and a.status > -1
                   and a.type = ' . APP_BEFORE_AUTO_OVERTIME . ' 
                  left join lib_graph_times gt
                    on gt. id = e.time_id
                  left join slf_worker ow
                    on ow.id = e.staff_id
			left join slf_persons w on w.id = ow.person 
                 WHERE e.event_date >= to_date(\'' . $Start->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\')
                   AND e.event_date <=
                       to_date(\'' . $Start->toFormat( '%Y-%m-%d' ) . ' 23:59:59\', \'yyyy-mm-dd hh24:mi:ss\')
                   AND (e.real_type_id = 2000)
                   and ow.Auto_Overtime = 1
                   and a.id is null) m) k
 where k.diff between ' . $MinHour . ' and ' . $MaxHour
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
		/** @var TableHrs_applicationsInterface $Table */
		$BaseDate = new PDate( C::_( 'STARTTIME', $Worker ) );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );
		$Table->ORG = C::_( 'ORG', $Worker );
		$Table->WORKER = C::_( 'ORGPID', $Worker );
		$Table->WORKER_ID = C::_( 'ID', $Worker );
		$Table->TYPE = APP_BEFORE_AUTO_OVERTIME;
		$Table->START_DATE = $StartDate->toFormat();
		$Table->END_DATE = $EndDate->toFormat();
		$Table->REC_DATE = $Now->toFormat();
		$Table->DAY_COUNT = $Hour;
		$Table->STATUS = 0;
		$Table->APPROVE = 0;
		$Table->SYNC = 0;
		$Table->DEL_USER = 0;
		$Table->INFO = Text::_( 'Before AutoOvertime' );
		$Table->check();
		$Table->store();
		echo C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker ) . ' - Done!' . PHP_EOL;
	}
}
$tm = round( microtime( true ) - $st, 10 );
echo ' Time Elapsed: ' . $tm . ' Sec';
