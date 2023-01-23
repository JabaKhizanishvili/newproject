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
$LogPath = PATH_LOGS . DS . 'LoginData';
if ( Folder::exists( $LogPath ) )
{
	$Files = Folder::files( $LogPath );
	foreach ( $Files as $File )
	{
		unlink( $LogPath . DS . $File );
	}
}
### End Clear Button Status Data

$All = Request::getInt( 'all', 0 );
$st = microtime( 1 );

$Config = Helper::getConfig( 'run_logout' );
$SetLateness = Helper::getConfig( 'run_logout_set_lateness', 0 );
$run_logout_alert = Helper::getConfig( 'run_logout_alert' );
if ( !$Config )
{
	die( 'Run Logout Disabled By Config!' );
}

include(dirname( __FILE__ ) . DS . 'tables' . DS . 'BlackListTable.php');
require_once PATH_BASE . DS . 'libraries' . DS . 'live.php';
$Hour = (int) trim( Helper::getConfig( 'hr_logout_hours' ) );
$Query = '
select k.*
  from (select t.id,
               t.firstname,
               t.lastname,
               t.mobile_phone_number,
               t.email,
               t.permit_id,
               to_char(ed.event_date, \'hh24:mi:ss dd-mm-yyyy\') event_date,
               ed.real_type_id,
							ed.event_id,
               nvl(to_char(ede.event_date, \'hh24:mi:ss dd-mm-yyyy\'),
                   to_char(trunc(sysdate), \'hh24:mi:ss dd-mm-yyyy\')) status_date,
               nvl(ede.real_type_id, 2) status_id,
               ap.type
          from HRS_WORKERS_sch t
          left join (select ee.real_type_id, ee.staff_id, ee.event_date, ee.id event_id
                      from hrs_staff_events ee
                     where (ee.staff_id, ee.event_date) in
                           (select e.staff_id, max(e.event_date) event_date
                              from hrs_staff_events e
                             where e.event_date between sysdate - 15 and sysdate
                               and e.real_type_id in
                                   (1500, 2000, 2500, 3000, 3500)
                             group by e.staff_id)) ed
            on ed.staff_id = t.id
           and ed.real_type_id in (1500, 2000, 2500, 3000, 3500)
          left join (select ee.real_type_id, ee.staff_id, ee.event_date
                      from hrs_staff_events ee
                     where (ee.staff_id, ee.event_date) in
                           (select e.staff_id, max(e.event_date) event_date
                              from hrs_staff_events e
                             where e.event_date between sysdate - 15 and sysdate
                               and e.real_type_id in (1, 2, 10, 11)
                             group by e.staff_id)
                       and ee.real_type_id in (1, 2, 10, 11)) ede
            on ede.staff_id = t.id
          left join hrs_applications ap
            on ap.worker = t.id
           and sysdate between ap.start_date and ap.end_date
           and ap.status > 0
         WHERE 
				 --(t.sms_reminder = 1) AND 
				 (t.active = 1)
         --  AND T.MOBILE_PHONE_NUMBER is not null
      --     and ap.type is null
	 ) k
 where 
 k.STATUS_ID = 1 '
				. ' and k.real_type_id = 3500 '
;

if ( !$All )
{
	$Query .= ' and (to_date(k.event_date, \'hh24:mi:ss dd-mm-yyyy\') + 1 / 24 * ' . $Hour . ') <= sysdate '
					. ' and (to_date(k.event_date, \'hh24:mi:ss dd-mm-yyyy\') + 1 / 24 * ' . ($Hour + 1) . ') >= sysdate ';
}

$items = DB::LoadObjectList( $Query );
echo '<pre><pre>';
echo 'Run Logout Script - ';
if ( !empty( $items ) )
{
	$Text = Text::_( 'Unknown movement Charge' );
	$Now = new PDate();
	$NowUnix = $Now->toUnix();
	foreach ( $items as $Worker )
	{
		$EVENT_DATE = PDate::Get( C::_( 'EVENT_DATE', $Worker, null ) );
		$STATUS_DATE = PDate::Get( C::_( 'STATUS_DATE', $Worker, null ) );
		$Minutes = round( ($EVENT_DATE->toUnix() - $STATUS_DATE->toUnix()) / 60, 0, PHP_ROUND_HALF_DOWN );
		$EventID = C::_( 'EVENT_ID', $Worker );
		LiveHelper::LogoutUser( $Worker );
		if ( $run_logout_alert )
		{
			LiveHelper::SendNoLogOutNotice( $Worker );
		}
		if ( $SetLateness )
		{
			LiveHelper::SetLatenes( $EventID, $Minutes, $Text );
		}
		echo C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker ) . ' - Done!' . PHP_EOL;
	}
}

$tm = round( microtime( true ) - $st, 10 );
echo ' Time Elapsed: ' . $tm . ' Sec';
