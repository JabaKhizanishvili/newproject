<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class usertableModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new TableHRS_Table( );
		parent::__construct( $params );

	}

	public function getItem( $BillID )
	{
		$id = Users::GetUserID();
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->loads( array(
					'WORKER' => $id,
					'BILL_ID' => $BillID,
			) );
		}
		$this->Table->BILL_ID = $BillID;
		$this->Table->WORKER = $id;
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
				$this->Table->load( $id );
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
				if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
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

}
