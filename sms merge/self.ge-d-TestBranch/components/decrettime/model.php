<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

require_once 'table.php';

class DecretTimeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new DecretTimeTable();
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}

		$StartDate = new PDate( C::_( 'START_DATE', $data ) );
		$EndDate = new PDate( C::_( 'END_DATE', $data ) );
		if ( $StartDate->toUnix() >= $EndDate->toUnix() )
		{
			XError::setError( 'Time Incorrect!' );
			return false;
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
		$IDx = $this->Table->insertid();
		return $IDx;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				$this->Table->STATUS = -2;
				$this->Table->DEL_USER = Users::GetUserID();
				$this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

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
				if ( C::_( 'ID', $this->Table ) )
				{
					if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
					{
						$link = '?option=' . $this->_option;
						XError::setMessage( 'Private Time Request is Already Approved!' );
						Users::Redirect( $link );
					}
					$this->Table->STATUS = 1;
					$this->Table->APPROVE = Users::GetUserID();
					$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
					$this->Table->store();
					$User = Users::getUser( $this->Table->WORKER );
					$PHONE_WORK_NUMBER = C::_( 'MOBILE_PHONE_NUMBER', $User );
					if ( !empty( $PHONE_WORK_NUMBER ) )
					{
						$AppStart = new PDate( $this->Table->START_DATE );
						$AppEnd = new PDate( $this->Table->END_DATE );
						include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';
						$sms = new oneWaySMS( );
						$private_time = Helper::getPrivateTime( $this->Table->WORKER );
						$sms->SendPrivateTimeSMS( C::_( 'FIRSTNAME', $User ), C::_( 'LASTNAME', $User ), $AppStart->toFormat(), $AppEnd->toFormat(), $PHONE_WORK_NUMBER, $private_time );
					}
				}
				else
				{
					return false;
				}
			}
			return true;
		}

	}

}
