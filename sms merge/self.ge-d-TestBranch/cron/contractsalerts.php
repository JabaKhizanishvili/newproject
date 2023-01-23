<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( 1 );

$Config = Helper::getConfig( 'contracts_alerts' );
if ( !$Config )
{
	die( 'Contracts Alerts Disabled By Config!' );
}

$Day1 = (int) Helper::getConfig( 'contracts_alerts_day1' );
$Day2 = (int) Helper::getConfig( 'contracts_alerts_day2' );
$Day3 = (int) Helper::getConfig( 'contracts_alerts_day3' );
$Hour = (int) Helper::getConfig( 'contracts_alerts_hour' );
$HourNow = PDate::Get()->toFormat( '%H' );
if ( $Hour > $HourNow )
{
	die( 'Not In Hour!' );
}

$Mails = Helper::CleanArray( explode( ',', Helper::getConfig( 'contracts_alerts_mails' ) ), 'Str' );
if ( !count( $Mails ) )
{
	die( 'Mail List is empty' );
}

$LockDir = PATH_LOGS . DS . 'ContractsAlertsLock';
if ( !is_dir( $LockDir ) )
{
	Folder::create( $LockDir, 0777 );
}
$LockFile = $LockDir . DS . 'lock-' . date( 'Y-m-d' );
if ( is_file( $LockFile ) )
{
	die( 'Task Job Must Run Only Once!' );
}
file_put_contents( $LockFile, date( 'Y-m-d  H:i:s' ) );
echo '<pre>Run Contracts Alerts Script - ' . $Day1 . ' <br /> ';
SendAlerts( $Day1, $Mails );
echo '<pre>Run Contracts Alerts Script - ' . $Day2 . ' <br /> ';
SendAlerts( $Day2, $Mails );
echo '<pre>Run Contracts Alerts Script - ' . $Day3 . ' <br /> ';
SendAlerts( $Day3, $Mails );

/*
  [FIRSTNAME] => რუსუდანი
  [LASTNAME] => ანანიაშვილი
  [ORG_NAME] => შპს ალფა
  [ORG_PLACE_NAME] => შერჩევის განყოფილება
  [POSITION] => სინიორ კონსულტანტი
  [CONTRACT_END_DATE] => 13-09-2021

 */
echo PHP_EOL;
$tm = round( microtime( true ) - $st, 10 );
echo 'Time Elapsed: ' . $tm . ' Sec';
/**
 * 
 * @param type $Day
 * @return type
 */
function GetWorkers( $Day )
{
	$Query = 'select 
	t.firstname,
       t.id,
       t.lastname,
       t.private_number,
       t.org_name,
       sc.lib_title as staff_schedule,
       t.org_place_name,
       t.position,
       to_char(t.contract_end_date, \'dd-mm-yyyy\') contract_end_date
  from hrs_workers_sch t
  left join lib_staff_schedules sc on sc.id = t.staff_schedule
 where trunc(t.contract_end_date) = trunc(sysdate + ' . $Day . ')
   and t.active = 1
';
	return DB::LoadObjectList( $Query );

}

function SendAlerts( $Day, $Mails )
{
	$Items = GetWorkers( $Day );
	if ( !empty( $Items ) )
	{
		foreach ( $Items as $Worker )
		{
			$ContractsDate = PDate::Get( C::_( 'CONTRACT_END_DATE', $Worker, null ) );
			$Firstname = C::_( 'FIRSTNAME', $Worker );
			$Lastname = C::_( 'LASTNAME', $Worker );
			$PN = C::_( 'PRIVATE_NUMBER', $Worker );
			$SCH = C::_( 'STAFF_SCHEDULE', $Worker );
			$Position = C::_( 'POSITION', $Worker );
			$Org = C::_( 'ORG_NAME', $Worker );
			$OrgPlace = C::_( 'ORG_PLACE_NAME', $Worker );

			$Subject = $Firstname . ' ' . $Lastname . ' - თანამშრომლის კონტრაქტის ვადის შეტყობინება';

			$Message = ' თანამშრომლის კონტრაქტს, ' . $Day . ' დღეში გასდის ვადა:' . PHP_EOL . PHP_EOL
							. 'თანამშრომელი: ' . $Firstname . ' ' . $Lastname . PHP_EOL
							. 'პ/ნ: ' . $PN . PHP_EOL
							. 'ორგანიზაცია: ' . $Org . PHP_EOL
							. 'სტრუქტურული ერთეული: ' . $OrgPlace . PHP_EOL
							. 'შტატი: ' . $SCH . PHP_EOL
							. 'პოზიცია: ' . $Position . PHP_EOL
							. 'კონტრაქტის ვადა: ' . $ContractsDate->toFormat( '%d-%m-%Y' ) . PHP_EOL
			;
			foreach ( $Mails as $To )
			{
				Cmail( $To, $Subject, nl2br( $Message ) );
			}
			echo C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker ) . ' - Done!' . PHP_EOL;
		}
	}

}
