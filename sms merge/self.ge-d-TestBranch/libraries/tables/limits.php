<?PHP

class HolidayLimitsTable extends TableLib_user_holiday_limitInterface
{
	public $_DATE_FIELDS = array(
			'END_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'START_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'lib_user_holiday_limit', 'ID', 'sqs_lib_user_holiday_limit.nextval' );

	}

	public function LoadUserLimits( $userID, $D, $Type )
	{
		$Date = PDate::Get( $D );
//		$HStartDay = 
		$Query = 'select * from ' . $this->_tbl . ' t '
						. ' where '
						. ' t.worker = ' . DB::Quote( $userID )
						. ' and trunc(to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\')) '
						. ' between t.start_date and t.end_date '
						. ' and t.htype = ' . DB::Quote( $Type )
		;
		$Data = DB::LoadObject( $Query );
		if ( empty( $Data ) )
		{
			$TypeConfig = $this->GetTypeConfig( $Type );
			$Dates = $this->_GetHolidayDates( $TypeConfig );
			$Start = PDate::Get( C::_( '0', $Dates ) );
			$End = PDate::Get( C::_( '1', $Dates ) );
			$Limit = C::_( 'LIMIT', $TypeConfig );
			$this->COUNT = $Limit;
			$this->WORKER = $userID;
			$this->HTYPE = $Type;
			$this->START_DATE = $Start->toFormat();
			$this->END_DATE = $End->toFormat();
			$this->YEAR = $Date->toFormat( '%Y' );
			$this->C_LIMIT = $Limit;
			$this->store();
		}
		else
		{
			$this->bind( $Data );
		}
		return $this->COUNT;

	}

	public function CheckUseds( $UserData, $Type, $Start, $End )
	{
		$this->resetAll();
		$this->bind( $UserData );
		$Limit = C::_( 'C_LIMIT', $UserData );
		$Q = 'select '
						. ' nvl(sum(t.day_count) , 0) '
						. ' from HRS_APPLICATIONS t '
						. ' where '
						. ' t. worker = ' . C::_( 'WORKER', $UserData )
						. ' and ('
						. ' trunc( t.start_date) between to_date(' . DB::Quote( $Start->toformat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\')  and  to_date(' . DB::Quote( $End->toformat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') '
						. ' or '
						. ' trunc( t.end_date) between to_date(' . DB::Quote( $Start->toformat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\')  and  to_date(' . DB::Quote( $End->toformat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') '
						. ' ) '
						. ' and t.type = ' . $Type
						. ' and t.status > 0 '
		;
		$UsedCount = (int) DB::LoadResult( $Q );
		if ( $UsedCount == 0 )
		{
			return true;
		}
		$CurrentLimit = C::_( 'COUNT', $UserData );
		$Diff = $Limit - $UsedCount;

		if ( $Diff != $CurrentLimit )
		{
			$this->COUNT = $Diff;
			$this->store();
		}

	}

	public function _GetHolidayDates( $TypeConfig, $Next = false, $Year = null )
	{
		$Period = C::_( 'PERIODICITY', $TypeConfig );
		switch ( $Period )
		{
			case 1:
				$Base = PDate::Get()->toFormat( '%Y-%m' );
				$StartDate = PDate::Get( $Base . '-01' );
				$EndDate = PDate::Get( 'last day of ' . $Base );
				break;
			case 2:
				$this->getQuarterStartEnd( $StartDate, $EndDate );
				$StartDate = PDate::Get( $StartDate );
				$EndDate = PDate::Get( $EndDate );
				break;
			default:
			case 3:
				if ( $Next )
				{
					$Year = intval( PDate::Get()->toFormat( '%Y' ) ) + 1;
				}
				else
				{
					$Year = PDate::Get()->toFormat( '%Y' );
				}
				$StartD = (int) C::_( 'HOLIDAY_RESTART_DAY', $TypeConfig );
				$StartM = (int) C::_( 'HOLIDAY_RESTART_MONTH', $TypeConfig );
				if ( !$StartD )
				{
					$StartD = 1;
				}
				if ( !$StartM )
				{
					$StartM = 1;
				}
				$CrYear = $Year;
				$StartDate = PDate::Get( $CrYear . '-' . $StartM . '-' . $StartD );
				$EndDate = PDate::Get( PDate::Get( ($CrYear + 1) . '-' . $StartM . '-' . $StartD )->toUnix() - 86400 );
				if ( $EndDate->toUnix() < $StartDate->toUnix() )
				{
					$StartDate = PDate::Get( ($CrYear - 1) . '-' . $StartM . '-' . $StartD );
					$EndDate = PDate::Get( PDate::Get( $CrYear . '-' . $StartM . '-' . $StartD )->toUnix() - 86400 );
				}
				break;
		}
		return array( $StartDate->toFormat(), $EndDate->toFormat() );

	}

	public function Generate( $Type = -1, $Next = false )
	{
		if ( $Type < 0 )
		{
			$Types = $this->GetHolidays();
		}
		else
		{
			$Types = (array) $Type;
		}
		foreach ( $Types as $Type )
		{
			$TypeConfig = $this->GetTypeConfig( $Type );
			if ( $Next )
			{
				$CurrentDates = $this->_GetHolidayDates( $TypeConfig );
				$End = PDate::Get( C::_( '1', $CurrentDates ) );
				$NextGenStart = $End->toUnix() - 30 * 86400;
				$Now = PDate::Get()->toUnix();
				if ( $Now < $NextGenStart )
				{
					continue;
				}
			}
			$Dates = $this->_GetHolidayDates( $TypeConfig, $Next );
			$Start = PDate::Get( C::_( '0', $Dates ) );
			$End = PDate::Get( C::_( '1', $Dates ) );
			$Workers = $this->GetWorkers( $Start->toFormat( '%Y-%m-%d' ), $End->toFormat( '%Y-%m-%d' ), $Type );
			foreach ( $Workers as $Worker )
			{
				$this->resetAll();
				$ULimit = $this->CalculateLimit( $Start, $End, $TypeConfig, $Worker );
				$this->HTYPE = $Type;
				$this->WORKER = C::_( 'WID', $Worker );
				$this->START_DATE = $Start->toFormat( '%Y-%m-%d' );
				$this->END_DATE = $End->toFormat( '%Y-%m-%d' );
				$this->AWORKER = Users::GetUserID();
				$this->COUNT = $ULimit;
				$this->C_LIMIT = $ULimit;
				$this->ORG = C::_( 'ORG_ID', $Worker );
				$this->YEAR = $Start->toFormat( '%Y' );
				$this->store();
			}
		}
		return true;

	}

	public function GetWorkers( $Start, $End, $Htype, $Limits = false )
	{
		$Query = 'select '
						. ' w.firstname, '
						. ' w.lastname, '
						. ' ss.assignment_date as contracts_date, '
						. ' w.id wid, '
						. ' w.org org_id, '
						. ' l.* '
						. ' from hrs_workers w '
						. ' left join rel_person_org ss on ss.id = w.id '
//						. ' left join (	select	ww.orgpid,	min(ww.contracts_date) contracts_date	from	slf_worker ww	where	ww.active = 1	group by	ww.orgpid) ss on ss.orgpid = w.id '
						. ' left join lib_user_holiday_limit l on w.id = l.worker and l.start_date = to_date( ' . DB::Quote( $Start ) . ', \'yyyy-mm-dd\') '
						. ' and l.end_date = to_date(' . DB::Quote( $End ) . ', \'yyyy-mm-dd\') '
						. ' and l.htype = ' . $Htype
						. ' where '
						. ' ss.active = 1 '
						. ' and w.active = 1 '
						. ' and w.id > 0 '
		;
		if ( $Limits )
		{
			$Query .= ' and l.id is not null ';
		}
		else
		{
			$Query .= ' and l.id is null ';
		}
		return DB::LoadObjectList( $Query );

	}

	public function CalculateLimit( $Start, $End, $TypeConfig, $Worker )
	{
		$HLimitAddType = C::_( 'HOLIDAY_START_LIMIT', $TypeConfig );
		$Limit = C::_( 'LIMIT', $TypeConfig );
		$Period = C::_( 'PERIODICITY', $TypeConfig );
		$RLimit = 0;
		switch ( $Period )
		{
			case 1:
				$RLimit = $Limit;
				break;
			case 2:
				$RLimit = $Limit;
			default:
			case 3:
				$LimitDays = 365;
				$StartDate = PDate::Get( C::_( 'CONTRACTS_DATE', $Worker ) );
				if ( $HLimitAddType == '1' )
				{
					$RLimit = $Limit;
				}
				else if ( $HLimitAddType == '2' && $Start->toUnix() >= $StartDate->toUnix() )
				{
					$RLimit = $Limit;
				}
				else if ( $HLimitAddType == '2' )
				{
					$Days = Helper::CalculateDayCount( $StartDate->toUnix(), $End->toUnix() );
					$RLimit = ceil( $Days * $Limit / $LimitDays );
				}
				break;
		}
		return $RLimit;

	}

	public function GetTypeConfig( $Type )
	{
		$Config = $this->GetHolidays( true );
		return C::_( $Type, $Config );

	}

	public function GetHolidays( $Full = false )
	{
		static $Apps = null;
		if ( is_null( $Apps ) )
		{
			$Query = 'select '
							. ' * '
							. ' from lib_limit_app_types a '
							. ' where '
//							. ' a.id=  ' . $Type
							. ' a.active = 1'

			;
			$Apps = (array) XRedis::getDBCache( 'lib_limit_app_types', $Query, 'LoadObjectList', 'ID' );
//			$Apps = DB::LoadObjectList( $Query, 'ID' );
		}
		if ( $Full )
		{
			return $Apps;
		}
		else
		{
			return array_keys( $Apps );
		}

	}

	public function getQuarterStartEnd( &$QStartDate, &$QEndDate )
	{
		$current_month = PDate::Get()->toFormat( '%m' );
		$current_year = PDate::Get()->toFormat( '%Y' );

		if ( $current_month >= 1 && $current_month <= 3 )
		{
			$QStartDate = strtotime( '1-January-' . $current_year ); // timestamp or 1-Januray 12:00:00 AM
			$QEndDate = strtotime( '31-March-' . $current_year ); // timestamp or 1-April 12:00:00 AM means end of 31 March
		}
		else if ( $current_month >= 4 && $current_month <= 6 )
		{
			$QStartDate = strtotime( '1-April-' . $current_year ); // timestamp or 1-April 12:00:00 AM
			$QEndDate = strtotime( '31-June-' . $current_year ); // timestamp or 1-July 12:00:00 AM means end of 30 June
		}
		else if ( $current_month >= 7 && $current_month <= 9 )
		{
			$QStartDate = strtotime( '1-July-' . $current_year ); // timestamp or 1-July 12:00:00 AM
			$QEndDate = strtotime( '1-September-' . $current_year ); // timestamp or 1-October 12:00:00 AM means end of 30 September
		}
		else if ( $current_month >= 10 && $current_month <= 12 )
		{
			$QStartDate = strtotime( '1-October-' . $current_year ); // timestamp or 1-October 12:00:00 AM
			$QEndDate = strtotime( '1-December-' . ($current_year + 1) ); // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
		}

	}

	public function SyncHolidayLimits()
	{
		$Diffs = $this->GetDiffs();
		foreach ( $Diffs as $Diff )
		{
			$this->resetAll();
			$this->bind( $Diff );
			$this->AWORKER = 0;
			$this->COUNT = C::_( 'N_COUNT', $Diff );
			$this->store();
		}

	}

	public function GetDiffs()
	{
		$Query = 'select 
			 f.*, 
			 (f.c_limit-f.used) n_count
	from (
	select
		l.*,
		(select
			nvl(sum(ha.day_count), 0)
		from
			hrs_applications ha
		where
			ha.worker = l.worker
			and ha.type = l.htype
			and ha.status >0
			and ha.start_date >= l.start_date
                and ha.end_date <= l.end_date			
) used
	from
		 lib_user_holiday_limit l
	left join hrs_workers w
            on
		w.id = l.worker
	where
		w.active = 1
		and sysdate < l.end_date
		) f
where
	to_char(f.end_date, \'yyyy\') >= 2022
	and (f.c_limit-f.used) != f.count  ';
		return DB::LoadObjectList( $Query );

	}

	public function CheckHolidayLimit( $Day, $type, $Worker, $Date = 'now' )
	{
		$UserData = $this->GetWorkerLimit( $Worker, $type, $Date );
		$Limit = C::_( 'COUNT', $UserData );
		if ( $Day > $Limit )
		{
			return false;
		}
		return true;

	}

	public function GetWorkerLimit( $Worker, $type, $Date )
	{
		$Query = 'select '
						. ' l.* '
						. ' from lib_user_holiday_limit l '
						. 'where '
						. ' to_date(' . DB::Quote( PDate::Get( $Date )->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') between l.start_date and  l.end_date '
						. ' and l.htype = ' . $type
						. ' and l.worker = ' . $Worker
						. ' and l.worker  > 0 '
		;
		return DB::LoadObject( $Query );

	}

	public static function GetHolidayIDx( $Type = '-1', $Format = 'csv' )
	{
		static $R = null;
		static $W = null;
		static $WL = null;
		if ( is_null( $R ) )
		{
			$Query = 'select '
							. ' a.id,'
							. ' a.wage_type '
							. ' from lib_limit_app_types a '
//							. ' where '
//							. ' a.active = 1'
			;
			$RR = XRedis::getDBCache( 'lib_limit_app_types', $Query, 'LoadObjectList', 'ID' );
//			$RR = DB::LoadObjectList( $Query, 'ID' );
			foreach ( $RR as $Key => $Item )
			{
				$R[$Key] = $Key;
				$WType = C::_( 'WAGE_TYPE', $Item );
				if ( $WType == 0 )
				{
					$W[$Key] = $Key;
				}
				else
				{
					$WL[$Key] = $Key;
				}
			}
		}

		if ( $Type == 0 )
		{
			return self::FormatKey( $W, $Format );
		}

		if ( $Type == 1 )
		{
			return self::FormatKey( $WL, $Format );
		}

		return self::FormatKey( $R, $Format );

	}

	public static function FormatKey( $D, $Format )
	{
		if ( $Format == 'csv' )
		{
			return implode( ',', $D );
		}
		return $D;

	}

}
