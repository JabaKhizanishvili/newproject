<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_tabelModel extends Model
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
		$Return->reason = (int) trim( Request::getState( $this->_space, 'reason', '' ) );
		$Return->sphere = (int) trim( Request::getState( $this->_space, 'sphere', '' ) );
		$Return->department = (int) trim( Request::getState( $this->_space, 'department', '' ) );
		$Return->chapter = (int) trim( Request::getState( $this->_space, 'chapter', '' ) );
		$Return->cat_id = (int) trim( Request::getState( $this->_space, 'cat_id', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->position = mb_strtolower( trim( Request::getState( $this->_space, 'position', '' ) ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '0' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', 0 ) );
//		$order_by = ' order by trunc(t.event_date) asc, t.time_min desc  ';
		$where = array();
		$SubWhere = array();
		$OWhere = array();
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

			$where[] = ' w.orgpid in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		if ( $Return->position )
		{
			$where[] = ' sc.position in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . ')';
		}
		if ( $Return->unit > 0 )
		{
			$where[] = ' sc.org_place in ( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->unit )
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}

		if ( $Return->org > 0 )
		{
			$where[] = ' w.org = ' . DB::Quote( $Return->org );
		}
		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' w.staff_schedule = ' . DB::Quote( $Return->staffschedule );
		}
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->private_number != '' )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}

		if ( !empty( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' e.event_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
			$SubWhere[] = ' m.event_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
			$OWhere[] = ' o.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( !empty( $Return->end_date ) )
		{
			$End_Date = new PDate( $Return->end_date );
			$where[] = ' e.event_date < to_date(\'' . $End_Date->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
			$SubWhere[] = ' m.event_date < to_date(\'' . $End_Date->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
			$OWhere[] = ' o.start_date <= to_date(\'' . $End_Date->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		}
		if ( count( $where ) > 1 )
		{
			if ( $Return->cat_id > 0 )
			{
				$where[] = ' w.category_id= ' . $Return->cat_id;
			}
			$where[] = ' w.active > -6 ';
			$where[] = ' w.id is not null ';
			$where[] = ' e.time_id > 0 ';
			$where[] = ' e.real_type_id = 2000 ';
//			$where[] = ' trunc(e.event_date) not in (select to_date(to_char(sysdate, \'yyyy\') || \'-\' || t.lib_month || \'-\' || t.lib_day, \'yyyy-mm-dd\') holiday from lib_holidays t where t.active = 1) ';
			$OWhere[] = ' o.type in ( 11, 13) ';
			$OWhere[] = '  o.status = 1 ';
			$SubWhere[] = ' m.c_resolution <>1 ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$SubwhereQ = count( $SubWhere ) ? ' WHERE (' . implode( ') AND (', $SubWhere ) . ')' : '';
			$OWhereQ = count( $OWhere ) ? ' WHERE (' . implode( ') AND (', $OWhere ) . ')' : '';
			$Return->total = 0;
			$Query = 'select '
							. ' w.id, '
							. ' w.orgpid, '
							. ' w.firstname, '
							. ' w.lastname, '
							. ' w.org_name, '
							. ' w.p_code, '
							. ' un.unit_code, '
							. ' w.private_number, '
							. ' w.tablenum, '
							. ' w.position, '
							. ' un.lib_title org_place, '
							. ' w.salary, '
							. ' w.orgpid, '
							. ' e.staff_id, '
							. ' e.time_id, '
							. ' sc.lib_title staff_schedule, '
							. ' sc.schedule_code, '
							. ' gt.START_TIME, '
							. ' gt.END_TIME, '
							. ' gt.START_BREAK, '
							. ' gt.END_BREAK, '
							. ' gt.REST_TIME, '
							. ' gt.TIME_CODE, '
							. ' (gt.WORKING_TIME + gt.REST_TIME) as workinghours, '
							. '  to_char(e.event_date, \'yyyy-mm-dd\') event_date, '
//							. ' (nvl(gt.rest_minutes, 0) / 60) as rest_time, '
//							. ' (((ee.event_date - e.event_date) / (1 / 24)) - nvl(gt.rest_minutes, 0) / 60) as workinghours, '
//							. ' lt.lateness, '
							. ' GetLatenes(e.staff_id, e.event_date) lateness, '
							. ' oo.osum overtime '
							. ' from HRS_STAFF_EVENTS e '
							. ' left join lib_graph_times gt on gt. id = e.time_id '
							. ' left join hrs_workers_sch w on w.id = e.staff_id '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
							. ' left join lib_units un on un.id = w.org_place '
//							. ' left join HRS_STAFF_EVENTS ee on ee.staff_id = e.staff_id and trunc(ee.event_date) = trunc(e.event_date) and ee.real_type_id = 3500 '
//							. ' left join ('
//							. ' select '
//							. ' m.staff_id, '
//							. ' trunc(m.event_date) event_date, '
//							. ' sum(nvl(m.time_min, 0)) / 60 lateness '
//							. ' from hrs_staff_events m '
//							. $SubwhereQ
//							. ' group by m.staff_id, trunc(m.event_date) '
//							. ' ) lt on lt.staff_id = e.staff_id and lt.event_date = trunc(e.event_date) '
							. ' left join ('
							. ' select '
							. ' case when o.worker_id > 0 then o.worker_id else o.worker end worker_id, '
							. ' trunc(o.start_date) odate, '
							. ' sum(o.day_count) osum '
							. ' from HRS_APPLICATIONS o '
							. $OWhereQ
							. ' group by o.worker_id, o.worker, trunc(o.start_date)'
							. ' ) oo on oo.worker_id = e.staff_id and oo.odate = trunc(e.event_date) '
							. $whereQ
							. ' order by w.lastname, e.event_date asc'
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
				$ID = C::_( 'ID', $Item );
				$TDate = C::_( 'EVENT_DATE', $Item );
				$Return->items[$ID] = C::_( $ID, $Return->items, array() );
				$Return->items[$ID][$TDate] = $Item;
				$Return->Workers[$ID]['TIME_CODE'] = C::_( 'TIME_CODE', $Item );
				$Return->Workers[$ID]['FIRSTNAME'] = C::_( 'FIRSTNAME', $Item );
				$Return->Workers[$ID]['LASTNAME'] = C::_( 'LASTNAME', $Item );
				$Return->Workers[$ID]['PRIVATE_NUMBER'] = C::_( 'PRIVATE_NUMBER', $Item );
				$Return->Workers[$ID]['ORG_PLACE'] = C::_( 'ORG_PLACE', $Item );
				$Return->Workers[$ID]['TABLENUM'] = C::_( 'TABLENUM', $Item );
				$Return->Workers[$ID]['POSITION'] = C::_( 'POSITION', $Item );
				$Return->Workers[$ID]['SALARY'] = C::_( 'SALARY', $Item );
				$Return->Workers[$ID]['PRIVATE_NUMBER'] = C::_( 'PRIVATE_NUMBER', $Item );
				$Return->Workers[$ID]['ORG_NAME'] = C::_( 'ORG_NAME', $Item );
				$Return->Workers[$ID]['P_CODE'] = C::_( 'P_CODE', $Item );
				$Return->Workers[$ID]['ORGPID'] = C::_( 'ORGPID', $Item );
				$Return->Workers[$ID]['ORG_PLACE'] = C::_( 'ORG_PLACE', $Item );
				$Return->Workers[$ID]['UNIT_CODE'] = C::_( 'UNIT_CODE', $Item );
				$Return->Workers[$ID]['STAFF_SCHEDULE'] = C::_( 'STAFF_SCHEDULE', $Item );
				$Return->Workers[$ID]['SCHEDULE_CODE'] = C::_( 'SCHEDULE_CODE', $Item );
			}

			$Return->Outs = $this->getWorkersOuts( $Start_date, $End_Date );
			$Return->OverTimes = XHRSTable::GetOverTimes( $Start_date, $End_Date );
		}
		else
		{
			$Return->Workers = array();
			$Return->items = array();
		}
		return $Return;

	}

	public function getWorkersOuts( $startDate, $EndDate )
	{
		$Query = 'select '
						. ' t.worker, '
						. ' to_char(t.start_date, \'yyyy-mm-dd\') StartDate, '
						. ' to_char(t.end_date, \'yyyy-mm-dd\') EndDate, '
						. ' t.type'
						. ' from '
						. ' hrs_applications t '
						. ' where '
						. ' t.status > 0 '
						. ' and t.type in (' . HolidayLimitsTable::GetHolidayIDx() . ', 5, 7)'
						. ' and ('
						. ' ( t.start_date >= to_date(\'' . $startDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') '
						. ' and t.start_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\'))'
						. ' or  to_date(\'' . $startDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') between t.start_date and t.end_date '
						. ' or to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\') between  t.start_date and t.end_date) '
//						. ''
//						. ''
//						. ' and t.start_date >= to_date(\'' . $startDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')'
//						. ' and t.start_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')'
		;

		$data = DB::LoadObjectList( $Query );
		$Return = array();
		foreach ( $data as $d )
		{
			$Start = C::_( 'STARTDATE', $d );
			$End = C::_( 'ENDDATE', $d );
			$Type = C::_( 'TYPE', $d );
			$Days = Helper::GetDays( $Start, $End );
			foreach ( $Days as $Day )
			{
				$Return[$d->WORKER][$Day] = $Type;
			}
		}
		return $Return;

	}

}
