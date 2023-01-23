<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class DecretModel extends Model
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
		$WORKER = C::_( 'WORKER', $data );
		if ( empty( $WORKER ) )
		{
			return false;
		}

		$TYPE = (int) C::_( 'TYPE', $data );
		if ( empty( $TYPE ) )
		{
			return false;
		}

		$this->Table = AppHelper::getTable();
		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}

		$data['ORG'] = XGraph::GetWorkerORGByID( $WORKER );
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		$StartDate = new PDate( C::_( 'START_DATE', $data ) );
		$ReqEndData = new PDate( C::_( 'END_DATE', $data ) );
		$Now = new PDate();
		$EndDate = new PDate( $ReqEndData->toFormat( '%d-%m-%Y' ) . ' 23:59:59' );
		$this->Table->END_DATE = $EndDate->toFormat();
		$this->Table->START_DATE = $StartDate->toFormat();
		if ( !$this->checkFreeAppDate( $this->Table->WORKER, $data['ORG'], $this->Table->START_DATE, $id ) )
		{
			XError::setError( 'YOU ALREADY HAVE REGISTERED APPLICATION!' );
			return false;
		}
		if ( !$id )
		{
			$this->Table->REC_USER = Users::GetUserID();
		}
		$this->Table->APPROVE = Users::GetUserID();
		$this->Table->APPROVE_DATE = $Now->toFormat();
		$this->Table->STATUS = 1;
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
		$this->ClearTimeData( C::_( 'WORKER', $data ), C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ) );
		return $this->Table->insertid();

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				$this->Table->STATUS = -2;
				$this->Table->DEL_USER = Users::GetUserID();
				$this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

	}

	public function ClearTimeData( $Worker, $Start, $End )
	{
		$StartDate = new PDate( $Start );
		$EndDate = new PDate( $End );

		$Query = 'delete from HRS_GRAPH g '
						. ' WHERE g.WORKER = ' . $Worker
						. ' and REAL_DATE between to_date(\'' . $StartDate->toFormat( '%Y-%m-%d 00:00:00' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') '
						. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') '
		;
		return DB::Delete( $Query );

	}

	public function checkFreeAppDate( $worker, $org, $startDate = '', $id = 0 )
	{
		$query = 'select count(*) from hrs_applications t where '
						. ' t.worker = ' . (int) $worker
						. ' and t.org = ' . (int) $org
						. ($id > 0 ? ' and t.id != ' . (int) $id : '')
						. ' and t.status > -1 '
						. ' and t.end_date > to_date(\'' . $startDate . '\', \'yyyy-mm-dd hh24:mi:ss\')'
		;
		$result = DB::LoadList( $query );
		if ( C::_( '0', $result ) > 0 )
		{
			return false;
		}
		return true;

	}

}
