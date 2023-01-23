<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

//Configuration
$Config = Helper::getConfig( 'worker_auto_dismission' );
if ( !$Config )
{
	die( 'Dismission Process Disabled By Config!' );
}
$Hour = (int) Helper::getConfig( 'worker_auto_dismission_hour' );
$HourNow = PDate::Get()->toFormat( '%H' );
if ( $Hour > $HourNow )
{
	die( 'Not In Hour!' );
}

$Dissmision_MSG = (int) Helper::getConfig( 'worker_auto_dismission_msg' );
$Dissmision_EMAILS = trim( Helper::getConfig( 'dismission_alerts_mails' ) );
$Dissmision_TYPE = trim( Helper::getConfig( 'dismission_release_type' ) );

$Q = 'select w.*, 
	po.lib_title POSITION,
	sc.lib_title STAFF_SCHEDULE_NAME,
	un.lib_title ORG_PLACE,
	u.lib_title ORG_NAME,
	ss.private_number,
	ss.firstname,
	ss.lastname,
	ss.email
	from	slf_worker w 
	left join slf_persons ss on ss.id = w.person
	left join lib_unitorgs u on u.id = w.org
	left join lib_staff_schedules sc on sc.id = w.staff_schedule
	left join lib_units un on un.id = sc.org_place
	left join lib_positions po on po.id = sc.position
      where
	w.active = 1
	and  trunc(w.contract_end_date) <= trunc(sysdate)'
;
$Targets = DB::LoadObjectList( $Q );

$Slf_changesTable = new TableSlf_changesInterface( 'slf_changes', 'ID' );

if ( $Targets )
{
	$Date = new PDate();
	$link = PATH_BASE . DS . 'components' . DS . 'person_org' . DS . 'model.php';
	if ( !is_file( $link ) )
	{
		echo '<pre><pre>';
		print_r( 'Invalid file: ' . $link );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
		die;
	}
	require_once $link;
	$model = new Person_orgModel( [] );
	foreach ( $Targets as $data )
	{
		$command = [];
		$command['WORKERS'] = C::_( 'ID', $data );
		$command['ORG'] = C::_( 'ORG', $data );
		$command['CHANGE_DATE'] = $Date->toFormat();
		$command['RELEASE_TYPE'] = $Dissmision_TYPE;
		$command['RELEASE_COMMENT'] = 'Auto Release';
		if ( $model->save_release( $command ) )
		{
			SendEmail( $Dissmision_EMAILS, 'Auto Release', $data );
			echo '<pre><pre>';
			print_r( 'Auto released worker: ' . C::_( 'ID', $data ) );
			echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
		}
	}
}
else
{
	echo '<pre><pre>';
	print_r( 'No workers detected.' );
	echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
}


function SendEmail( $Emails = '', $Subject = '', $data = [] )
{
	if ( !empty( $data ) && !empty( $Emails ) )
	{
		$EMS = explode( ',', $Emails );
		$Message = 'თანამშრომლის კონტრაქტს გაუვიდა ვადა და გათავისუფლდა დანიშნული პოზიციიდან: ' . PHP_EOL . PHP_EOL
						. 'თანამშრომელი: ' . C::_( 'FIRSTNAME', $data ) . ' ' . C::_( 'LASTNAME', $data ) . PHP_EOL
						. 'პ/ნ: ' . C::_( 'PRIVATE_NUMBER', $data ) . PHP_EOL
						. 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $data ) . PHP_EOL
						. 'სტრუქტურული ერთეული: ' . C::_( 'ORG_PLACE', $data ) . PHP_EOL
						. 'შტატი: ' . C::_( 'STAFF_SCHEDULE_NAME', $data ) . PHP_EOL
						. 'პოზიცია: ' . C::_( 'POSITION', $data ) . PHP_EOL
						. 'კონტრაქტის ვადა: ' . C::_( 'CONTRACT_END_DATE', $data ) . PHP_EOL
		;
		foreach ( $EMS as $email )
		{
			Cmail( trim( $email ), $Subject, nl2br( $Message ) );
		}
	}

}
