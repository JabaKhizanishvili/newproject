<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class hrgraphallModel extends Model
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
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->staff_schedule = trim( Request::getState( $this->_space, 'staff_schedule', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$where = array();

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
		if ( $Return->firstname )
		{
			$where[] = ' w.person in (' . $this->_search( $Return->firstname, 'FIRSTNAME', 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.person in (' . $this->_search( $Return->lastname, 'LASTNAME', 'slf_persons' ) . ')';
		}
		if ( $Return->staff_schedule )
		{
			$where[] = ' w.staff_schedule in (select lss.id from lib_staff_schedules lss where lss.lib_title like ' . DB::Quote( '%' . $Return->staff_schedule . '%' ) . ')';
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org= ' . DB::Quote( $Return->org );
		}
		if ( $Return->unit > 0 )
		{
			$where[] = ' lss.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->unit )
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}

		$where[] = 'w.active >0 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$countQuery = 'select '
						. 'count(1) '
						. ' from slf_worker w  '
						. ' left join lib_staff_schedules lss on lss.id = w.staff_schedule '
						. $whereQ

		;
		$Return->total = DB::LoadResult( $countQuery );

		$Query = 'select '
						. ' p.firstname,'
						. ' p.lastname,'
						. ' w.*, '
						. ' u.lib_title org_place_title, '
						. ' lss.lib_title as sch_name '
						. ' from slf_worker w  '
						. ' left join slf_persons p on p.id = w.person '
						. ' left join lib_staff_schedules lss on lss.id = w.staff_schedule '
						. ' left join lib_units u on u.id = lss.org_place'
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->workers = DB::LoadObjectList( $Limit_query );

		$Return->items = Request::getVar( 'params', array() );
		return $Return;

	}

	public function SaveData( $data )
	{
		return parent::SaveData( $data );

	}

	public function getWorkers( $key = false, $WorkerItems = array() )
	{
		$Add = '';
		if ( count( $WorkerItems ) )
		{
			$Add = ' and wg.worker in (' . implode( ',', $WorkerItems ) . ' ) ';
		}

		$Query = 'select '
						. ' w.*, '
						. ' lss.lib_title as sch_name '
						. ' from rel_workers_groups wg'
						. ' left join hrs_workers_sch w on w.id=wg.worker '
						. ' left join lib_staff_schedules lss on lss.id = w.staff_schedule '
						. ' where w.active = 1 '
						. ' and w.graphtype = 0'
						. $Add
//						. ' and w.org = (select g.org from lib_workers_groups g where g.id = ' . DB::Quote( (int) $group_id ) . ') '
						. ' order by wg.ordering asc '
		;
		return (array) XRedis::getDBCache( 'lib_workers_groups', $Query, 'LoadObjectList', $key );

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

	public function getUserGroup( $Worker )
	{
		static $WorkerGroup = null;
		if ( is_null( $WorkerGroup ) )
		{
			$Query = 'Select wg.group_id from rel_workers_groups wg where wg.worker =' . $Worker;
			$WorkerGroup = DB::LoadResult( $Query );
		}
		return$WorkerGroup;

	}

}
