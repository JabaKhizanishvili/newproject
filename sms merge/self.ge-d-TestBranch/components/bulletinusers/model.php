<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class bulletinusersModel extends Model
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
		$Return->year = (int) trim( Request::getState( $this->_space, 'year', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->state = trim( Request::getState( $this->_space, 'state', -1 ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		switch ( $Return->state )
		{
			case 1:
				$where[] = 't.status=1 ';
				break;
			case 2:
				$where[] = 't.status=2 ';
				break;
			case 3:
				$where[] = 't.status=3 ';
				break;
			default:
				$where[] = 't.status >-1 ';
				break;
		}
		$where[] = 't.type = ' . APP_BULLETINS;
		$where[] = 't.worker in ( select id from hrs_workers m where m.parent_id = ' . Users::GetUserID() . ' ) ';
		if ( $Return->firstname )
		{
			$where[] = ' w.firstname like ' . DB::Quote( '%' . $Return->firstname . '%' );
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
		}
		if ( $Return->org )
		{
			$where[] = ' w.org = ' . DB::Quote( $Return->org );
		}
		if ( $Return->year )
		{
			$where[] = ' to_char(t.start_date, \'yyyy\') = ' . DB::Quote( $Return->year )
							. ' or '
							. ' to_char(t.end_date, \'yyyy\') = ' . DB::Quote( $Return->year )
			;
		}
		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.start_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.start_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker'
						. ' left join slf_persons app on app.id = t.approve '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.files, '
						. ' t.type, '
						. ' t.ucomment, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date, '
						. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
						. ' t.rec_user, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.org, '
						. ' w.mobile_phone_number, '
						. ' t.approve, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. ' app.firstname afirstname, '
						. ' app.lastname alastname '
						. ' from hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker'
						. ' left join slf_persons app on app.id = t.approve '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

}
