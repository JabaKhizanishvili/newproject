<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class GraphsModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new GraphTable( );
		parent::__construct( $params );

	}

	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$WorkerGroups = Helper::getWorkerGroups( true );
		$group_id = (int) trim( Request::getState( $this->_space, 'group_id', C::_( '0.ID', $WorkerGroups, '' ) ) );
		$Return->group_id = (int) trim( Request::getState( $this->_space, 'group_id', $group_id ) );
		$Return->group_id_a = (int) trim( Request::getState( $this->_space, 'group_id_a', $group_id ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->archive_start_date = trim( Request::getState( $this->_space, 'archive_start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->archive_end_date = trim( Request::getState( $this->_space, 'archive_end_date', '' ) );

		if ( $Return->group_id == $Return->group_id_a )
		{
			$Return->workers_a = $Return->workers = $this->getWorkers();
		}
		else
		{
			$Return->workers = $this->getWorkers( $Return->group_id );
			$Return->workers_a = $this->getWorkers( $Return->group_id_a );
		}
		if ( empty( $Return->start_date ) )
		{
			$StartDate = new PDate();
			$Return->start_date = $StartDate->toFormat( '%d-%m-%Y' );
		}
		else
		{
			$StartDate = new PDate( $Return->start_date );
		}

		if ( empty( $Return->end_date ) )
		{
			$EndDate = new PDate( time() + 30 * 86400 );
			$Return->end_date = $EndDate->toFormat( '%d-%m-%Y' );
		}
		$Return->total = 0;
		$Return->items = Request::getVar( 'params', array() );
		return $Return;

	}

	public function SaveData( $data )
	{
		return parent::SaveData( $data );

	}

	public function getWorkers( $group_id = null, $key = false, $WorkerItems = array() )
	{
		$WorkerGroups = Helper::getWorkerGroups( true );
		if ( empty( $group_id ) )
		{
			$group_id = (int) trim( Request::getState( $this->_space, 'group_id', Collection::get( '0.ID', $WorkerGroups, '' ) ) );
		}
		$Add = '';
		if ( count( $WorkerItems ) )
		{
			$Add = ' and wg.worker in (' . implode( ',', $WorkerItems ) . ' ) ';
		}
		if ( $group_id )
		{
			$Query = 'select w.* from rel_workers_groups wg'
							. ' left join hrs_workers_sch w on w.id=wg.worker '
							. ' where wg.group_id = ' . (int) $group_id
							. ' and w.active = 1 '
							. $Add
							. ' order by wg.ordering asc '
			;
			return DB::LoadObjectList( $Query, $key );
		}
		return array();

	}

	public function CopyData( $Data, $Group )
	{
		$WorkerItems = Helper::CleanArray( Request::getVar( 'nid', array() ) );
		$StartDate = trim( C::_( 'START_DATE', $Data ) );
		$EndDate = trim( C::_( 'END_DATE', $Data ) );
		$CopyStartDate = trim( C::_( 'COPY_START_DATE', $Data ) );
		$CopyIteration = intval( C::_( 'COPY_ITERATION', $Data, 1 ) );

		if ( empty( $CopyIteration ) || $CopyIteration > 10 )
		{
			$CopyIteration = 1;
		}
		if ( empty( $StartDate ) )
		{
			return false;
		}
		if ( empty( $EndDate ) )
		{
			return false;
		}
		if ( empty( $CopyStartDate ) )
		{
			return false;
		}
		$Start = new PDate( $StartDate );
		$End = new PDate( $EndDate );
		$CopyStart = new PDate( $CopyStartDate );
		if ( $Start->toUnix() > $End->toUnix() )
		{
			return false;
		}
		if ( $CopyStart->toUnix() < $End->toUnix() )
		{
			return false;
		}

		$Workers = $this->getWorkers( $Group, 'ID', $WorkerItems );
		$WorkersKey = array_keys( $Workers );
		foreach ( $WorkersKey as $WorkerID )
		{
			$this->CopyWorkerData( $WorkerID, $Start, $End, clone $CopyStart, $CopyIteration );
		}
		return true;

	}

	public function CopyWorkerData( $WorkerID, $Start, $End, $CopyStart, $CopyIteration )
	{
		$Items = $this->getWorkerGraph( $Start, $End, $WorkerID );
		$Diff = $this->CalculateDayCount( $Start, $CopyStart ) - 1;
		$DayDiff = $this->CalculateDayCount( $Start, $End );

		/* @var $StartDate PDate */
		$Table = clone $this->Table;
		$KDiff = $Diff;
		for ( $K = 0; $K < $CopyIteration; $K++ )
		{
			$KDiff = $Diff + $K * $DayDiff;
			foreach ( $Items as $Item )
			{
				$Date = C::_( 'REAL_DATE', $Item );
				$CurDate = new PDate( $Date );
				$FutureDate = new PDate( $CurDate->toUnix() + ( $KDiff * 86400 ) );
				$Table->resetAll();
				$Table->bind( $Item );
				$Table->GT_DAY = (int) $FutureDate->toFormat( '%j' );
				$Table->GT_YEAR = $FutureDate->toFormat( '%Y' );
				$Table->REAL_DATE = $FutureDate->toFormat( '%Y-%m-%d' );
				$Table->SaveData();
			}
		}
		return true;

	}

	public static function getWorkerGraph( $startDate, $EndDate, $worker )
	{
		$Query = 'select '
						. ' t.worker, '
						. ' t.gt_day, '
						. ' t.gt_year, '
						. ' t.time_id, '
						. ' to_char(t.real_date, \'dd-mm-yyyy\') real_date '
						. ' from hrs_graph t '
						. ' where '
						. ' t.worker = ' . (int) $worker
						. ' and t.real_date between to_date(\'' . $startDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\')'
						. ' order by t.real_date asc '
		;
		$Return = DB::LoadObjectList( $Query );
		return $Return;

	}

	public static function CalculateDayCount( $date1Object, $date2Object )
	{
		$date1Unix = new PDate( $date1Object->toFormat( '%Y-%m-%d' ) );
		$date2Unix = new PDate( $date2Object->toFormat( '%Y-%m-%d' ) );
		return ceil( abs( $date1Unix->toUnix() - $date2Unix->toUnix() ) / 86400 ) + 1;

	}

}
