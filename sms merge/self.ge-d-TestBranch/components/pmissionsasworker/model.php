<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class pmissionsAsWorkerModel extends Model
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
		if ( !Xhelp::checkDate( C::_( 'START_DATE', $data ) ) )
		{
			return false;
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
		$Comment = C::_( 'UCOMMENT', $data );
		if ( empty( $Comment ) )
		{
			XError::setError( 'Comment Incorrect!' );
			return false;
		}
		$Approve = Helper::getConfig( 'apps_mission_approve' );
		$BaseDate = new PDate( C::_( 'START_DATE', $data ) );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d ' . $START_TIME . ':00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d ' . $END_TIME . ':00' ) );
		if ( $StartDate->toUnix() >= $EndDate->toUnix() )
		{
			XError::setError( 'Time Incorrect!' );
			return false;
		}
		$data['TYPE'] = APP_OFFICIAL;
		$data['START_DATE'] = $StartDate->toFormat();
		$data['END_DATE'] = $EndDate->toFormat();
		$data['DAY_COUNT'] = 0;
		$data['STATUS'] = 0;
//		$data['APPROVE'] = Users::GetUserID();
		if ( !$id )
		{
			$data['REC_USER'] = Users::GetUserID();
		}
//		$data['APPROVE_DATE'] = PDate::Get()->toFormat();

		if ( $Approve )
		{
			$data['STATUS'] = 0;
		}
		else
		{
			$data['STATUS'] = 1;
		}

		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$id && !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$WorkerData = XGraph::GetOrgUser( C::_( 'WORKER', $this->Table ) );
			$Subject = 'New leave request';
			$TextLines = [];
			$TextLines[] = 'ახალი გასვლის განაცხადი ';
			$TextLines[] = 'თანამშრომელი: ' . C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
			$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
			$TextLines[] = 'თარიღი: ' . explode( ' ', C::_( 'START_DATE', $data ) )[0];
			$TextLines[] = 'დასაწყისი: ' . C::_( 'START_TIME', $data );
			$TextLines[] = 'დასასრული: ' . C::_( 'END_TIME', $data );

			Mail::ToChiefs( C::_( 'WORKER', $data ), $Subject, $TextLines, 1, 1 );
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

					$WorkerData = XGraph::GetOrgUser( C::_( 'WORKER', $this->Table ) );
					$Subject = 'Your private time request confirmed.';
					$TextLines = [];
					$TextLines[] = C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
					$TextLines[] = 'თქვენი სამსახურებრივი გასვლის განაცხადი  დადასტურებულია';
					$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
					$TextLines[] = 'თარიღი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[0];
					$TextLines[] = 'დასაწყისი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[1];
					$TextLines[] = 'დასასრული: ' . explode( ' ', C::_( 'END_DATE', $this->Table ) )[1];

					$Phone_Number = C::_( 'MOBILE_PHONE_NUMBER', $WorkerData );
					Mail::sendAppSMS( $Phone_Number, $TextLines );
//					$Email = C::_( 'EMAIL', $WorkerData );
//					Mail::sendAppEMAIL( $Email, $Subject, $TextLines, $Worker );
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
