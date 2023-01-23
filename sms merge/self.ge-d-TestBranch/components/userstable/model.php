<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class userstableModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new TableUHRS_Table( );
		parent::__construct( $params );

	}

	public function getItem( $BillID )
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->loads( array(
					'WORKER' => C::_( '0', $id ),
					'BILL_ID' => $BillID,
			) );
//			$this->Table->loads( $id[0] );
		}
		$this->Table->BILL_ID = $BillID;
		$this->Table->WORKER = C::_( '0', $id );
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$id = Collection::get( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
//		$User =  Users::getUser( C::_( 'WORKER', $data ) );
//		if ( $id )
//		{
//			Mail::sendLeaveEditMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser( C::_( 'WORKER', $data ) ) );
//		}
//		else
//		{
//			Mail::sendLeaveMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ) );
//		}
		return $this->Table->insertid();

	}

	public function Approve()
	{
		$idx = Request::getVar( 'nid', array() );
		$BillID = Request::getVar( 'bill_id', 0 );
		if ( empty( $BillID ) )
		{
			return false;
		}
		if ( is_array( $idx ) )
		{
			$date = new PDate();
			foreach ( $idx as $id )
			{
				$this->Table->loads( array( 'BILL_ID' => $BillID, 'WORKER' => $id, 'STATUS' => 0 ) );
				if ( empty( $this->Table->ID ) )
				{
					$this->Table->loads( array( 'BILL_ID' => $BillID, 'WORKER' => $id, 'STATUS' => 1 ) );
				}
				if ( $this->Table->STATUS == 2 )
				{
					continue;
				}
				$this->Table->STATUS = 2;
				$this->Table->APPROVE = Users::GetUserID();
				$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
			return true;
		}

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				if ( Collection::get( 'STATUS', $this->Table, 0 ) != 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'Holiday Already Approved!' );
					Users::Redirect( $link );
				}
				$this->Table->STATUS = -2;
				$this->Table->DEL_USER = Users::GetUserID();
				$this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

	}

	public function GetAPPS( $UserID, $StartDate, $EndDate )
	{
		$Query = ' select '
						. ' to_char(t.start_date, \'yyyy-mm-dd\')  start_date, '
						. ' to_char(t.end_date, \'yyyy-mm-dd\')  end_date, '
						. ' a.lib_title title, '
						. ' a.type '
						. ' from HRS_APPLICATIONS t '
						. ' left join lib_applications_types a on a.type = t.type '
						. ' where '
						. ' t.worker = ' . DB::Quote( $UserID )
						. ' and t.status > 0 '
						. ' and ( '
						. ' t.start_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') '
						. ' or t.end_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') '
						. ' or to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') between t.start_date and t.end_date '
						. ' or to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') between t.start_date and t.end_date '
						. ' ) '
						. ' and t.type in (0, 1, 3, 4, 5, 11, 12, 13, 14) '
		;

		$Dates = DB::LoadObjectList( $Query );
		$Result = array();
		foreach ( $Dates as $Date )
		{
			$Day_dates = $this->GetDays( C::_( 'START_DATE', $Date ), C::_( 'END_DATE', $Date ), $Date );
			$Result = array_merge( $Result, $Day_dates );
		}

		return $Result;

	}

	public function GetDays( $Start, $End, $Value )
	{
		$StartDate = new PDate( $Start );
		$ENDTMP = new PDate( $End );
		$EndDate = new PDate( $ENDTMP->toformat( '%Y-%m-%d 23:59:59' ) );
		$Days = array();
		while ( $StartDate->toUnix() < $EndDate->toUnix() )
		{
			$Days[$StartDate->toformat( '%Y-%m-%d' )] = $Value;
			$StartDate = new PDate( $StartDate->toUnix() + 86400 );
		}
		return $Days;

	}

	public function AutoGen( $BillID, $WORKER )
	{
		if ( empty( $BillID ) )
		{
			return false;
		}
		if ( empty( $WORKER ) )
		{
			return false;
		}
		$Table = new XHRSTable();
		return $Table->Generate( $BillID, $WORKER );

	}

}
