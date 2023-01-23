<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_latenessModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->staff_schedule = trim( Request::getState( $this->_space, 'staff_schedule', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->reason = (int) trim( Request::getState( $this->_space, 'reason', '' ) );
		$Return->sphere = (int) trim( Request::getState( $this->_space, 'sphere', '' ) );
		$Return->department = (int) trim( Request::getState( $this->_space, 'department', '' ) );
		$Return->chapter = (int) trim( Request::getState( $this->_space, 'chapter', '' ) );
		$Return->cat_id = (int) trim( Request::getState( $this->_space, 'cat_id', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', -1 ) );
//		$Return->c_resolution = (int) trim( Request::getState( $this->_space, 'c_resolution', -1 ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '0' ) );
		$where = array();
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =' . DB::Quote( $Return->org );
		}

//		if ( $Return->c_resolution > -1 )
//		{
//			$where[] = ' t.c_resolution =' . DB::Quote( $Return->c_resolution );
//		}

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

		if ( $Return->staff_schedule )
		{
			$where[] = ' w.staff_schedule in (select ch.id from lib_staff_schedules ch where ch.id in ( ' . $this->_search( $Return->staff_schedule, [ 'lib_title' ], 'lib_staff_schedules' ) . '))';
		}

		if ( $Return->org_place > 0 )
		{
			$where[] = ' w.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->org_place
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}


		if ( $Return->private_number )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}

		if ( $Return->cat_id > 0 )
		{
			$where[] = ' w.category_id= ' . $Return->cat_id;
		}
		if ( !Xhelp::checkDate( $Return->start_date ) )
		{
			$Return->start_date = '';
		}
		if ( !empty( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.event_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( !Xhelp::checkDate( $Return->end_date ) )
		{
			$Return->end_date = '';
		}
		if ( !empty( $Return->end_date ) )
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

			$where[] = ' t.staff_id in (select wc.worker from rel_worker_chief wc where wc.chief_pid = ' . Users::GetUserID() . '  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}

		if ( count( $where ) > 0 )
		{
			$where[] = ' w.active > -6 ';
			$where[] = ' w.id is not null ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = ' select '
							. ' x.*,'
							. ' to_char(x.event_date, \'hh24:mi:ss dd-mm-yyyy\') event_date, '
							. ' to_char(x.prev_event_date, \'hh24:mi:ss dd-mm-yyyy\') prev_event_date '
							. ' from ('
							. ' select '
							. ' w.lastname || \' \' || w.firstname worker, '
							. ' w.private_number, '
							. ' w.org_name, '
							. ' w.org_place_name, '
							. ' w.position, '
							. ' sc.lib_title staff_schedule, '
							. ' t.staff_id, '
							. ' t.real_type_id, '
							. ' t.event_date, '
							. ' t.time_comment, '
							. ' t.time_min, '
							. ' t.c_resolution, '
							. ' to_char(t.event_date, \'yyyy-mm-dd\') event_date_day, '
							. ' lag(t.real_type_id ) over(order by t.staff_id, t.event_date) prev_real_type_id, '
							. ' lag(t.event_date) over(order by t.staff_id, t.event_date) prev_event_date '
							. ' from hrs_staff_events t '
							. ' left join hrs_workers_sch w on w.id = t.staff_id '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
							. $whereQ
							. ' ) x '
							. ' where '
							. ' x.real_type_id in( 1, 2500) '
							. ' and x.prev_real_type_id = 2000 '
//							. ' and x.time_min > 0 '
							. ' order by  trunc(x.event_date) asc, x.worker asc '
			;
			$Data = DB::LoadObjectList( $Query );
			$Return->items = array();
			$Return->Workers = array();
			$StartDate = PDate::Get( $Return->start_date )->toFormat( '%Y-%m-%d 00:00:00' );
			$EndDate = PDate::Get( $Return->end_date )->toFormat( '%Y-%m-%d 23:59:59' );
			$DayQuery = 'select to_char(trunc(to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\')) + level - 1, \'yyyy-mm-dd\') pdate from dual connect by level <= trunc(to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd hh24:mi:ss\')) - trunc(to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') )+1';
			$Return->days = DB::LoadList( $DayQuery, 'PDATE' );
			foreach ( $Data as $Item )
			{
				$ID = C::_( 'STAFF_ID', $Item );
				$TDate = C::_( 'EVENT_DATE_DAY', $Item );
				$Return->items[$ID] = C::_( $ID, $Return->items, array() );
				$Return->items[$ID][$TDate] = $Item;
				$Return->Workers[$ID]['WORKER'] = C::_( 'WORKER', $Item );
				$Return->Workers[$ID]['PRIVATE_NUMBER'] = C::_( 'PRIVATE_NUMBER', $Item );
				$Return->Workers[$ID]['ORG_NAME'] = C::_( 'ORG_NAME', $Item );
				$Return->Workers[$ID]['ORG_PLACE'] = C::_( 'ORG_PLACE_NAME', $Item );
				$Return->Workers[$ID]['STAFF_SCHEDULE'] = C::_( 'STAFF_SCHEDULE', $Item );
				$Return->Workers[$ID]['POSITION'] = C::_( 'POSITION', $Item );
			}
		}
		else
		{
			$Return->Workers = array();
			$Return->items = array();
		}
		return $Return;

	}

}
