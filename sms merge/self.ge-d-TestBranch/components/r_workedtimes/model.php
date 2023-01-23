<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_workedtimesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->doors = Request::getState( $this->_space, 'doors', array() );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$Return->status = trim( Request::getState( $this->_space, 'status', '-1' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' sp.id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' sp.id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->org )
		{
			$where[] = ' wt.org =  ' . $Return->org;
		}
		if ( $Return->status == -1 )
		{
			$where[] = ' wt.status > -1  ';
		}
		else
		{
			$where[] = ' wt.status =  ' . $Return->status;
		}
		if ( $Return->org_place )
		{
			$where[] = ' lss.org_place in ( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id =  ' . DB::Quote( $Return->org_place )
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null '
							. ')';
		}
		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' wt.start_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' wt.end_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		}
		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) || !Helper::CheckTaskPermision( 'manager', $this->_option ) )
		{
			if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
			{
				$where[] = ' wt.office in (SELECT lss.OFFICE FROM	SLF_WORKER sw LEFT JOIN LIB_STAFF_SCHEDULES lss ON lss.ID = sw.STAFF_SCHEDULE WHERE	sw.PERSON =' . Users::GetUserID() . ') ';
				if ( $Return->status == -1 )
				{
					$where[] = ' wt.status = 0  ';
				}
			}
			else if ( !Helper::CheckTaskPermision( 'manager', $this->_option ) )
			{
				
			}
		}


//		$where[] = ' t.access_point_code is not null ';
//		$where[] = ' wt.status > -1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = ' select count(1) '
						. ' from hrs_worked_times wt '
						. ' left join slf_worker sw on sw.id = wt.worker '
						. ' left join slf_persons sp on 	sp.id = sw.person '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = ' select'
						. ' wt.id, '
						. ' sp.firstname, '
						. ' sp.lastname, '
						. ' sp.private_number, '
						. ' sw.tablenum, '
						. ' to_char(wt.start_date, \'yyyy-mm-dd\') graph_day, '
						. ' wt.org, '
						. ' wt.org_place, '
						. ' wt.status, '
						. ' wt.office, '
						. ' wt.worked_time, '
						. ' wt.night_worked_time, '
						. ' wt.day_worked_time,'
						. ' to_char(wt.start_date, \'hh24:mi:ss\') start_time, '
						. ' wt.start_pic, '
						. ' to_char(wt.end_date, \'hh24:mi:ss\') end_time, '
						. ' wt.end_pic,'
						. ' lp.lib_title position '
						. ' from hrs_worked_times wt '
						. ' left join slf_worker sw on sw.id = wt.worker '
						. ' left join slf_persons sp on 	sp.id = sw.person '
						. ' left join lib_staff_schedules lss on lss.id = sw.staff_schedule '
						. ' left join lib_positions lp on lp.id = lss.position '
						. $whereQ
						. $order_by
		;
		if ( $Full )
		{
			$Return->items = DB::LoadObjectList( $Query );
		}
		else
		{
			$Limit_query = 'select * from ( '
							. ' select a.*, rownum rn from (' .
							$Query
							. ') a) where rn > '
							. $Return->start
							. ' and rn <= ' . $Return->limit;
			$Return->items = DB::LoadObjectList( $Limit_query );
		}
		return $Return;

	}

}
