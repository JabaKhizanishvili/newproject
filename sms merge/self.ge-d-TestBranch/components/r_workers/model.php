<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_workersModel extends Model
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
		$Return->cat_id = (int) trim( Request::getState( $this->_space, 'cat_id', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$order_by = ' order by w.firstname asc, t.event_date asc, t.real_type_id asc ';
		$where = array();

		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->orgid > 0 )
		{
			$where[] = ' w.org =' . DB::Quote( $Return->orgid );
		}
		if ( $Return->org_place )
		{
			$where[] = ' w.org_place = ' . DB::Quote( $Return->org_place );
		}
		if ( $Return->cat_id > 0 )
		{
			$where[] = ' w.category_id= ' . DB::Quote( $Return->cat_id );
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
		if ( count( $where ) > 0 )
		{
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

				$where[] = ' w.id in (select wc.worker from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
			}

			$where[] = ' w.active > -6 ';
			$where[] = ' w.id is not null ';
			$where[] = ' t.real_type_id in (1, 2, 1500, 2000, 2500, 3000, 3500, 4000, 4500) ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = 'select '
							. ' t.*, '
							. ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
							. ' w.id userid, '
							. ' st.lib_title staff_schedule, '
							. ' o.lib_title org_name, '
							. ' a.lib_title event_name, '
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname  '
							. ' from HRS_STAFF_EVENTS t '
							. ' left join lib_actions a on a.type = t.real_type_id '
							. ' left join hrs_workers_sch w on w.id = t.staff_id '
							. ' left join lib_unitorgs o on w.org = o.id '
							. ' left join lib_staff_schedules st on st.id = w.staff_schedule '
							. $whereQ
							. $order_by
			;
			$Data = DB::LoadObjectList( $Query );
			$Return->items = array();
			foreach ( $Data as $Item )
			{
				$UserID = C::_( 'USERID', $Item );
				$Return->items[$UserID] = C::_( $UserID, $Return->items, array() );
				$Return->items[$UserID][] = $Item;
			}
		}
		else
		{
			$Return->items = array();
		}
		return $Return;

	}

}
