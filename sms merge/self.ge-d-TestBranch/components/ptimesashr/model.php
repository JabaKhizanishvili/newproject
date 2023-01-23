<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class PTimesAsHRModel extends Model
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

		if ( empty( $PDate ) || !AppHelper::IsValidPTDate( $PDate ) )
		{
			XError::setError( 'Date Incorrect!' );
			return false;
		}
		$START_TIME = trim( C::_( 'START_TIME', $data ) );
		$END_TIME = trim( C::_( 'END_TIME', $data ) );

		if ( !AppHelper::IsValidTime( $START_TIME ) )
		{
			XError::setError( 'Start Time Incorrect!' );
			return false;
		}
		if ( !AppHelper::IsValidTime( $END_TIME ) )
		{
			XError::setError( 'End Time Incorrect!' );
			return false;
		}
		$BaseDate = new PDate( C::_( 'START_DATE', $data ) );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d ' . $START_TIME . ':00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d ' . $END_TIME . ':00' ) );
		if ( $StartDate->toUnix() >= $EndDate->toUnix() )
		{
			XError::setError( 'Time Incorrect!' );
			return false;
		}

		$ptime_on_off = (int) Helper::getConfig( 'private_date' );
		$ptime_orgs = (array) explode( '|', Helper::getConfig( 'private_date_orgs' ) );
		$after_limit_register_limit = (int) Helper::getConfig( 'private_date_after_limit_registration_disable' );
		$check_org = (int) C::_( 'ORG', XGraph::GetOrgUser( C::_( 'WORKER', $data ) ) );
		if ( in_array( $check_org, $ptime_orgs ) && $ptime_on_off == 1 && $after_limit_register_limit == 1 )
		{
			$rame = Helper::getRemPrivateTime( C::_( 'WORKER', $data ), 1 );
			if ( $rame < 0 )
			{
				XError::setError( 'your limit has passed!' );
				return false;
			}
		}

		$data['TYPE'] = APP_PRIVATE_TIME;
		$data['START_DATE'] = $StartDate->toFormat();
		$data['END_DATE'] = $EndDate->toFormat();
		$data['DAY_COUNT'] = 0;
		$data['STATUS'] = 0;
		if ( !$id )
		{
			$data['REC_USER'] = Users::GetUserID();
		}
//		$data['APPROVER'] = Users::GetUserID();
//		$data['APPROVE_DATE'] = PDate::Get()->toFormat();

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
			$let = Helper::CheckTaskPermision( 'admin', $this->_option );
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				if ( !$let && C::_( 'STATUS', $this->Table, 0 ) != 0 )
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
