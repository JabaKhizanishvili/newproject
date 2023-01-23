<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class wmissionModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new MissionTable( );
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
		$id = Collection::get( 'ID', $data );
		$StartDate = new PDate( C::_( 'START_DATE', $data ) );
		$EndDate = new PDate( C::_( 'END_DATE', $data ) );
		$Approve = Helper::getConfig( 'mission_mission_approve' );
		if ( $id )
		{
			$this->Table->load( $id );
			if ( !$this->Table->bind( $data ) )
			{
				return false;
			}
			$this->Table->START_DATE = $StartDate->toFormat();
			$this->Table->END_DATE = $EndDate->toFormat( '%Y-%m-%d 23:59:59' );
			if ( !$this->Table->check() )
			{
				return false;
			}
			$Now = new PDate();
			$this->Table->TYPE = APP_MISSION;
			$this->Table->STATUS = 0;
			if ( !$this->Table->store() )
			{
				return false;
			}
			if ( $Now->toUnix() > PDate::Get( $StartDate )->toUnix() )
			{
				$Worker = Collection::get( 'WORKER', $this->Table );
				$Params = array(
						':p_date_start' => $StartDate->toFormat(),
						':p_date_end' => $EndDate->toFormat( '%Y-%m-%d 23:59:59' ),
						':p_worker' => $Worker
				);
				DB::callProcedure( 'ReCalc', $Params );
			}

			return $this->Table->insertid();
		}
		else
		{
			if ( empty( C::_( 'ORG', $data ) ) )
			{
				return false;
			}
			foreach ( C::_( 'ORG', $data ) as $org )
			{
				$this->Table->resetAll();
				if ( !$this->Table->bind( $data ) )
				{
					return false;
				}
				$O = XGraph::getWorkerIDsByOrg( $org );
				$this->Table->WORKER = $O;
				$this->Table->ORG = $org;
				$this->Table->REC_USER = Users::GetUserID();
				$this->Table->START_DATE = $StartDate->toFormat();
				$this->Table->END_DATE = $EndDate->toFormat( '%Y-%m-%d 23:59:59' );
				if ( !$this->Table->check() )
				{
					return false;
				}
				$Now = new PDate();
				$this->Table->TYPE = APP_MISSION;
				if ( $Approve )
				{
					$this->Table->STATUS = 0;
				}
				else
				{
					$this->Table->STATUS = 1;
				}

				if ( !$this->Table->store() )
				{
					return false;
				}
				if ( $Now->toUnix() > PDate::Get( $StartDate )->toUnix() )
				{
					$Worker = Collection::get( 'WORKER', $this->Table );
					$Params = array(
							':p_date_start' => $StartDate->toFormat(),
							':p_date_end' => $EndDate->toFormat( '%Y-%m-%d 23:59:59' ),
							':p_worker' => $Worker
					);
					DB::callProcedure( 'ReCalc', $Params );
				}
				$this->Table->insertid();

//				SEND MSG
				$IDx = $this->Table->insertid();
				$WorkerData = XGraph::GetOrgUser( $O );
				$Subject = 'New business trip request';
				$TextLines = [];
				$TextLines[] = 'ახალი მივლინების განაცხადი';
				$TextLines[] = 'თანამშრომელი: ' . C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
				$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
				$TextLines[] = 'მივლინების დასაწყისი: ' . explode( ' ', C::_( 'START_DATE', $this->Table ) )[0];
				$TextLines[] = 'მივლინების დასასრული: ' . explode( ' ', C::_( 'END_DATE', $this->Table ) )[0];
				$TextLines[] = Uri::getInstance()->base() . '?option=wmissionsasworkers';
				Mail::ToChiefs( $O, $Subject, $TextLines, 1, 1 );
			}
			return $this->Table;
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
					XError::setError( 'HOLIDAY ALREADY APPROVED!' );
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
