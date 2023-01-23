<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class HolidayRegAsUserModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new HolidayUserTable( );
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
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}

		$data['DAY_COUNT'] = Helper::getDayCount( $data['WORKER'], $data['START_DATE'], $data['END_DATE'] );
		$data['ORG'] = XGraph::GetWorkerORGByID( $this->Table->WORKER );
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

				if ( C::_( 'ID', $this->Table ) )
				{
					$Worker = C::_( 'WORKER', $this->Table );
					$StartDate = C::_( 'START_DATE', $this->Table );
					$EndDate = C::_( 'END_DATE', $this->Table );
					$WorkerData = XGraph::GetOrgUser( $Worker );
					$SalaryID = C::_( 'ID', $WorkerData );
					$Type = (int) C::_( 'TYPE', $this->Table );

					$LimitsTable = new HolidayLimitsTable();
					$PDate = Pdate::Get( $StartDate );
					$LimitsTable->LoadUserLimits( $SalaryID, $PDate->toFormat(), $Type );
					if ( $LimitsTable->COUNT < $this->Table->DAY_COUNT )
					{
						XError::setError( 'HOLIDAY LIMIT  EXHAUSTED!' );
						return false;
					}
					$LimitsTable->COUNT = $LimitsTable->COUNT - $this->Table->DAY_COUNT;
					$LimitsTable->AWORKER = Users::GetUserID();

					if ( !$LimitsTable->store() )
					{
						return false;
					}
					$this->Table->STATUS = 1;
					$this->Table->APPROVE = Users::GetUserID();
					$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
					$this->Table->store();
					if ( $date->toUnix() > PDate::Get( $StartDate )->toUnix() )
					{
						$Params = array(
								':p_date_start' => $StartDate,
								':p_date_end' => $EndDate,
								':p_worker' => $Worker
						);
						DB::callProcedure( 'ReCalc', $Params );
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
