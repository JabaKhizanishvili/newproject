<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class BulletinModel extends Model
{
	protected $Table = null;
	protected $admin = null;
	public function __construct( $params )
	{
		$this->admin = Helper::CheckTaskPermision( 'admin', $this->_option );
		if ( $this->admin )
		{
			$this->Table = new BulletinTable();
		}
		else
		{
			$this->Table = AppHelper::getTable();
		}
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
		if ( $this->admin )
		{
			return $this->SaveData_1( $data );
		}
		else
		{
			return $this->SaveData_0( $data );
		}

	}

	public function Additional_Status( $data )
	{
		if ( empty( $data ) )
		{
			return false;
		}

		foreach ( $data as $id )
		{
			$this->Table->resetAll();
			$this->Table->load( $id );
			$this->Table->ADDITIONAL_STATUS = 1;
			if ( !$this->Table->check() )
			{
				return false;
			}
			$this->Table->store();
		}
		return true;

	}

	public function SaveData_0( $data )
	{
		$Now = New PDate( );
		$Date = trim( C::_( 'START_DATE', $data ) );
		if ( !Xhelp::checkDate( $Date ) )
		{
			return false;
		}

		$StartDate = New PDate( $Date );
		$this->Table->bind( $data );
		$this->Table->START_DATE = $StartDate->toFormat();
		$this->Table->ORG = XGraph::GetWorkerORGByID( $this->Table->WORKER );
		$this->Table->END_DATE = PDate::Get( $StartDate->toFormat() . ' +39 day' )->toFormat();
//		$this->Table->APPROVE_DATE = $Now->toFormat();
		$this->Table->STATUS = 1;
		$this->Table->TYPE = APP_BULLETINS;
		$this->Table->DAY_COUNT = 0;
//		$this->Table->APPROVE = Users::GetUserID();
		$this->Table->REC_USER = Users::GetUserID();
		if ( $this->Exists( $Date, $this->Table->ORG, $this->Table->WORKER ) )
		{
			XError::setError( 'Bulletin Allready Registered!' );
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
		return $this->Table->insertid();

	}

	public function SaveData_1( $data )
	{
		$id = C::_( 'ID', $data );
        $date = new PDate();
		$Status = C::_( 'STATUS', $data );
		$DAY_COUNT = (int) C::_( 'DAY_COUNT', $data );
		$Date = trim( C::_( 'START_DATE', $data ) );
		if ( !Xhelp::checkDate( $Date ) )
		{
			return false;
		}
		if ( $Status == 3 )
		{
			$StartDate = $Date;
			$EndDate = C::_( 'END_DATE', $data );
			if ( !Xhelp::checkDate( $EndDate ) )
			{
				return false;
			}
			$DAY_COUNT = Helper::CalculateDayCount( $StartDate, $EndDate );
			if ( empty( C::_( 'APPROVE', $data ) ) )
			{
				$data['APPROVE'] = Users::GetUserID();
				$data['APPROVE_DATE'] = PDate::Get()->toFormat();
			}
		}

		if ( empty( $data['FILES'] ) )
		{
			$data['FILES'] = ' ';
		}
		$e_status = 0;
		if ( $id )
		{
			$this->Table->load( $id );
			$e_status = C::_( 'STATUS', $this->Table );
		}
		else
		{
			$this->Table->REC_USER = Users::GetUserID();
		}
		$data['ORG'] = XGraph::GetWorkerORGByID( C::_( 'WORKER', $data ) );
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}

		$this->Table->DAY_COUNT = $DAY_COUNT;
//		$this->Table->APPROVE = Users::GetUserID();
//		$this->Table->APPROVE_DATE = PDate::Get( $Date )->toFormat();
		if ( !$this->Table->check() )
		{
			return false;
		}

		if ( !$this->Table->store() )
		{
			return false;
		}
		if ( $Status == 3 ) {
			$StartDate = PDate::Get( PDate::Get( C::_( 'START_DATE', $data ) )->toUnix() + 10 )->toFormat();
			$EndDate = PDate::Get( PDate::Get( C::_( 'END_DATE', $data ) )->toUnix() + 10 )->toFormat();

            if ( $date->toUnix() > PDate::Get( $StartDate )->toUnix() ) {
                $WIDx = XGraph::GetOrgUserIDByOrgID( $this->Table->WORKER );
                foreach ( $WIDx as $WID ) {
                    $Params = array(
                        ':p_date_start' => $StartDate,
                        ':p_date_end' => $EndDate,
                        ':p_worker' => $WID
                    );
                    DB::callProcedure( 'ReCalc', $Params );
                }
            }
		}


		if ( $id && $Status == 3 && $e_status != 3 )
		{
			$WorkerData = XGraph::GetOrgUser( C::_( 'WORKER', $data ) );
			$Subject = 'Your bulletin request confirmed.';
			$TextLines = [];
			$TextLines[] = C::_( 'FIRSTNAME', $WorkerData ) . ' ' . C::_( 'LASTNAME', $WorkerData );
			$TextLines[] = 'თქვენი ბიულეტენის განაცხადი  დადასტურებულია';
			$TextLines[] = 'ორგანიზაცია: ' . C::_( 'ORG_NAME', $WorkerData );
			$TextLines[] = 'გახსნის თარიღი: ' . C::_( 'START_DATE', $this->Table );
			$TextLines[] = 'დასრულების თარიღი: ' . C::_( 'END_DATE', $this->Table );

			$Phone_Number = C::_( 'MOBILE_PHONE_NUMBER', $WorkerData );
			Mail::sendAppSMS( $Phone_Number, $TextLines );
//					$Email = C::_( 'EMAIL', $WorkerData );
//					Mail::sendAppEMAIL( $Email, $Subject, $TextLines, $Worker );
		}

		return $this->Table->insertid();

	}

	public function Exists( $Date, $Org, $O )
	{
		$Query = 'select '
						. ' a.id '
						. ' from hrs_applications a '
						. ' where '
						. ' to_date(\'' . PDate::Get( $Date )->toFormat() . '\', \'yyyy-mm-dd hh24:mi:ss\') between a.start_date and a.end_date '
						. ' and a.org= ' . $Org
						. ' and a.status= 1'
						. ' and a.type= ' . APP_BULLETINS
						. ' and a.worker= ' . $O
		;
		return DB::LoadResult( $Query );

	}

	public function BContinue()
	{
		if ( $this->admin )
		{
			return $this->BContinue_1();
		}
		else
		{
			return $this->BContinue_0();
		}

	}

	public function BContinue_1()
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			foreach ( $idx as $id )
			{
				$this->Table->load( $id );
				if ( C::_( 'ID', $this->Table ) )
				{
					$Date = new PDate( 'now + 30 day' );
					$this->Table->STATUS = 1;
					$this->Table->END_DATE = $Date->toFormat( '%Y-%m-%d 23:58:59' );
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

	public function BContinue_0()
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			$Workers = Helper::getChiefsWorkersIDx();
			foreach ( $idx as $id )
			{
				$this->Table->load( $id );
				if ( !C::_( 'ID', $this->Table ) )
				{
					continue;
				}
				if ( C::_( 'STATUS', $this->Table ) != 2 )
				{
					continue;
				}
				$ID = C::_( 'WORKER', $this->Table );
				if ( !isset( $Workers[$ID] ) )
				{
					continue;
				}
				$Date = new PDate( 'now + 30 day' );
				$this->Table->STATUS = 1;
				$this->Table->END_DATE = $Date->toFormat( '%Y-%m-%d 23:58:59' );
				$this->Table->store();
			}
			return true;
		}
		return false;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				if ( !$this->admin && C::_( 'STATUS', $this->Table, 0 ) != 0 )
				{
					$link = '?option=' . $this->_option;
					Error::setError( 'Bulletins Already Approved!' );
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
