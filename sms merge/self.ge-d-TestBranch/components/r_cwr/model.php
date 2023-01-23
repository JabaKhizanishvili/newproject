<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_cwrModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->worker = trim( Request::getState( $this->_space, 'worker', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '0' ) );
		$order_by = ' order by w.firstname asc, t.event_date asc ';
		$where = array();
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =' . DB::Quote( $Return->org );
		}
		if ( $Return->worker )
		{
			$where[] = ' w.id = ' . DB::Quote( $Return->worker );
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
//			$where[] = ' w.active > -1';
			$where[] = 'w.id is not null ';
			$where[] = ' w.id in (select wc.worker from rel_worker_chief wc where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =  ' . Users::GetUserID() . ' )) ';
			$where[] = ' t.real_type_id in (1, 2,10, 11, 1500, 2000, 2500, 3000, 3500, 4000, 4500) ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = 'select '
							. ' /*+ use_nl(t a w) index(t HRS_STAFF_EVENTS_IDX6)*/ '
							. ' t.*, '
							. ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
							. ' w.id userid, '
							. ' a.lib_title event_name, '
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname,  '
							. ' w.org_name '
							. ' from hrs_workers w '
							. ' left join HRS_STAFF_EVENTS t on w.id = t.staff_id '
							. ' left join lib_actions a on a.type = t.real_type_id '
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
