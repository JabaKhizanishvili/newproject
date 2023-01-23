<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class o_workerModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', -1 ) );
		$order_by = ' order by w.firstname asc, t.event_date asc ';
		$where = array();
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =' . $Return->org;
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
			$where[] = ' w.active > -1';
			$where[] = ' w.parent_id =' . (int) Users::GetUserID();
			$where[] = ' t.real_type_id in (1, 2, 1500, 2000, 2500, 3000, 3500) ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = 'select '
							. ' t.*, '
							. ' st.lib_title as staff_schedule, '
							. ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
							. ' w.id userid, '
							. ' w.org_name, '
							. ' a.lib_title event_name, '
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname '
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
		}
		else
		{
			$Return->items = array();
		}
		return $Return;

	}

	public function SaveData( $Data )
	{
		$Table = new TableHrs_staff_eventsInterface( 'hrs_staff_events', 'ID' );
		$Table->SetDateField( 'EVENT_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$Table->SetDateField( 'REC_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$Table->SetDateField( 'U_COMMENT_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$Table->SetDateField( 'PREV_EVENT_DATE', 'yyyy-mm-dd hh24:mi:ss' );

		foreach ( $Data as $key => $value )
		{

			$Key = trim( $key );
			if ( empty( $Key ) )
			{
				continue;
			}
			$U_COMMENT = trim( C::_( 'U_COMMENT', $value ) );
			if ( empty( $U_COMMENT ) )
			{
				continue;
			}

			$U_COMMENT_DATE = PDate::Get()->toFormat();
			$Table->resetAll();
			$Table->load( $Key );

			$stop = null;
			foreach ( XGraph::getWorkerSCH_IDx() as $x )
			{
				if ( $Table->STAFF_ID == $x->ID && $stop == null )
				{
					$Table->U_COMMENT_DATE = $U_COMMENT_DATE;
					$Table->U_COMMENT = $U_COMMENT;
					$Table->store();
					$stop = true;
				}
			}

//			if ( XGraph::getWorkerIDx()[0]->ID == $Table->STAFF_ID )
//			{
//				$Table->U_COMMENT_DATE = $U_COMMENT_DATE;
//				$Table->U_COMMENT = $U_COMMENT;
//				$Table->store();
//			}
//			$Query = 'update '
//							. ' hrs_staff_events '
//							. ' set '
//							. ' u_comment = ' . DB::Quote( $U_COMMENT ) . ','
//							. ' u_comment_date = to_date(' . DB::Quote( $U_COMMENT_DATE ) . ', ' . DB::Quote( 'yyyy-mm-dd hh24:mi:ss' ) . ' ) '
//							. ' where id = ' . DB::Quote( $Key )
//							. ' and staff_id = ' . DB::Quote( Users::GetUserID() )
//			;
//			DB::Update( $Query );
		}

		return $Table;

	}

}
