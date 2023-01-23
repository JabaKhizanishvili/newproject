<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_workerModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->position = mb_strtolower( trim( Request::getState( $this->_space, 'position', '' ) ) );
		$Return->reason = (int) trim( Request::getState( $this->_space, 'reason', '' ) );
		$Return->cat_id = (int) trim( Request::getState( $this->_space, 'cat_id', '-1' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$Return->staff_schedule = trim( Request::getState( $this->_space, 'staff_schedule', '' ) );
		$Return->minutes_from = (int) trim( Request::getState( $this->_space, 'minutes_from', '0' ) );
		$Return->minutes_to = (int) trim( Request::getState( $this->_space, 'minutes_to', '0' ) );
		$Return->c_resolution = (int) trim( Request::getState( $this->_space, 'c_resolution', -1 ) );
		$order_by = ' order by trunc(t.event_date) asc, t.time_min desc  ';
		$where = array();

		if ( $Return->minutes_from > 0 )
		{
			$where[] = ' t.time_min >= ' . DB::Quote( $Return->minutes_from );
		}
		if ( $Return->minutes_to > 0 )
		{
			$where[] = ' t.time_min <= ' . DB::Quote( $Return->minutes_to );
		}
		//if ( $Return->c_resolution > -1 )
		//{
			$where[] = ' t.c_resolution in (0, 2) ';
		//}
		if ( $Return->orgid )
		{
			$where[] = ' w.org = ' . DB::Quote( $Return->orgid );
		}
		if ( $Return->org_place )
		{
            $where[] = ' w.org_place in( '
                . ' select '
                . ' t.id '
                . ' from lib_units t '
                . ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->org_place )
                . ' where '
                . ' t.active = 1 '
                . ' and u.id is not null )'
            ;
		}
		if ( $Return->staff_schedule )
		{
			$where[] = ' w.staff_schedule in (select ch.id from lib_staff_schedules ch where ch.id in (' . $this->_search( $Return->staff_schedule, [ 'lib_title' ], 'lib_staff_schedules' ) . '))';
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
		if ( $Return->position )
		{
			$where[] = ' sc.position in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . ')';
		}
		if ( $Return->cat_id > 0 )
		{
			$where[] = ' w.category_id= ' . DB::Quote( $Return->cat_id );
		}
		if ( $Return->reason > 0 )
		{
			switch ( $Return->reason )
			{
				case 1:
					$where[] = ' trim(t.time_comment) = \'?\' ';
					break;
				default:
					$where[] = ' trim(t.time_comment) <> \'?\' ';
					break;
			}
		}
		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.event_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.event_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		}
		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$DirectTreeUnion = ' or w.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
			}
			if ( $AdditionalTree )
			{
				$AdditionalTreeUnion = ' or w.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
			}

			$where[] = ' t.staff_id in (select wc.worker from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		if ( count( $where ) > 0 )
		{
			$where[] = ' w.active > -6 ';
			$where[] = 'w.id is not null ';
			$where[] = ' t.time_min >0 ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

            $countQuery = 'select count(*) from  HRS_STAFF_EVENTS t '
                . ' left join hrs_workers_sch w on w.id = t.staff_id '
                . ' left join lib_units un on un.id = w.org_place '
                . ' left join lib_actions a on a.type = t.prev_event_type '
                . ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
                . $whereQ
            ;
            $Return->total = DB::LoadResult( $countQuery );

//            . ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
//            . ' to_char(t.event_date, \'dd-mm-yyyy\') event_date_day, '
//            . ' to_char(t.prev_event_date, \'dd-mm-yyyy hh24:mi:ss\') prev_event_date, '


			$Query = ' select '
							. ' t.*, '
                            . ' to_char(t.event_date, \'dd-mm-yyyy\') event_date_day, '
                            . ' to_char(t.event_date, \'hh24:mi:ss\') event_dates, '
							. ' w.id userid, '
							. ' w.org_name, '
							. ' un.lib_title org_place, '
							. ' sc.lib_title staff_schedule, '
							. ' w.position, '
							. ' w.private_number, '
							. ' a.lib_title prev_event_name, '
							. ' w.firstname || \' \' || w.lastname worker '
//							. ' d.lib_title department, '
//							. ' s.lib_title section '
							. ' from HRS_STAFF_EVENTS t '
							. ' left join hrs_workers_sch w on w.id = t.staff_id '
							. ' left join lib_units un on un.id = w.org_place '
							. ' left join lib_actions a on a.type = t.prev_event_type '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '


//							. ' left join lib_sections s on s.id = w.section_id '
//							. ' left join lib_departments d on d.id = w.dept_id '
							. $whereQ
							. $order_by
			;

            $Limit_query = 'select * from ( '
                . ' select a.*, rownum rn from (' .
                $Query
                . ') a) where rn > '
                . $Return->start
                . ' and rn <= ' . $Return->limit;

            if ( $Full )
            {
                $Return->items = DB::LoadObjectList( $Query );
            }
            else
            {
                $Return->items = DB::LoadObjectList( $Limit_query );
            }
		}


		else{
			$Return->items = array();
		}

		return $Return;
	}
}
