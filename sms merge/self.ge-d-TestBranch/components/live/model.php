<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class LiveModel extends Model
{
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
		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '0' ) );
		$Return->refresh = C::_( '0', Request::getState( $this->_space, 'refresh', array() ) );
		$Return->alerts = C::_( '0', Request::getState( $this->_space, 'alerts', array() ) );
		$Return->refreshtime = trim( Request::getState( $this->_space, 'refreshtime', '15' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '0' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' t.staff_schedule=  ' . DB::Quote( $Return->staffschedule );
		}
		if ( $Return->orgid > 0 )
		{
			$where[] = ' t.org =' . DB::Quote( $Return->orgid );
		}
        if ( $Return->org_place > 0 )
        {
            $where[] = ' t.org_place in( '
                . ' select '
                . ' t.id '
                . ' from lib_units t '
                . ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->org_place
                . ' where '
                . ' t.active = 1 '
                . ' and u.id is not null )'
            ;
        }
		if ( $Return->category )
		{
			$where[] = ' t.category_id = ' . DB::Quote( $Return->category );
		}
		if ( $Return->firstname )
		{
			$where[] = ' t.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' t.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		$where[] = 't.active =1 ';
		$where[] = 't.livelist =1 ';
		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$DirectTreeUnion = ' or t.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
			}
			if ( $AdditionalTree )
			{
				$AdditionalTreeUnion = ' or t.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
			}

			$where[] = ' t.id in (select wc.worker from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel IN(0, 1) ) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$Return->total = 0; //DB::LoadResult( $countQuery );
		$Query = 'select t.id,'
						. ' t.parent_id, '
						. ' t.firstname, '
						. ' t.lastname, '
						. ' lss.lib_title staff_schedule, '
						. ' t.livelist, '
						. ' t.org_name, '
						. ' to_char( ed . event_date, \'hh24:mi:ss dd-mm-yyyy\') event_date, '
						. ' ed.real_type_id, '
						. ' nvl( to_char(ede.event_date, \'hh24:mi:ss dd-mm-yyyy\'), to_char(trunc(sysdate), \'hh24:mi:ss dd-mm-yyyy\')) status_date, '
						. ' nvl( ede.real_type_id, 2 ) status_id, '
//						. ' ap.type, '
						. ' app.type '
						. ' from hrs_workers_sch t '
						. ' left join lib_staff_schedules lss on lss.id = t.staff_schedule '
						. ' left join ( '
						. ' select '
						. ' e.staff_id, '
						. ' e.real_type_id, '
						. ' e.event_date, '
						. ' row_number() over (partition by e.staff_id order by e.event_date desc) rn '
						. ' from hrs_staff_events e '
						. ' WHERE '
						. ' e.event_date between sysdate - 20 and sysdate '
						. ' and e.real_type_id in (1500, 2000, 2500, 3000, 3500) '
						. ' ) ed '
						. ' on ed.staff_id = t.id '
						. ' and ed.rn = 1 '
						. ' left join ( '
						. ' select '
						. ' e.staff_id, '
						. ' e.real_type_id, '
						. ' e.event_date, '
						. ' row_number() over (partition by e.staff_id order by e.event_date desc) rn '
						. ' from hrs_staff_events e '
						. ' WHERE '
						. ' e.event_date between sysdate - 20 and sysdate '
						. ' and e.real_type_id in (1, 2, 10, 11) '
						. ' ) ede '
						. ' on ede.staff_id = t.id '
						. ' and ede.rn = 1 '
//						. ' left join hrs_applications ap '
//						. ' on ap.worker = t.id '
//						. ' and ( '
//						. ' -- (ede.event_date between ap.start_date and ap.end_date and trunc(ede.event_date) = trunc(sysdate)) or '
//						. ' --	 ed.event_date between ap.start_date and ap.end_date or '
//						. ' sysdate between ap.start_date and ap.end_date '
//						. ' ) '
//						. ' and ap.status >0 '
						. ' left join hrs_applications app '
						. ' on app.worker = t.orgpid '
						. ' and (sysdate between app.start_date and app.end_date) '
						. ' and app.status > 0 '
						. $whereQ
						. $order_by
		;

		$Return->items = DB::LoadObjectList( $Query );
		return $Return;

	}

}
