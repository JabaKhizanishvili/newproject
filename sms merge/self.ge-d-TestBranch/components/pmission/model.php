<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class pmissionModel extends Model
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
		$ID = C::_( 'ID', $data );
		if ( $ID )
		{
			$this->Table->load( $ID );
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
		$BaseDate = new PDate( C::_( 'START_DATE', $data ) );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d ' . $START_TIME . ':00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d ' . $END_TIME . ':00' ) );
		if ( $StartDate->toUnix() >= $EndDate->toUnix() )
		{
			XError::setError( 'Time Incorrect!' );
			return false;
		}
		$Comment = C::_( 'UCOMMENT', $data );
		if ( empty( $Comment ) )
		{
			XError::setError( 'Comment Incorrect!' );
			return false;
		}
		$Approve = Helper::getConfig( 'apps_mission_approve' );

		if ( $ID )
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
			$O = $this->Table->WORKER;
			$data['DAY_COUNT'] = Helper::getDayCount( $this->Table->WORKER, $this->Table->START_DATE, $this->Table->END_DATE );
			if ( !$this->Table->check() )
			{
				return false;
			}
			if ( !$this->Table->store() )
			{
				return false;
			}
			$IDx = $this->Table->insertid();
//			Mail::sendLeaveEditMail( $O, C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser(), $IDx );
//			Mail::senLeaveEditSMS( $O, C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser(), $IDx );
		}
		else
		{
			$Org = C::_( 'ORG', $data, array() );
			if ( empty( $Org ) && empty( $ID ) )
			{
				return false;
			}
			$OrgIDx = XGraph::getWorkerIDxByOrgs( $Org );
			if ( empty( $OrgIDx ) )
			{
				return false;
			}

			foreach ( $OrgIDx as $OrgD )
			{
				$O = C::_( 'ID', $OrgD );
				$data['REC_USER'] = Users::GetUserID();
				$data['WORKER'] = $O;
				$data['ORG'] = C::_( 'ORG', $OrgD );
				$data['TYPE'] = APP_OFFICIAL;
				$data['START_DATE'] = $StartDate->toFormat();
				$data['END_DATE'] = $EndDate->toFormat();
				$data['DAY_COUNT'] = 0;
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
				if ( !$this->Table->store() )
				{
					return false;
				}
				$IDx = $this->Table->insertid();
//				SEND MSG
				$WorkerData = XGraph::GetOrgUser( $O );
				$Subject = 'New leave request';
				$TextLines = [];
				$TextLines[] = 'ახალი სამსახურებრივი გასვლის განაცხადი';
				$TextLines[] = 'თანამშრომელი: ' . C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
				$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
				$TextLines[] = 'თარიღი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[0];
				$TextLines[] = 'დასაწყისი: ' . C::_( 'START_TIME', $data );
				$TextLines[] = 'დასასრული: ' . C::_( 'END_TIME', $data );
				$TextLines[] = Uri::getInstance()->base() . '?option=pmissionsasworkers';
				Mail::ToChiefs( $O, $Subject, $TextLines, 1, 1 );
			}
		}
		return $IDx;

		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}

		$data['WORKER'] = Users::GetUserID();
		$data['TYPE'] = APP_OFFICIAL;
		$data['START_DATE'] = $StartDate->toFormat();
		$data['END_DATE'] = $EndDate->toFormat();
		$data['DAY_COUNT'] = 0;
		$data['STATUS'] = 0;
//		$data['APPROVER'] = Users::GetUserID();
//		$data['APPROVE_DATE'] = PDate::SGet()->toFormat();
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

		if ( $id )
		{
			Mail::sendPTimeEditMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), Users::getUser(), $IDx );
		}
		else
		{
			Mail::sendPTimeMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), Users::getUser(), $IDx );
		}
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
