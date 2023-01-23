<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class overtimeworkerModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = AppHelper::getTable();
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
			$StartDate = new PDate( $this->Table->START_DATE );
			$EndDate = new PDate( $this->Table->END_DATE );
			$this->Table->START_TIME = $StartDate->toFormat( '%H:%M' );
			$this->Table->END_TIME = $EndDate->toFormat( '%H:%M' );
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
		$PDate = trim( C::_( 'START_DATE', $data ) );
		$DAY_COUNT = Helper::CleanNumber( C::_( 'DAY_COUNT', $data ) );
		if ( empty( $DAY_COUNT ) )
		{
			XError::setError( 'Overtime Hour Incorrect!' );
			return false;
		}
		if ( empty( $PDate ) )
		{
			XError::setError( 'Date Incorrect!' );
			return false;
		}
		$BaseDate = new PDate( C::_( 'START_DATE', $data ) );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );
		$data['TYPE'] = APP_OVERTIME;
		$data['START_DATE'] = $StartDate->toFormat();
		$data['END_DATE'] = $EndDate->toFormat();
		$data['DAY_COUNT'] = $DAY_COUNT;
		$data['INFO'] = trim( C::_( 'INFO', $data ) );
		if ( $EndDate->toUnix() > PDate::Get()->toUnix() )
		{
			XError::setError( 'overtime Date Incorrect!' );
			return false;
		}
		if ( empty( $data['WORKER'] ) )
		{
			XError::setError( 'Worker Incorrect!' );
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
				if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'Overtime Already Approved!' );
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
						XError::setError( 'Overtime Already Approved!' );
						Users::Redirect( $link );
					}
					$this->Table->STATUS = 1;
					$this->Table->APPROVE = Users::GetUserID();
					$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
					$this->Table->store();
//					$User = Users::getUser( $this->Table->WORKER );
//					$PHONE_WORK_NUMBER = C::_( 'MOBILE_PHONE_NUMBER', $User );
//					if ( !empty( $PHONE_WORK_NUMBER ) )
//					{
//						$AppStart = new PDate( $this->Table->START_DATE );
//						$AppEnd = new PDate( $this->Table->END_DATE );
//						include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';
//						$sms = new oneWaySMS( );
//						$private_time = Helper::getPrivateTime( $this->Table->WORKER );
//						$sms->SendPrivateTimeSMS( C::_( 'FIRSTNAME', $User ), C::_( 'LASTNAME', $User ), $AppStart->toFormat(), $AppEnd->toFormat(), $PHONE_WORK_NUMBER, $private_time );
//					}
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
