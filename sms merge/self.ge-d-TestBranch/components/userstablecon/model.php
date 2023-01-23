<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class userstableconModel extends Model
{
	public function __construct( $params )
	{

		parent::__construct( $params );

	}

	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
//		$Return->bill_id = (int) trim( Request::getState( $this->_space, 'bill_id', '-1' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->tablenum = trim( Request::getState( $this->_space, 'tablenum', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->status = (int) trim( Request::getState( $this->_space, 'status', '1' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );

		$Return->BILL_ID = (int) trim( Request::getState( $this->_space, 'BILL_ID', null ) );
		if ( empty( $Return->BILL_ID ) )
		{
			$Return->BILL_ID = PDate::Get( 'now - 25 day' )->toFormat( '%y%m' );
		}
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->loaded = 0;
		$where = array();
//		$where[] = ' w.active = 1 ';
		$where[] = ' w.id is not null ';

		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$DirectTreeUnion = ' or t.worker in (select rpo.id from slf_worker rpo where rpo.person in (' . XStaffSchedule::GetChiefSubordinationsTree() . ')) ';
			}
			if ( $AdditionalTree )
			{
				$AdditionalTreeUnion = ' or t.worker in (select rpo.id from slf_worker rpo where rpo.person in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ')) ';
			}

			$where[] = ' w.id IN ( '
							. ' SELECT '
							. ' wc.worker '
							. ' FROM rel_worker_chief wc '
							. ' WHERE '
							. ' wc.chief_pid = ' . DB::Quote( Users::GetUserID() )
							. ' AND clevel IN (0, 1) '
							. ' union all '
							. ' select '
							. ' w.id '
							. ' from SLF_WORKER t '
							. ' left join lib_staff_schedules ss on ss.chief_schedule = t.staff_schedule '
							. ' left join slf_worker w on w.staff_schedule = ss.id '
							. ' where t.person =' . DB::Quote( Users::GetUserID() )
							. ') ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		if ( $Return->org )
		{
			$where[] = ' w.org =' . $Return->org;
		}
		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' w.staff_schedule=  ' . DB::Quote( $Return->staffschedule );
		}
		if ( $Return->unit )
		{
			$where[] = ' w.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->unit
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}
		if ( $Return->position )
		{
			$where[] = ' sc.position  in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . ')';
		}
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->private_number )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->tablenum )
		{
			$where[] = ' w.tablenum like ' . DB::Quote( '%' . $Return->tablenum . '%' );
		}
		if ( $Return->BILL_ID > 0 )
		{
			$where[] = ' t.bill_id = ' . DB::Quote( $Return->BILL_ID );
		}

		switch ( $Return->status )
		{
			case -1:
				$where[] = ' t.status > 0';

				break;
			case 2:
				$where[] = ' t.status = 2 ';

				break;
			case 3:
				$where[] = ' t.status = 3 ';
				break;
			case 1:
			default:
				$where[] = ' t.status = 1 ';
				break;
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  HRS_TABLE t '
						. ' left join hrs_workers_sch w on w.id = t.worker '
						. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
						. ' left join slf_persons wc  on wc.id = t.approve '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.id,'
						. ' w.id IDX,'
						. ' t.bill_id, '
						. ' w.private_number,'
						. ' w.org,'
						. ' w.org_place,'
						. ' w.tablenum,'
						. ' w.position,'
						. ' t.SUMHOUR, '
						. ' sc.lib_title staff_schedule, '
						. ' t.APPROVE, '
						. ' to_char(t.APPROVE_DATE , \'yyyy-mm-dd hh24:mi:ss\' ) APPROVE_DATE, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. ' wc.firstname afirstname, '
						. ' wc.lastname alastname,  '
						. ' t.holiday, '
						. ' t.bulletins, '
						. ' t.overtimehour, '
						. ' nvl(t.STATUS, -1) status '
						. ' from HRS_TABLE t '
						. ' left join hrs_workers_sch w on w.id = t.worker '
						. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
						. ' left join slf_persons wc  on wc.id = t.approve '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );

		return $Return;

	}

}
