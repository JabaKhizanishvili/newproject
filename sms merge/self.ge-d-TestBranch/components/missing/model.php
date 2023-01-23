<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

//require_once 'table.php';

class MissingModel extends Model
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
		if ( $id )
		{
			$this->Table->load( $id );
		}
		else
		{
			$data['REC_USER'] = Users::GetUserID();
		}

		$workers = C::_( 'WORKER_ID', $data );
		if ( !is_array( $workers ) )
		{
			$workers = explode( ',', $workers );
		}

		if ( !count( $workers ) )
		{
			return false;
		}

		$BaseDate = PDate::Get( trim( C::_( 'START_DATE', $data ) ) );
		if ( empty( $BaseDate ) )
		{
			return false;
		}

		$NowDay = PDate::Get( '- 1day' )->toFormat( '%Y-%m-%d' );
		$Now = PDate::Get( $NowDay );

		if ( $Now->toUnix() < $BaseDate->toUnix() )
		{
			XError::setError( 'select previous date!' );
			return false;
		}

		$auto_confirm = (int) Helper::getConfig( 'missing_app_auto_confirm' );
		$StartDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 00:00:00' ) );
		$EndDate = new PDate( $BaseDate->toFormat( '%Y-%m-%d 23:59:59' ) );
		$data['START_DATE'] = $StartDate->toFormat();
		$data['END_DATE'] = $EndDate->toFormat();
		$data['INFO'] = trim( C::_( 'INFO', $data ) );
		$data['TYPE'] = APP_MISSING;

		$count = 0;
		$error = '';
		foreach ( $workers as $worker )
		{
			$worker_data = XGraph::getWorkerDataSch( $worker );
			$OrgPid = C::_( 'ORGPID', $worker_data );
			if ( empty( $OrgPid ) )
			{
				continue;
			}

			$data['WORKER'] = $OrgPid;
			$data['WORKER_ID'] = $worker;

			if ( $this->SameExists( $data ) )
			{
				continue;
			}

			if ( !$this->checkGraph( $worker, $StartDate->toFormat('%Y-%m-%d') ) )
			{
				$error = 'leave day count is zero!';
				continue;
			}

			if ( !$this->Table->bind( $data ) )
			{
				continue;
			}

			if ( !$this->Table->check() )
			{
				continue;
			}

			if ( $auto_confirm == 1 )
			{
				$this->Table->STATUS = 1;
			}

			if ( !$this->Table->store() )
			{
				continue;
			}

			$count++;
		}

		if ( $count > 0 )
		{
			return true;
		}

		if ( !empty( $error ) )
		{
			XError::setError( $error );
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
				if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
				{
					if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
					{
						$link = '?option=' . $this->_option;
						XError::setError( 'you cannot access task' );
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
						XError::setError( 'Missing Already Approved!' );
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

	public function SameExists( $data = [] )
	{
		if ( !count( $data ) )
		{
			return false;
		}

		$load = [
				'WORKER_ID' => C::_( 'WORKER_ID', $data ),
				'START_DATE' => C::_( 'START_DATE', $data ),
				'WORKER' => C::_( 'WORKER', $data ),
				'TYPE' => APP_MISSING,
				'STATUS' => 0
		];

		if ( !$this->Table->loads( $load ) )
		{
			return false;
		}

		XError::setError( 'record already exists!' );
		return true;

	}

	public function checkGraph( $worker = 0, $date = '' )
	{
		if ( empty( $worker ) || empty( $date ) )
		{
			return false;
		}

		$query = 'select '
						. ' count(*) '
						. ' from hrs_graph t '
						. ' where '
						. ' t.worker =  ' . (int) $worker
						. ' and t.real_date = to_date(' . DB::Quote( $date ) . ', \'yyyy-mm-dd\')';

		$result = DB::LoadResult( $query );
		if ( $result > 0 )
		{
			return true;
		}

		return false;

	}

}
