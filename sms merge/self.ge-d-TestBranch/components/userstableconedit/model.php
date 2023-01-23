<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';
require_once PATH_BASE . DS . 'libraries' . DS . 'Table.php';

class userstableconeditModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new TableUCHRS_Table( );
		parent::__construct( $params );

	}

	public function getItem( $BillID )
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->loads( array(
					'ID' => C::_( '0', $id ),
					'BILL_ID' => $BillID,
			) );
//			$this->Table->loads( $id[0] );
		}

		$this->Table->BILL_ID = $BillID;
//		$this->Table->WORKER = C::_( '0', $id );		
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$id = C::_( 'ID', $data );
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
		if ( is_array( $idx ) )
		{
			$date = new PDate();
			foreach ( $idx as $id )
			{
				$this->Table->resetAll();
				$this->Table->load( $id );
				$Approve = C::_( 'ID', XGraph::GetApprove( $this->Table->ORG ) );
				$this->Table->STATUS = 2;
				$this->Table->APPROVE = Users::GetUserID();
				$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
				$Table = new XHRSTable();
				$Table->SendAlert( $this->Table->WORKER, $this->Table );
			}
			return true;
		}

	}

	public function GetAPPS( $UserID, $StartDate, $EndDate )
	{
		$Query = ' select '
						. ' to_char(t.start_date, \'yyyy-mm-dd\')  start_date, '
						. ' to_char(t.end_date, \'yyyy-mm-dd\')  end_date, '
						. ' a.lib_title title, '
						. ' a.type '
						. ' from HRS_APPLICATIONS t '
						. ' left join v_lib_applications_types a on a.type = t.type '
						. ' where '
						. ' t.worker = (SELECT ORGPID FROM SLF_WORKER WHERE ID = ' . DB::Quote( $UserID ) . ')'
						. ' and t.status > 0 '
						. ' and ( '
						. ' t.start_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') '
						. ' or t.end_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') '
						. ' or to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') between t.start_date and t.end_date '
						. ' or to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') between t.start_date and t.end_date '
						. ' ) '
						. ' and t.type in (' . HolidayLimitsTable::GetHolidayIDx() . ', 3, 4, 5, 17) '
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

}
