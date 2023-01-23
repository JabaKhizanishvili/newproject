<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class o_reviewModel extends Model
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
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '0' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '0' ) );
//		$Return->c_resolution = (int) trim( Request::getState( $this->_space, 'c_resolution', '0' ) );
		$order_by = ' order by worker asc, t.event_date asc ';
		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
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
		$ORG = '';
		if ( $Return->orgid > 0 )
		{
			$ORG = ' and wc.org = ' . DB::Quote( $Return->orgid );
			$where[] = ' w.org = ' . DB::Quote( $Return->orgid );
		}
		if ( $Return->org_place > 0 )
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
//		if ( $Return->c_resolution )
//		{
//			$where[] = ' t.c_resolution = ' . DB::Quote( $Return->c_resolution );
//		}
//		else
//		{
//		}
//		$where[] = ' w.active > -1';
		$where[] = ' t.c_resolution = 0 ';
		$where[] = ' w.id is not null ';
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

			$where[] = ' w.id in (select wc.worker from rel_worker_chief wc where wc.chief_pid =  ' . Users::GetUserID() . ' and wc.clevel in (0, 1))' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		$where[] = ' t.u_comment is not null ';

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$Return->total = 0;
		$Query = 'select '
						. ' t.*, '
						. ' st.lib_title as staff_schedule, '
						. ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
						. ' to_char(t.u_comment_date, \'dd-mm-yyyy hh24:mi:ss\') u_comment_date, '
						. ' w.id userid, '
						. ' w.org_name, '
						. ' a.lib_title event_name, '
						. ' w.firstname || \' \' || w.lastname worker '
						. ' from HRS_STAFF_EVENTS t '
						. ' left join lib_actions a on a.type = t.real_type_id '
						. ' left join hrs_workers_sch w on w.id = t.staff_id '
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
		return $Return;

	}

	public function SaveData( $Data )
	{
		foreach ( $Data as $key => $value )
		{
			$Key = trim( $key );
			if ( empty( $Key ) )
			{
				continue;
			}
			$C_RESOLUTION = intval( C::_( 'C_RESOLUTION', $value ) );
			$C_COMMENT = trim( C::_( 'C_COMMENT', $value ) );
			if ( empty( $C_RESOLUTION ) )
			{
				continue;
			}
			if ( $C_RESOLUTION == 2 and empty( $C_COMMENT ) )
			{
				continue;
			}
			$C_COMMENT_DATE = PDate::Get()->toFormat();
			$Query = 'update '
							. ' hrs_staff_events e '
							. ' set '
							. ' e.c_resolution = ' . DB::Quote( $C_RESOLUTION ) . ','
							. ' e.c_comment = ' . DB::Quote( $C_COMMENT ) . ','
							. ' e.c_chief = ' . DB::Quote( Users::GetUserID() ) . ','
							. ' e.c_comment_date = to_date(' . DB::Quote( $C_COMMENT_DATE ) . ', ' . DB::Quote( 'yyyy-mm-dd hh24:mi:ss' ) . ' ) '
							. ' where id = ' . DB::Quote( $Key )
//							. ' and staff_id = ' . DB::Quote( Users::GetUserID() )
			;
			DB::Update( $Query );
		}

	}

}
