<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class hrtablesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->BILL_ID = (int) trim( Request::getState( $this->_space, 'BILL_ID', '-1' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->year = (int) trim( Request::getState( $this->_space, 'year', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->status = (int) trim( Request::getState( $this->_space, 'status', '-1' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );
		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '-1' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->loaded = 0;
		if ( $Return->BILL_ID > 0 )
		{
			$where = array();
			$where[] = '  t.BILL_ID =  ' . DB::Quote( $Return->BILL_ID );
			$where[] = ' w.active > -6 ';
			$where[] = ' w.id is not null ';
//			$where[] = ' w.calculus_type = 2 ';
			if ( $Return->status > -1 )
			{
				$where[] = ' t.status=  ' . DB::Quote( $Return->status );
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
				$where[] = ' sc.position in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . ')';
			}
			if ( $Return->firstname )
			{
				$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
			}
			if ( $Return->lastname )
			{
				$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
			}
			if ( $Return->org )
			{
				$where[] = ' w.org =' . $Return->org;
			}
			if ( $Return->category > 0 )
			{
				$where[] = ' w.category_id = ' . $Return->category;
			}

			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*)  '
							. ' from hrs_table t '
							. ' left join hrs_workers_sch w  on w.id = t.worker '
							. ' left join lib_staff_schedules sc  on sc.id = w.staff_schedule '
							. ' left join slf_persons wc  on wc.id = t.approve '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' t.id, '
							. ' w.id idx, '
							. ' w.private_number, '
							. ' w.org,'
							. ' w.position,'
							. ' w.tablenum,'
							. ' w.org_place,'
							. ' w.staff_schedule,'
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname, '
							. ' wc.firstname afirstname, '
							. ' wc.lastname alastname, '
							. ' to_char(t.approve_date, \'dd-mm-yyyy\') approve_date , '
							. ' nvl(t.STATUS, -1) status, '
							. ' t.sumhour, '
							. ' t.holiday, '
							. ' t.nholiday, '
							. ' t.bulletins, '
							. ' t.overtimehour, '
							. DB::Quote( $Return->BILL_ID ) . ' BILL_ID'
							. ' from hrs_table t '
							. ' left join hrs_workers_sch w  on w.id = t.worker '
							. ' left join lib_staff_schedules sc  on sc.id = w.staff_schedule '
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
			$Return->loaded = 1;
		}
		else
		{
			$Return->BILL_ID = '';
		}
		return $Return;

	}

}
