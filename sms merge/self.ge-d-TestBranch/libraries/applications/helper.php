<?php
define( 'APP_HOLIDAY_WAGES', 0 );
define( 'APP_HOLIDAY_WAGELESS', 1 );
define( 'APP_PRIVATE_TIME', 2 );
define( 'APP_BULLETINS', 5 );
define( 'APP_OFFICIAL', 6 );
define( 'APP_MISSION', 7 );
define( 'APP_OVERTIME', 11 );
define( 'APP_AUTO_OVERTIME', 13 );
define( 'APP_BEFORE_AUTO_OVERTIME', 15 );
define( 'APP_MISSING', 17 );

abstract class AppHelper
{
	public static function RegisterOfficial( $User, $Reason = '', $Comment = '' )
	{
		$Result = self::HasApplication( $User, APP_OFFICIAL );
		if ( $Result )
		{
			XError::setError( 'tqven ukve shevsebuli gaqvst samsaxureobrivi gasvlis ganacxadi!' );
			return false;
		}
		$UserDayTimes = self::GetUserDayDates( $User->ID );
		$UserTimeID = C::_( 'UserTimeID', $UserDayTimes );
		$DayStart = C::_( 'DayStart', $UserDayTimes );
		$DayEnd = C::_( 'DayEnd', $UserDayTimes );
		if ( $UserTimeID == 0 )
		{
			XError::setError( 'samsaxurebrivi ganacxadis shevseba unda ganxorcieldes samushao dges!' );
			return false;
		}
		if ( empty( $DayStart ) )
		{
			XError::setError( 'samushao drois dasawyisis gansazgvra ver ganxorcielda!' );
			return false;
		}
		if ( empty( $DayEnd ) )
		{
			XError::setError( 'samushao drois dasrulebis gansazgvra ver ganxorcielda!' );
			return false;
		}
		$Now = new PDate();
		$StartDate = $Now;
		$OutDate = new PDate( self::GetUserLogOutData( $User->ID ) );
		$DayStartDate = new PDate( $DayStart );
		$DayEndDate = new PDate( $DayEnd );
		if ( $OutDate->toUnix() < $DayStartDate->toUnix() )
		{
			$StartDate = $DayStartDate;
		}
		else if ( $OutDate->toUnix() < $Now->toUnix() )
		{
			$StartDate = $OutDate;
		}
		else if ( $OutDate->toUnix() > $DayStartDate->toUnix() )
		{
			$StartDate = $OutDate;
		}
		else
		{
			$StartDate = $Now;
		}

		$APPTable = self::getTable( false );
		$APPTable->WORKER = $User->ID;
		if ( !$APPTable->ID )
		{
			$APPTable->REC_USER = $User->ID;
		}
		$APPTable->TYPE = APP_OFFICIAL;
		$APPTable->START_DATE = $StartDate->toFormat();
		$APPTable->END_DATE = $DayEndDate->toFormat();
		$APPTable->STATUS = 1;
		$APPTable->INFO = $Reason;
		$APPTable->UCOMMENT = $Comment;
		$APPTable->check();
		if ( $APPTable->store() )
		{
			self::SendOficialSMSAlertToCheaf( $User, $Reason, $Comment );
			self::SendOficialEmailAlertToCheaf( $User, $Reason, $Comment );
		}
		else
		{
			return false;
		}
		return $APPTable->insertid();

	}

	/**
	 * 
	 * @staticvar type $Table
	 * @return ApplicationsTable object
	 */
	public static function getTable( $Instance = true )
	{
		static $Table = null;
		if ( is_null( $Table ) )
		{
			require_once dirname( __FILE__ ) . DS . 'tables' . DS . 'applications.php';
			$Table = new ApplicationsTable();
		}

		if ( $Instance == FALSE )
		{
			$Table = new ApplicationsTable();
		}
		return $Table;

	}

	public static function IsValidPTDate( $PDate )
	{
		$DayStart = new PDate( );
		$Start = new PDate( $DayStart->toFormat( '%Y-%m-%d' ) );
		$Date = new PDate( $PDate );
		if ( $Start->toUnix() <= $Date->toUnix() )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public static function IsValidTime( $time )
	{
		if ( strtotime( $time ) )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public static function GetUserDayDates( $Worker, $Date = null )
	{
		static $Results = array();
		if ( empty( $Date ) )
		{
			$Date = new PDate();
		}
		$Day = $Date->toFormat( '%j' );
		if ( !isset( $Results[$Day][$Worker] ) )
		{
			$Procedure = 'pkg_hrs_helper.getUserDayTimesAsText';
			$Params = array(
					':p_user_id' => (int) $Worker,
					':p_date' => $Date->toFormat( '%Y-%m-%d' ),
					':v_start' => 'null',
					':v_end' => 'null',
					':v_bstart' => 'null',
					':v_bend' => 'null',
					':hrs_user_time_id' => 0
			);
			$Dates = DB::callProcedure( $Procedure, $Params );
			$Return = array();
			$Return['UserTimeID'] = C::_( 'params.:hrs_user_time_id', $Dates );
			$Return['DayStart'] = C::_( 'params.:v_start', $Dates );
			$Return['DayEnd'] = C::_( 'params.:v_end', $Dates );
			$Return['BreakStart'] = C::_( 'params.:v_bstart', $Dates );
			$Return['BreakEnd'] = C::_( 'params.:v_bend', $Dates );
			$Results[$Day][$Worker] = $Return;
		}
		return $Results[$Day][$Worker];

	}

	public static function HasApplication( $User, $Type, $Date = null )
	{
		if ( empty( $Date ) )
		{
			$Now = new PDate();
			$Date = $Now->toFormat();
		}
		$SQL = 'select count(*)   from hrs_applications t  where t.worker = ' . C::_( 'ID', $User ) . ' and t.status > -1 and sysdate between t.start_date and t.end_date';
		return DB::LoadResult( $SQL );

	}

	public static function GetUserLogOutData( $Staff_ID )
	{
		$query = ' select '
						. ' to_char(decode(k.event_date, null, sysdate, k.event_date), \'yyyy-mm-dd hh24:mi:ss\')  event_date'
						. ' from ( '
						. ' select '
						. ' * '
						. ' from HRS_STAFF_EVENTS e '
						. ' where '
						. ' e.staff_id = ' . $Staff_ID
						. ' and e.event_date < sysdate '
						. ' and e.real_type_id in (2, 3000, 2000) '
						. ' and e.event_date > sysdate - 1 '
						. ' order by e.event_date desc'
						. ' ) k '
						. ' where '
						. ' rownum < 2 ';
		return DB::LoadResult( $query );

	}

	public static function SendOficialSMSAlertToCheaf( $User, $Reason, $Comment )
	{
		include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';
		$sms = new oneWaySMS();
		$Phones = self::GetChiefsForAlert( $User->ID, 'official' );
		foreach ( $Phones as $Phone )
		{
			$text = 'თანამშრომლის გასვლის რეგისტრაცია.' . PHP_EOL
							. 'თანამშრომელი: ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . PHP_EOL
							. 'მიზეზი: ' . $Reason . '(' . $Comment . ')' . PHP_EOL
			;
			$sms->SendSMS( $text, $Phone );
		}

	}

	public static function SendOficialEmailAlertToCheaf( $User, $Reason, $Comment )
	{
		$Emails = self::GetChiefsForAlert( $User->ID, 'official', 2 );
		foreach ( $Emails as $Email )
		{
			$Subject = 'სამსახურებრივი გასვლის რეგისტრაცია';
			$message = 'თანამშრომლის გასვლის რეგისტრაცია.' . PHP_EOL
							. 'თანამშრომელი: ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . PHP_EOL
							. 'მიზეზი: ' . $Reason . '(' . $Comment . ')' . PHP_EOL
			;
			return Mail::Send( $Email, $Subject, nl2br( $message ) );
		}

	}

	public static function GetChiefsForAlert( $UserID, $Type, $AlertType = 1 )
	{
		$chiefs = Helper::getUserChiefs( $UserID );
		$Key = null;
		switch ( $Type )
		{
			case 'official':
				$Key = 'WORKERS_OFFICIAL';
				break;

			default:
				break;
		}
		$return = array();
		foreach ( $chiefs as $chief )
		{
			$Value = C::_( $Key, $chief );
			if ( $Value != $AlertType )
			{
				continue;
			}
			if ( $Value == 1 )
			{
				$mobile = C::_( 'MOBILE_PHONE_NUMBER', $chief );
				if ( $mobile )
				{
					$return[] = $mobile;
				}
			}
			else if ( $Value == $AlertType )
			{
				$Email = C::_( 'EMAIL', $chief );
				if ( $Email )
				{
					$return[] = $Email;
				}
			}
			else
			{
				continue;
			}
//			if($)
		}
		return $return;

	}

	public static function CheckDirectApprove( $Type )
	{
		$Q = 'select t.flow from LIB_LIMIT_APP_TYPES t where t.id = ' . (int) $Type;
		return !DB::LoadResult( $Q );

	}

}
