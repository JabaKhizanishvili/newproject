<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( dirname( dirname( __FILE__ ) ) ) );

define( 'PATH_BASE', $base );
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

echo '<pre><pre>';
$st = microtime( 1 );
$Query = 'update lib_graph_times t
       set t.holiday_yn = 1
     where t.owner = 0
       and t.holiday_yn is null
';
DB::Update( $Query );
$Query2 = 'update lib_graph_times t
set t.holiday_yn = 0
where t.owner > 0
and t.holiday_yn is null
';
DB::Update( $Query2 );
echo '<pre>';

$Query3 = 'select e.staff_id,
       to_char(e.event_date, \'yyyy-mm-dd\') start_date,
       to_char(getenddate(e.staff_id, e.event_date), \'yyyy-mm-dd\') end_date
  from hrs_staff_events e
	left join lib_graph_times gt on gt.id = e.time_id
 where e.real_type_id = 2000
   and trunc(e.event_date) in
       (select *
          from (select to_date((to_char(sysdate, \'yyyy\')) || \'-\' ||
                               t.lib_month || \'-\' || t.lib_day,
                               \'yyyy-mm-dd\') holiday
                  from lib_holidays t
                 where t.active = 1) m
		where m.holiday between sysdate - 10 and sysdate)
		and gt.holiday_yn = 1
';
$Data = DB::LoadObjectList( $Query3 );
foreach ( $Data as $D )
{
	$ID = C::_( 'STAFF_ID', $D );
	$StartDate = C::_( 'START_DATE', $D );
	$EndDate = C::_( 'END_DATE', $D );
	if ( empty( $EndDate ) )
	{
		$EndDate = $StartDate;
	}
	$Params = array(
			':p_date_start' => $StartDate,
			':p_date_end' => $EndDate,
			':p_worker' => $ID
	);
	DB::callProcedure( 'ReCalc', $Params );
	echo $ID . ' - ' . $StartDate . ' - ' . $EndDate . ' - Processed!' . PHP_EOL;
}
echo PHP_EOL . 'All Done!</pre>' . PHP_EOL . PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec' . PHP_EOL . PHP_EOL;
