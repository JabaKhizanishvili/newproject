<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( 1 );

$Config = Helper::getConfig( 'graph_weekovertime_on', 'overtimeworkers' );
if ( !$Config )
{
	die( 'Run Logout Disabled By Config!' );
}
$AfterDay = intval( trim( Helper::getConfig( 'graph_weekovertime_after_day', 'overtimeworkers' ) ) );
$AlertTemplate = trim( Helper::getConfig( 'graph_weekovertime_alert', 'overtimeworkers' ) );
$AutoConfirm = trim( Helper::getConfig( 'graph_weekovertime_auto_confirm', 'overtimeworkers' ) );

$Now = PDate::Get();
$Start = PDate::Get( $Now->toUnix() - 60 * 60 * 24 * ($AfterDay ) );
$Dates = XGraph::GetWeekStartEnd( $Start->toFormat() );
$Today = $Start->toFormat( '%Y-%m-%d' );
$MaxHour = 8;
$MinHour = 0.5;
$StartDate = C::_( '0', $Dates );
$EndDate = C::_( '1', $Dates );
$Q = 'select k.*, wr.work_duration, w.org
  from (select t.worker, nvl(sum(gt.working_time), 0) time
          from HRS_GRAPH t
          left join lib_graph_times gt
            on gt.id = t.time_id
         where t.real_date between  to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') '
				. ' and  to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') 
         group by t.worker) k
  left join hrs_workers_org w
    on w.id = k.worker
  left join lib_working_rates wr
    on wr.id = w.work_type

 where k.time > wr.work_duration'

;
echo '<pre><pre>';
print_r($Q);
echo '</pre><b>FILE:</b> '.__FILE__.'     <b>Line:</b> '.__LINE__.'</pre>'."\n";
die;

$Items = DB::LoadObjectList( $Q );

foreach ( $Items as $Item )
{
	$Worker = C::_( 'WORKER', $Item );
	$Org = C::_( 'ORG', $Item );
	$Rate = C::_( 'WORK_DURATION', $Item );
	$Days = GetDaysData( $Worker, $StartDate, $Start );
	$Sum = 0.0;
	$AllOvertime = 0.0;
	$Overtime = 0;
	foreach ( $Days as $Day )
	{
		$Sum += floatval( C::_( 'WORKING_TIME', $Day ) );
		$D = C::_( 'SDAY', $Day );
		$APP = C::_( 'APP', $Day );
		if ( $Sum > $Rate )
		{
			$Overtime = $Sum - $Rate - $AllOvertime;
		}
		if ( $Sum > $Rate && $D == $Today )
		{
			if ( $APP )
			{
				break;
			}
			RegisterOvertime( $Worker, $Overtime, $D, $Org, $AutoConfirm );
			break;
		}
		$AllOvertime += $Overtime;
		$Overtime = 0;
	}
}
function GetDaysData( $Worker, $StartDate, $EndDate )
{
	$Query = 'select gt.working_time, to_char(t.real_date, \'yyyy-mm-dd\') sday, a.id app
  from HRS_GRAPH t
  left join lib_graph_times gt on gt.id = t.time_id
  left join hrs_applications a on a.worker = t.worker and a.type = 14 and trunc(a.start_date) = trunc(t.real_date) and a.status > -1
  where t.real_date between  to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') '
					. ' and  to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') 
   and t.worker = ' . $Worker . ' and t.time_id > 0 order by t.real_date asc'
	;
	return DB::LoadObjectList( $Query );

}

/**
 * 
 * @param type $Worker
 * @param type $Rate
 * @param type $D
 * @param type $Org
 * @param type $AutoConfirm
 * @return type
 */
function RegisterOvertime( $Worker, $Rate, $D, $Org, $AutoConfirm )
{
	$Table = AppHelper::getTable();
	$Now = new PDate();
	$Table->resetAll();
	$BaseDate = new PDate( $D );
	$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
	$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );
	$Table->ORG = $Org;
	$Table->WORKER = $Worker;
	$Table->TYPE = APP_WEEK_OVERTIME;
	$Table->START_DATE = $StartDate->toFormat();
	$Table->END_DATE = $Now->toFormat();
	$Table->APPROVE_DATE = $EndDate->toFormat();
	$Table->REC_DATE = $Now->toFormat();
	$Table->DAY_COUNT = $Rate;
	if ( $AutoConfirm )
	{
		$Table->STATUS = 0;
	}
	else
	{
		$Table->STATUS = 1;
	}
	$Table->APPROVE = 0;
	$Table->SYNC = 0;
	$Table->DEL_USER = 0;
	$Table->INFO = Text::_( 'Week Rate Overtime' );
	$Table->check();
	return $Table->store();

}
