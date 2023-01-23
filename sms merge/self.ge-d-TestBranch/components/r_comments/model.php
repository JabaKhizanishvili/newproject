<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_commentsModel extends Model
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
		$Return->category_id = (int) trim( Request::getState( $this->_space, 'category_id', '0' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '0' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
        $Return->showData = 0;

//		$order_by = ' order by w.firstname asc, t.event_date asc ';
        $dir = ($Return->dir == 1) ? 'desc' : 'asc';
        $order = $Return->order;
        $order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->orgid > 0 )
		{
			$where[] = ' w.org =' . DB::Quote( $Return->orgid );
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
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->category_id > 0 )
		{
			$where[] = ' w.category_id= ' . DB::Quote( $Return->category_id );
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

			$where[] = ' w.id in (select wc.worker from rel_worker_chief wc where wc.chief_pid =  ' . Users::GetUserID() . '  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		if ( count( $where ) > 0 )
		{
            $Return->showData = 1;
			$where[] = ' w.active > -6 ';
			$where[] = ' w.id is not null ';
			$where[] = ' t.C_RESOLUTION in (1, 2)';
			$where[] = ' t.real_type_id in (1, 2, 1500, 2000, 2500, 3000, 3500, 4000, 4500) ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
            $countQuery =' select count(*)  '
                        .' from HRS_STAFF_EVENTS t '
                        . ' left join lib_actions a on a.type = t.real_type_id '
                        . ' left join hrs_workers_sch w on w.id = t.staff_id '
                        . ' left join lib_units u on u.id = w.org_place '
                        . ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
                        . ' left join slf_persons cw on cw.id = t.c_chief '
                        . $whereQ;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' t.*, '
							. ' to_char(t.event_date, \'dd-mm-yyyy\') event_date, '
							. ' to_char(t.event_date, \'hh24:mi:ss\') event_time, '
							. ' w.id userid, '
							. ' w.org_name, '
							. ' sc.lib_title as staff_schedule, '
							. ' w.category_id, '
							. ' a.lib_title event_name, '
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname,  '
							. ' cw.firstname || \' \' || cw.lastname chief '
							. ' from HRS_STAFF_EVENTS t '
							. ' left join lib_actions a on a.type = t.real_type_id '
							. ' left join hrs_workers_sch w on w.id = t.staff_id '
							. ' left join lib_units u on u.id = w.org_place '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
							. ' left join slf_persons cw on cw.id = t.c_chief '
							. $whereQ
							. $order_by
			;
            $Return->items = array();
            $Data =  DB::LoadObjectList( $Query );
            foreach ($Data AS $key=> $value) {
                $C_RESOLUTION = C::_( 'C_RESOLUTION', $value );
                if ($C_RESOLUTION == 1) {
                     $Data[$key]->DICISION_OF_CHIEF =  Text::_('ADEQUATE');
                } else if ($C_RESOLUTION == 2) {
                     $Data[$key]->DICISION_OF_CHIEF = Text::_('INADEQUATE') . ' ( ' . C::_('C_COMMENT', $value, '') . ' )';
                }
            }

            $Return->items =$Data;
		}
		else
		{
			$Return->items = array();
		}
		return $Return;

	}

}
