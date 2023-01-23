<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class WLiveModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
//		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
//		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->refresh = C::_( '0', Request::getState( $this->_space, 'refresh', array() ) );
		$Return->alerts = C::_( '0', Request::getState( $this->_space, 'alerts', array() ) );
		$Return->refreshtime = trim( Request::getState( $this->_space, 'refreshtime', '15' ) );
//		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '1' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', -1 ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->orgid > 0 )
		{
			$where[] = ' t.org =' . DB::Quote($Return->orgid);
		}
		if ( $Return->org_place )
		{
			$where[] = ' t.org_place = ' . DB::Quote($Return->org_place );
		}
//		if ( $Return->firstname )
//		{
//			$where[] = ' t.firstname like ' . DB::Quote( '%' . $Return->firstname . '%' );
//		}
//		if ( $Return->lastname )
//		{
//			$where[] = ' t.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
//		}
		$where[] = 't.active =1 ';
		$where[] = ' t.id in (select wc.worker from rel_worker_chief wc where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =   ' . Users::GetUserID() . ' )) ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$Return->total = 0; //DB::LoadResult( $countQuery );
		$Query = 'select t.id,
       t.firstname,
       t.lastname,
       t.org_name,
           to_char(ed.event_date, \'hh24:mi:ss dd-mm-yyyy\') event_date,
       ed.real_type_id,
       to_char(ede.event_date, \'hh24:mi:ss dd-mm-yyyy\') status_date,
       ede.real_type_id status_id,
			 ap.type
  from hrs_workers t
  left join (select *
               from hrs_staff_events ee
              where (ee.staff_id, ee.event_date) in
                    (select e.staff_id, max(e.event_date) event_date
                       from hrs_staff_events e
                      where e.event_date between sysdate -1 and sysdate
                        and e.real_type_id in (1500, 2000, 2500, 3000, 3500)
                      group by e.staff_id)) ed on ed.staff_id = t.id
left join (select *
               from hrs_staff_events ee
              where (ee.staff_id, ee.event_date) in
                    (select e.staff_id, max(e.event_date) event_date
                       from hrs_staff_events e
                      where e.event_date between sysdate - 1 and sysdate
                        and e.real_type_id  in (1,2, 10, 11)
                      group by e.staff_id)   and ee.real_type_id in (1, 2, 10, 11)) ede    on ede.staff_id = t.id
left join hrs_applications ap
    on ap.worker = t.id
   and sysdate between ap.start_date and ap.end_date
   and ap.status >0									
 '
						. $whereQ
						. $order_by
		;
		$Return->items = DB::LoadObjectList( $Query );
		return $Return;

	}

}
