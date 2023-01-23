<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_outminutesModel extends Model
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
		$Return->position = mb_strtolower( trim( Request::getState( $this->_space, 'position', '' ) ) );
		$Return->staff_schedule = trim( Request::getState( $this->_space, 'staff_schedule', '' ) );
		$Return->cat_id = (int) trim( Request::getState( $this->_space, 'cat_id', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '-1' ) );
		$where = array();
		if ( $Return->orgid > 0 )
		{
			$where[] = ' w.org =' . DB::Quote( $Return->orgid );
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
		if ( $Return->staff_schedule )
		{
			$where[] = ' w.staff_schedule in (select ch.id from lib_staff_schedules ch where ch.id in (' . $this->_search( $Return->staff_schedule, [ 'lib_title' ], 'lib_staff_schedules' ) . '))';
		}
		if ( $Return->position )
		{
			$where[] = ' sc.position in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . ')';
		}
		if ( $Return->cat_id > 0 )
		{
			$where[] = ' w.category_id= ' . DB::Quote( $Return->cat_id );
		}
		if ( !Xhelp::checkDate( $Return->start_date ) )
		{
			$Return->start_date = '';
		}
		if ( !Xhelp::checkDate( $Return->end_date ) )
		{
			$Return->end_date = '';
		}
		if ( !empty( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.event_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( !empty( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.event_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		}
		if ( count( $where ) > 0 )
		{
			$where[] = ' w.active > -6 ';
			$where[] = 'w.id is not null ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = ' select '
							. ' w.lastname || \' \' || w.firstname worker, '
							. ' w.id, '
							. ' sc.lib_title staff_schedule, '
							. ' w.org_name, '
							. ' w.position, '
							. ' w.private_number, '
							. ' un.lib_title org_place, '
							. ' to_char(t.event_date, \'hh24:mi:ss dd-mm-yyyy\') p_event_date, '
							. ' to_char(t.event_date, \'yyyy-mm-dd\') p_day,'
							. ' case when t.event_date - nvl(se.event_date, t.event_date) < 0 then 0 '
							. ' else (t.event_date - nvl(se.event_date, t.event_date)) * 1 * 24 * 60 end diff '
							. ' from hrs_workers_sch w '
							. ' left join lib_units un on un.id = w.org_place '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
							. ' left join ( '
							. ' select  '
							. ' e.staff_id, '
							. ' max(e.event_date) event_date '
							. ' from ('
							. ' select '
							. ' s.staff_id, '
							. ' case when s.event_date < to_date(to_char(s.event_date, \'yyyy-mm-dd\') || \' 07:10:00\', \'yyyy-mm-dd hh24:mi:ss\') then to_date(to_char(s.event_date - 1, \'yyyy-mm-dd\') || \' 23:59:59\', \'yyyy-mm-dd hh24:mi:ss\') '
							. ' else '
							. ' s.event_date end event_date '
							. ' from hrs_staff_events s '
							. ' where s.real_type_id = 2) '
							. ' e group by trunc(e.event_date), e.staff_id'
							. ' ) t on t.staff_id = w.id '
							. ' left join hrs_staff_events se on se.staff_id = t.staff_id and trunc(se.event_date) = trunc(t.event_date) AND se.real_type_id = 3500 '
							. $whereQ
							. ' order by w.lastname asc  '
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
				$TDate = C::_( 'P_DAY', $Item );
				$Return->items[$ID] = C::_( $ID, $Return->items, array() );
				$Return->items[$ID][$TDate] = $Item;
				$Return->Workers[$ID]['WORKER'] = C::_( 'WORKER', $Item );
				$Return->Workers[$ID]['PRIVATE_NUMBER'] = C::_( 'PRIVATE_NUMBER', $Item );
				$Return->Workers[$ID]['ORG_NAME'] = C::_( 'ORG_NAME', $Item );
				$Return->Workers[$ID]['STAFF_SCHEDULE'] = C::_( 'STAFF_SCHEDULE', $Item );
				$Return->Workers[$ID]['ORG_PLACE'] = C::_( 'ORG_PLACE', $Item );
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
