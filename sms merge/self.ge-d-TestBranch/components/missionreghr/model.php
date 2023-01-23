<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class MissionRegHRModel extends Model
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
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$id = C::_( 'ID', $data );
        $date = new PDate();
		$StartDate = new PDate( C::_( 'START_DATE', $data ) );
		$EndDate = new PDate( C::_( 'END_DATE', $data ) );
		if ( $id )
		{
			$this->Table->load( $id );
		}
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
		$this->Table->STATUS = 1;
		$this->Table->APPROVE = Users::GetUserID();
		$this->Table->APPROVE_DATE = $Now->toFormat();
        $this->Table->DAY_COUNT = (int)Helper::CalculateDayCount( $this->Table->START_DATE , $this->Table->END_DATE );

		if ( !$this->Table->store() )
		{
			return false;
		}

        if (Helper::CheckTaskPermision( 'admin', $this->_option )) {
            $StartDate = C::_( 'START_DATE', $this->Table );
            $Worker = C::_( 'WORKER', $this->Table );
            $EndDate = C::_( 'END_DATE', $this->Table );

            if ( $date->toUnix() > PDate::Get( $StartDate )->toUnix() ) {
                $WIDx = XGraph::GetOrgUserIDByOrgID( $Worker );
                foreach ( $WIDx as $WID ) {
                    $Params = array(
                        ':p_date_start' => PDate::Get( PDate::Get( $StartDate )->toUnix() + 10 )->toFormat(),
                        ':p_date_end' => PDate::Get( PDate::Get( $EndDate )->toUnix() + 10 )->toFormat(),
                        ':p_worker' => $WID
                    );
                    DB::callProcedure( 'ReCalc', $Params );
                }
            }
        }


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
					$WorkerData = Users::getUser( $Worker );
					$SalaryID = C::_( 'SALARY_EMPLOYEE_ID', $WorkerData );
					$Type = (int) C::_( 'TYPE', $this->Table );
					$Days = Helper::getDayCount( $Worker, $StartDate, $EndDate );
					if ( !count( $Days ) )
					{
						return false;
					}

					foreach ( $Days as $Day )
					{
						$Date = C::_( 'REAL_DATE', $Day );
						$status = Helper::RegisterMission( $SalaryID, $Type, $Date );

						if ( $status != 0 )
						{
							return false;
						}
					}
					if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
					{
						$link = '?option=' . $this->_option;
						XError::setMessage( 'Mission Deleted!' );
						Users::Redirect( $link );
					}
					$this->Table->STATUS = 1;
					$this->Table->APPROVE = Users::GetUserID();
					$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
					$this->Table->store();
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
//                if(C::_('STATUS', $this->Table, 0) != 0)
//                {
//				$link = '?option=' . $this->_option;
//				Error::setError( 'Mission Already Approved!' );
//				Users::Redirect( $link );
//                }
				$this->Table->STATUS = -2;
				$this->Table->DEL_USER = Users::GetUserID();
				$this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

	}

}
