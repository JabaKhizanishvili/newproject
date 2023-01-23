<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');
$st = microtime( 1 );

$Config = Helper::getConfig( 'SENDING_BULLETIN_LIMIT_OF_MISCONDUCT_MESSAGES' );
if ( !$Config )
{
	die( 'Bulletin Alerts Disabled By Config!' );
}

$Day1 = (int) Helper::getConfig( 'THE_FIRST_LIMIT_TO_SEND_A_MESSAGE' );
$Day2 = (int) Helper::getConfig( 'THE_SECOND_LIMIT_TO_SEND_A_MESSAGE' );
$Day3 = (int) Helper::getConfig( 'THE_THIRD_LIMIT_TO_SEND_A_MESSAGE' );
$Hour = (int) Helper::getConfig( 'NOTIFICATION_SEND_TIME' );
$HourNow = PDate::Get()->toFormat( '%H' );
if ( $Hour > $HourNow )
{
	die( 'Not In Hour!' );
}

$Mails = Helper::CleanArray( explode( ',', Helper::getConfig( 'EMAILS_TO_SEND_NOTIFICATION' ) ), 'Str' );
if ( !count( $Mails ) )
{
	die( 'Mail List is empty' );
}

$LockDir = PATH_LOGS . DS . 'BulletinAlertsLock';
$sentAlertsLockDir = $LockDir . '/sentAlerts';
if ( !is_dir( $LockDir ) )
{
	Folder::create( $LockDir, 0777 );
}
if (!is_dir($sentAlertsLockDir)) {
    Folder::create( $sentAlertsLockDir, 0777 );
}

$LockFile = $LockDir . DS . 'lock-' . date( 'Y-m-d' );
if ( is_file( $LockFile ) ) {
	die( 'Task Job Must Run Only Once!' );
}
file_put_contents( $LockFile, date( 'Y-m-d  H:i:s' ) );

print ('<pre>Run Bulletin Alerts Script - ' . $Day1 . ' <br /> ');
SendAlerts( $Day1, $Mails );
echo '<pre>Run Bulletin Alerts Script - ' . $Day2 . ' <br /> ';
SendAlerts( $Day2, $Mails );
echo '<pre>Run Bulletin Alerts Script - ' . $Day3 . ' <br /> ';
SendAlerts( $Day3, $Mails );

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
	$Query = 'SELECT 
	rpo.id,
	sp.FIRSTNAME,
	sp.LASTNAME,
	sp.PRIVATE_NUMBER,
	lu.lib_title ORG_NAME,
	d.APP_IDS
FROM (SELECT
	g.id,
	LISTAGG(g.APPID, \',\') WITHIN GROUP (ORDER BY g.APPID) APP_IDS
FROM
	(
	SELECT
		k.id,
		k.APPID,
		k.EndDate - k.StartDate + 1 days
	FROM
		(
		SELECT
			rpo.id,
			ha.ID APPID,
			CASE
				WHEN ha.START_DATE < sysdate - 180 THEN
	trunc(sysdate) - 180
				ELSE
	trunc(ha.START_DATE)
			END StartDate,
			CASE
				WHEN ha.END_DATE > sysdate THEN
	trunc(sysdate)
				ELSE
	trunc(ha.END_DATE)
			END EndDate
		FROM
			HRS_APPLICATIONS ha
			LEFT JOIN rel_person_org rpo ON rpo.id = ha.worker
		WHERE
			ha.TYPE = 5
			AND 
	HA.STATUS > 0
	AND rpo.active = 1
			AND 
	(
		ha.START_DATE BETWEEN sysdate - 180 AND SYSDATE
				OR ha.END_DATE BETWEEN sysdate - 180 AND SYSDATE 
	) ) k
	) G
GROUP BY
	g.id
HAVING
	sum(g.days) = ' . $Day . ') d
LEFT JOIN REL_PERSON_ORG rpo ON d.id = rpo.id
LEFT JOIN SLF_PERSONS sp ON rpo.PERSON = sp.id
LEFT JOIN LIB_UNITORGS lu ON lu.ID = rpo.ORG ';

	return DB::LoadObjectList( $Query );

}

function SendAlerts( $Day, $Mails )
{
    global $sentAlertsLockDir;

	$Items = GetWorkers( $Day );

	if ( !empty( $Items ) )
	{
		foreach ( $Items as $Worker )
		{
			$Firstname = C::_( 'FIRSTNAME', $Worker );
			$Lastname = C::_( 'LASTNAME', $Worker );
			$PN = C::_( 'PRIVATE_NUMBER', $Worker );
			$Org = C::_( 'ORG_NAME', $Worker );

            $sentAlertLockFile = $sentAlertsLockDir . DS . 'lock-' . md5($Worker->APP_IDS);

            if ( is_file( $sentAlertLockFile ) ) {
                continue;
            }

            file_put_contents( $sentAlertLockFile, date( 'Y-m-d  H:i:s' ) );

			$Subject = $Firstname . ' ' . $Lastname . ' - თანამშრომლის ბიულეტენის ლიმიტის შეტყობინება';

			$Message = 'გაცნობებთ, რომ ბოლო 6 თვის განმავლობაში თანამშრომელი ბიულეტენზე იმყოფებოდა ' . $Day . ' დღე.' . PHP_EOL
                     . 'თანამშრომელი: ' . $Firstname . ' ' . $Lastname . PHP_EOL
                     . 'პ/ნ: ' . $PN . PHP_EOL
                     . 'ორგანიზაცია: ' . $Org . PHP_EOL;

			foreach ( $Mails as $To )
			{
				Cmail( $To, $Subject, nl2br( $Message ) );
			}
			print (C::_( 'FIRSTNAME', $Worker ) . ' ' . C::_( 'LASTNAME', $Worker ) . ' - Done!' . PHP_EOL);
		}
	}
}
