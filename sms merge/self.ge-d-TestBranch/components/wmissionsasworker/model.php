<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

require_once 'table.php';

class wmissionsasworkerModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new MissionsTable();
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
        $date = new PDate();
		if ( !Xhelp::checkDate( C::_( 'START_DATE', $data ) ) )
		{
			return false;
		}
		if ( !Xhelp::checkDate( C::_( 'END_DATE', $data ) ) )
		{
			return false;
		}
        $Worker = C::_( 'WORKER', $data );
        $sDate = C::_( 'START_DATE', $data );
        $eDate = C::_( 'END_DATE', $data );

		$StartDate = new PDate( $sDate );
		$EndDate = new PDate( $eDate );
		$Approve = Helper::getConfig( 'mission_mission_approve' );
		if ( $id )
		{
			$this->Table->load( $id );
		}
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		$orgdata = XGraph::GetOrgUser( $this->Table->WORKER );

		$this->Table->ORG = C::_( 'ORG', $orgdata );
		if ( !$id )
		{
			$this->Table->REC_USER = Users::GetUserID();
		}
		$this->Table->START_DATE = $StartDate->toFormat();
		$this->Table->END_DATE = $EndDate->toFormat( '%Y-%m-%d 23:59:59' );
		if ( !$this->Table->check() )
		{
			return false;
		}
		$Now = new PDate();
		$this->Table->TYPE = APP_MISSION;
        if ( !$id ) {
            $this->Table->STATUS = 0;
            $this->Table->APPROVE = Users::GetUserID();
            $this->Table->APPROVE_DATE = PDate::Get()->toFormat();
        }

		if ( !$this->Table->store() )
		{
			return false;
		}

        if (!empty($id)) {
            $modelId = $id;
        } else {
            $modelId = $this->Table->insertid();
        }

        if ($Approve !== null && !(int)$Approve) {
            $this->Approve([$modelId]);
        }

		return $this->Table->insertid();

	}

	public function Approve($ids = [])
	{
		$idx = Request::getVar( 'nid', $ids );
		if ( is_array( $idx ) )
		{
			$date = new PDate();
			foreach ( $idx as $id )
			{
				$this->Table->load( $id );

				if ( Collection::get( 'ID', $this->Table ) )
				{
					if ( Collection::get( 'STATUS', $this->Table, 0 ) != 0 )
					{
						$link = '?option=' . $this->_option;
						XError::setMessage( 'application  already approved!' );
						Users::Redirect( $link );
					}
					$this->Table->STATUS = 1;
					$this->Table->APPROVE = Users::GetUserID();
					$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
					$this->Table->store();

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

					$WorkerData = XGraph::GetOrgUser( C::_( 'WORKER', $this->Table ) );
					$Subject = 'Your vacation request confirmed.';
					$TextLines = [];
					$TextLines[] = C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
					$TextLines[] = 'თქვენი მივლინების განაცხადი  დადასტურებულია';
					$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
					$TextLines[] = 'დასაწყისი: ' . C::_( 'START_DATE', $this->Table );
					$TextLines[] = 'დასასრული: ' . C::_( 'END_DATE', $this->Table );

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

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
				{
					if ( Collection::get( 'STATUS', $this->Table, 0 ) != 0 )
					{
						$link = '?option=' . $this->_option;
						XError::setError( 'application  already approved!' );
						Users::Redirect( $link );
					}
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
