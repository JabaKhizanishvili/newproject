<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = str_replace( '/' . basename( dirname( __FILE__ ) ), '', dirname( __FILE__ ) );
define( 'PATH_BASE', $base );
session_start();
include(PATH_BASE . DS . 'define.php');
include(PATH_BASE . DS . 'include.php');

$Emails = [];
$Emails_on = Helper::getConfig( 'controller_alert_email_switch', 0 );
if ( $Emails_on == 1 )
{
	$Emails = Helper::CleanArray( explode( ',', Helper::getConfig( 'controller_alert_email' ) ), 's' );
}

$Numbers = [];
$SMS_on = Helper::getConfig( 'controller_alert_sms_switch', 0 );
if ( $SMS_on == 1 )
{
	$Numbers = Helper::CleanArray( explode( ',', Helper::getConfig( 'controller_alert_sms' ) ), 's' );
}

$domain = X_DOMAIN . '.self.ge';
$system_email = 'alert@self.ge';
$critical_subject = Helper::getConfig( 'controller_alert_critical_subject', '' );
$resolved_subject = Helper::getConfig( 'controller_alert_resolved_subject', '' );

$query = ' select * from ( select '
				. ' c.id, '
				. ' c.lib_title, '
//				. ' a.rec_date,'
				. ' c.controller_code,nvl(cc.active, 1) current_status, '
				. ' nvl(aa.status, 0) action_status '
				. ' from lib_controllers c '
				. ' left join (select * from slf_api_controllers_alerts ll inner join (select max(al.id) max_id from slf_api_controllers_alerts al group by al.device_id) aa on aa.max_id = ll.id) cc on cc.device_id = c.id '
				. ' left join (select al.device_id, case when (count(1) / to_number(getconfig(\'controller_check_interval\'))) * 100 <= to_number(getconfig(\'controller_min_callback\')) then 0 else 1 end status from slf_api_controllers al where al.rec_date > sysdate - 1 / 24 / 60 * to_number(getconfig(\'controller_check_interval\')) group by al.device_id) aa on aa.device_id = c.id '
//				. ' left join slf_api_controllers a on a.controller_code = c.controller_code '
				. ' where c.active = 1 and c.alert = 1) k '
				. ' where k.current_status != k.action_status '
;
$result = DB::LoadObjectList( $query );

if ( !empty( $data ) )
{
	return false;
}

$Lib_controllers = new Tablelib_controllersInterface( 'lib_controllers', 'ID', 'sqs_controllers.nextval' );
$Api_Table = new TableSlf_api_controllers_alertsInterface( 'slf_api_controllers_alerts', 'ID', 'sqs_api_controllers_alerts.nextval' );

foreach ( $result as $data )
{
	$active = 0;
	$Subject = '';
	$TextLines = [];
	$id = C::_( 'ID', $data );

	if ( C::_( 'ACTION_STATUS', $data ) > C::_( 'CURRENT_STATUS', $data ) )
	{
		$active = 1;
		// Send On alert
		$Subject = $domain . ' - device status alert - RESOLVED - ' . $resolved_subject;
		$TextLines = [];
		$TextLines[] = 'მოგესალმებით';
		$TextLines[] = 'გაცნობებთ, რომ კონტროლერთან კავშირი აღდგა.';
		$TextLines[] = 'სისტემა: ' . $domain;
		$TextLines[] = 'კონტროლერი: ' . C::_( 'LIB_TITLE', $data ) . ' - ' . C::_( 'CONTROLLER_CODE', $data );
		$TextLines[] = 'კავშირის აღდგენის დაფიქსირების დრო: ' . PDate::Get()->toFormat();

		update_sub_data( $id, $Api_Table );
		connection_status_update( $id, 1, $Lib_controllers );
	}
	else
	{
		// Send On alert
		$Subject = $domain . ' - device status alert - CRITICAL - ' . $critical_subject;
		$TextLines = [];
		$TextLines[] = 'მოგესალმებით';
		$TextLines[] = 'გაცნობებთ, რომ ფიქსირდება კონტროლერთან კავშირის გათიშვა.';
		$TextLines[] = 'სისტემა: ' . $domain;
		$TextLines[] = 'კონტროლერი: ' . C::_( 'LIB_TITLE', $data ) . ' - ' . C::_( 'CONTROLLER_CODE', $data );
		$TextLines[] = 'კავშირის შეფერხების დაფიქსირების დრო: ' . PDate::Get()->toFormat();

		insert_sub_data( $id, $active, $Api_Table );
		connection_status_update( $id, 0, $Lib_controllers );
	}

	$Emails[] = $system_email;
	Mail::multiSend( $Emails, $Numbers, $Subject, $TextLines, $id );
}

clean_previous_data();
function clean_previous_data()
{
	if ( rand( 1, 20 ) != 20 )
	{
		return false;
	}

	$query = 'delete from slf_api_controllers c where c.rec_date < sysdate - 1 ';
	if ( !DB::Delete( $query ) )
	{
		return false;
	}

	return true;

}

send_lateness_alert( $system_email, $domain );
function send_lateness_alert( $Email = '', $domain = '' )
{
	$query = ' select '
					. ' cc.*,'
					. ' to_char(ss.controler_date, \'yyyy-mm-dd hh24:mi:ss\') controler_date'
					. ' from lib_controllers cc '
					. ' left join (select max(device_id) device_id, max(controler_date) controler_date from slf_api_controllers) ss on ss.device_id = cc.id '
					. ' where cc.id in '
					. ' (select c.device_id from slf_api_controllers c where '
					. ' c.rec_date > sysdate - (1/24/60) * 30 '
					. ' and nvl(c.controler_date, c.rec_date) not between c.rec_date - 1/24/60 and c.rec_date + 1/24/60 '
					. ' group by c.device_id having count(1) > 5) '
	;
	$result = DB::LoadObjectList( $query );
	if ( empty( $result ) )
	{
		return false;
	}

	foreach ( $result as $data )
	{
		$Subject = $domain . ' - TIME ERROR';
		$TextLines = [];
		$TextLines[] = 'მოგესალმებით';
		$TextLines[] = 'ფიქსირდება დროის შეცდომა';
		$TextLines[] = 'სისტემა: ' . $domain;
		$TextLines[] = 'კონტროლერი: ' . C::_( 'LIB_TITLE', $data ) . ' - ' . C::_( 'CONTROLLER_CODE', $data );
		$TextLines[] = 'კონტროლერის დრო: ' . C::_( 'LIB_TITLE', $data );
		$TextLines[] = 'რეალური დრო: ' . PDate::Get()->toFormat();

		Mail::sendAppEMAIL( $Email, $Subject, $TextLines, C::_( 'ID', $data ) );
	}

	return true;

}

function insert_sub_data( $id, $active, $Api_Table = null )
{
	$Api_Table->resetAll();
	$Api_Table->DEVICE_ID = $id;
	$Api_Table->OFF_DATE = PDate::Get()->toFormat();
	$Api_Table->ACTIVE = $active;
	if ( !$Api_Table->store() )
	{
		return false;
	}

	return true;

}

function update_sub_data( $id, $Api_Table = null )
{
	if ( empty( $id ) )
	{
		return false;
	}

	$Api_Table->resetAll();
	$Api_Table->loads( [
			'DEVICE_ID' => (int) $id,
			'ACTIVE' => 0,
	] );

	$Api_Table->ON_DATE = PDate::Get()->toFormat();
	$Api_Table->ACTIVE = 1;
	if ( !$Api_Table->store() )
	{
		return false;
	}

	return true;

}

function connection_status_update( $id, $status, $Lib_controllers = null )
{
	if ( empty( $id ) || !isset( $status ) )
	{
		return false;
	}

	$Lib_controllers->resetAll();
	$Lib_controllers->load( $id );
	$Lib_controllers->CONNECTION_STATUS = $status;
	if ( !$Lib_controllers->store() )
	{
		return false;
	}

	return true;

}

echo '<span style="color:green;font-weight:bold;">Controllers data updated!</span>';
