<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class hrtablessumModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->bill_id = (int) trim( Request::getState( $this->_space, 'bill_id', '-1' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		// $Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		// $Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		// $Return->year = (int) trim( Request::getState( $this->_space, 'year', '' ) );
		$Return->position = mb_strtolower( trim( Request::getState( $this->_space, 'position', '' ) ) );
		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '-1' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '0' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', 0 ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->loaded = 0;
		if ( $Return->bill_id > 0 && $Return->org > 0 )
		{
			$where = array();
//			$where[] = ' w.active = 1 ';
			$where[] = ' w.org = ' . $Return->org;
			$where[] = ' sw.calculus_type in(1, 2) ';
			$where[] = ' t.bill_id =  ' . $Return->bill_id;
			if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
			{
				$where[] = ' w.id IN (SELECT wc.worker FROM rel_worker_chief wc WHERE wc.chief_pid = ' . DB::Quote( Users::GetUserID() ) . ' AND clevel IN (0, 1))';
			}
			if ( $Return->firstname )
			{
				$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
			}
			if ( $Return->lastname )
			{
				$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
			}
			if ( $Return->position )
			{
				$where[] = ' sc.position in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . ')';
			}
			if ( $Return->unit > 0 )
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
			if ( $Return->category > 0 )
			{
				$where[] = ' w.category_id = ' . $Return->category;
			}

			$Return->UNITS = Units::getUnitList();

			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select '
							. ' count(*) '
							. ' from hrs_table t '
							. ' left join hrs_workers_sch w on w.id = t.worker '
							. ' left join slf_worker sw on sw.id = w.id '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
//							. ' w.id, '
							. ' w.private_number, '
							. ' w.tablenum,'
							. ' w.firstname workername, '
							. ' w.lastname wlastname, '
							. ' c.lastname || \' \' || c.firstname chiefname, '
							. ' w.position, '
							. ' nvl(t.STATUS, -1) status, '
							. ' t.BILL_ID, '
							. ' t.DAY01, '
							. ' t.DAY02, '
							. ' t.DAY03, '
							. ' t.DAY04, '
							. ' t.DAY05, '
							. ' t.DAY06, '
							. ' t.DAY07, '
							. ' t.DAY08, '
							. ' t.DAY09, '
							. ' t.DAY10, '
							. ' t.DAY11, '
							. ' t.DAY12, '
							. ' t.DAY13, '
							. ' t.DAY14, '
							. ' t.DAY15, '
							. ' t.DAYSUM01, '
							. ' t.DAY16, '
							. ' t.DAY17, '
							. ' t.DAY18, '
							. ' t.DAY19, '
							. ' t.DAY20, '
							. ' t.DAY21, '
							. ' t.DAY22, '
							. ' t.DAY23, '
							. ' t.DAY24, '
							. ' t.DAY25, '
							. ' t.DAY26, '
							. ' t.DAY27, '
							. ' t.DAY28, '
							. ' t.DAY29, '
							. ' t.DAY30, '
							. ' t.DAY31, '
							. ' t.DAYSUM02, '
							. ' t.DAYSUM, '
							. ' t.SUMHOUR, '
							. ' t.OVERTIMEHOUR, '
							. ' t.NIGHTHOUR, '
							. ' t.HOLIDAYHOUR, '
							. ' t.OTHERHOUR, '
							. ' t.BULLETINS, '
							. ' t.HOLIDAY, '
							. ' t.NHOLIDAY, '
							. ' t.OTHER, '
							. ' t.HOLIDAYS, '
							. ' t.APPROVE, '
							. ' to_char(t.APPROVE_DATE, \'hh24:mi:ss dd-mm-yyyy\') APPROVE_DATE '
							. ' from hrs_table t '
							. ' left join hrs_workers_sch w on w.id = t.worker and t.bill_id =  ' . DB::Quote( $Return->bill_id )
							. ' left join slf_worker sw on sw.id = w.id '
							. ' left join lib_staff_schedules sc on sc.id = sw.staff_schedule '
							. ' left join slf_persons c on c.id = t.approve and t.bill_id =  ' . DB::Quote( $Return->bill_id )
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
			$Return->ORGDATA = XGraph::GetOrgData( $Return->org );
			$Return->UNITS = Units::getUnitList( $Return->org );
		}
		else
		{
//			$Return->bill_id = '';
		}

		return $Return;

	}

}
