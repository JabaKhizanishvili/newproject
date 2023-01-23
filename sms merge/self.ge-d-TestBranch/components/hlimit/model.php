<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class hlimitModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new hlimitsTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		if ( $this->Table->ID )
		{
			$this->Table->WORKERNAME = self::GetUserFullName( $this->Table->WORKER );
		}
		return $this->Table;

	}

	public static function GetUserFullName( $ID = NULL )
	{
		if ( !is_null( $ID ) )
		{
			$ID = (int) $ID;
			$User = self::getUser( $ID );
			return C::_( 'FIRSTNAME', $User ) . ' ' . C::_( 'LASTNAME', $User );
		}
		return false;

	}

	public static function getUser( $ID = NULL )
	{
		if ( !is_null( $ID ) )
		{
			$ID = (int) $ID;
			static $Users = array();
			if ( !isset( $Users[$ID] ) )
			{
				$Query = ' Select * from hrs_workers '
								. ' where ID = ' . (int) $ID
				;
				$Users[$ID] = DB::LoadObject( $Query );
			}
			return $Users[$ID];
		}
		return false;

	}

	public function Generate( $Next = false )
	{
		$Htype = trim( Request::getInt( 'htype' ) );
		if ( $Htype == '' )
		{
			return false;
		}
		$LimitsTable = new HolidayLimitsTable();
		return $LimitsTable->Generate( $Htype, $Next );

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$this->Table->Delete( $id );
			}
		}
		return true;

	}

	public function GetPeriod()
	{
		$Htype = trim( Request::getInt( 'htype' ) );
		if ( $Htype == '' )
		{
			return false;
		}
		$Period = trim( Request::getVar( 'hperiod' ) );
		if ( $Period == '' )
		{
			return false;
		}
		$Data = $this->_GetPeriod( $Period );
		$Data->HT = $Htype;
		return $Data;

	}

	public function _GetPeriod( $Period )
	{
		$Query = 'select '
						. ' k.p, '
						. ' k.p_start, '
						. ' k.p_end, '
						. ' k.p_cur '
						. ' from ( '
						. ' select '
						. ' to_char(t.start_date, \'ddmmyy\') || to_char(t.end_date, \'ddmmyy\') p, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') p_start, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') p_end, '
						. ' case when sysdate between t.start_date and t.end_date then 1 else 0 end p_cur '
						. ' from lib_user_holiday_limit t '
						. ' where '
						. ' t.start_date is not null '
						. ' and t.end_date is not null'
						. ' ) k '
						. ' where k.p=' . DB::Quote( $Period )
						. ' group by k.p, k.p_start, k.p_end, k.p_cur'
		;
		return DB::LoadObject( $Query );

	}

	public function SaveData( $data )
	{
		$ID = C::_( 'ID', $data );
		$this->Table->load( $ID );
		return parent::SaveData( $data );

	}

	public function SavePeriod( $data )
	{
		$PStart = trim( C::_( 'P_START', $data ) );
		$PEnd = trim( C::_( 'P_END', $data ) );
		$P = trim( C::_( 'P', $data ) );
		$HT = trim( C::_( 'HT', $data ) );
		if ( empty( $PStart ) )
		{
			return false;
		}
		if ( empty( $PEnd ) )
		{
			return false;
		}
		if ( empty( $P ) )
		{
			return false;
		}
		if ( $HT == '' )
		{
			return false;
		}
		$DBData = $this->_GetPeriod( $P );
		if ( empty( $DBData ) )
		{
			return false;
		}

		$Query = 'update lib_user_holiday_limit l set '
						. ' l.start_date =to_date( ' . DB::Quote( PDate::Get( $PStart )->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\' ), '
						. ' l.end_date =to_date( ' . DB::Quote( PDate::Get( $PEnd )->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\' ) '
						. ' where '
						. ' to_char(l.start_date, \'ddmmyy\') || to_char(l.end_date, \'ddmmyy\') =' . DB::Quote( $P )
						. ' and htype = ' . DB::Quote( $HT )
		;
		$R = DB::Update( $Query );
		XRedis::CleanDBCache( 'lib_user_holiday_limit' );
		return $R;

	}

}
