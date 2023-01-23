<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class o_workersModel extends Model
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
		$Return->reason = (int) trim( Request::getState( $this->_space, 'reason', '' ) );
		$Return->sphere = (int) trim( Request::getState( $this->_space, 'sphere', '' ) );
		$Return->department = (int) trim( Request::getState( $this->_space, 'department', '' ) );
		$Return->chapter = (int) trim( Request::getState( $this->_space, 'chapter', '' ) );
		$Return->cat_id = (int) trim( Request::getState( $this->_space, 'cat_id', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '0' ) );
		$order_by = ' order by w.firstname asc, t.event_date asc ';
		$where = array();

		if ( $Return->org > 0 )
		{
			$where[] = ' w.org= ' . DB::Quote( $Return->org );
		}
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->sphere )
		{
			$where[] = ' w.sphere= ' . $Return->sphere;
		}
		if ( $Return->department )
		{
			$where[] = ' w.department= ' . $Return->department;
		}
		if ( $Return->chapter )
		{
			$where[] = ' w.chapter= ' . $Return->chapter;
		}
		if ( $Return->cat_id > 0 )
		{
			$where[] = ' w.category_id= ' . $Return->cat_id;
		}
		if ( $Return->reason > 0 )
		{
			switch ( $Return->reason )
			{
				case 1:
					$where[] = ' trim(t.time_comment)  like \'%?%\' ';
					break;
//				case 2:
//					$where[] = ' (trim(t.time_comment) <> \'?\') ';
//					break;
				case 2:
					$where[] = ' (t.time_min = 0 and  trim(t.time_comment) not like \'%?%\' ) ';
					break;
				default:
					$where[] = ' trim(t.time_comment) is not null ';
					break;
			}
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
			$where[] = ' t.real_type_id in (1, 2, 1500, 2000, 2500, 3000, 3500) ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$Return->total = 0;
			$Query = 'select '
							. ' t.*, '
							. ' w.org_name, '
							. ' sc.lib_title staff_schedule, '
							. ' to_char(t.event_date, \'dd-mm-yyyy hh24:mi:ss\') event_date, '
							. ' w.id userid, '
							. ' a.lib_title event_name, '
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname '
							. ' from HRS_STAFF_EVENTS t '
							. ' left join lib_actions a on a.type = t.real_type_id '
							. ' left join hrs_workers_sch w on w.id = t.staff_id '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
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
		foreach ( $Data as $key => $value )
		{
			$Key = trim( $key );
			if ( empty( $Key ) )
			{
				continue;
			}
			$TimeMIn = (int) C::_( 'TIME_MIN', $value );
			$TIME_COMMENT = trim( C::_( 'TIME_COMMENT', $value ) );
			$Query = 'update '
							. ' hrs_staff_events e '
							. ' set '
							. ' e.time_min = ' . DB::Quote( $TimeMIn ) . ','
							. ' e.time_comment = ' . DB::Quote( $TIME_COMMENT )
							. ' where id = ' . DB::Quote( $Key )
			;
			DB::Update( $Query );
		}

	}

}
