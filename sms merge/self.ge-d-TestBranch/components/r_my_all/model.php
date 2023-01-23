<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_my_allModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', 'now -1 month +1 day' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', 'now +1 day' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', -1 ) );
		$order_by = ' order by worker asc, trunc(t.event_date) desc, t.event_date asc ';
		$where = array();
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
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =' . DB::Quote( $Return->org );
		}
		if ( count( $where ) > 0 )
		{
			$where[] = ' w.active > -6 ';
			$where[] = 'w.id is not null ';
			$where[] = ' w.parent_id = ' . Users::GetUserID();
			//	$where[] = ' t.time_comment is not null ';
			$where[] = ' t.real_type_id in (1, 2, 10,11, 1500, 2000, 2500, 3000, 3500) ';

			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = 'select '
							. ' t.*, '
							. ' sc.lib_title staff_schedule, '
							. ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
							. ' to_char(t.prev_event_date, \'dd-mm-yyyy hh24:mi:ss\') prev_event_date, '
							. ' w.id userid, '
							. ' a.lib_title event_name, '
							. ' w.firstname || \' \' || w.lastname worker, '
							. ' o.lib_title org_name, '
//							. ' d.lib_title department, '
							. ' a.lib_title prev_event_name '
//							. ' s.lib_title section '
							. ' from HRS_STAFF_EVENTS t '
							. ' left join lib_actions a on a.type = t.real_type_id '
							. ' left join hrs_workers_sch w on w.id = t.staff_id '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
							. ' left join lib_actions a on a.type = t.prev_event_type '
							. ' left join lib_unitorgs o on w.org = o.id '
//							. ' left join lib_sections s on s.id = w.section_id '
//							. ' left join lib_departments d on d.id = w.dept_id'
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
