<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class bulletinasusersModel extends Model
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
		$Return->state = trim( Request::getState( $this->_space, 'state', -1 ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
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
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =' . $Return->org;
		}
		$where[] = 't.type = ' . APP_BULLETINS;
		$where[] = ' t.worker in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel < 2 ) ';
		if ( $Return->firstname )
		{
			$where[] = ' w.firstname like ' . DB::Quote( '%' . $Return->firstname . '%' );
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
		}
		if ( $Return->year )
		{
			$where[] = ' to_char(t.start_date, \'yyyy\') = ' . DB::Quote( $Return->year )
							. ' or '
							. ' to_char(t.end_date, \'yyyy\') = ' . DB::Quote( $Return->year )
			;
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker'
						. ' left join slf_persons app on app.id = t.approve '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' t.org, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date, '
						. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
						. ' t.day_count, '
						. ' t.status, '
						. ' w.mobile_phone_number, '
						. ' t.approve, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. 'w.firstname||\' \'|| w.lastname worker,  '
						. 'app.firstname||\' \'|| app.lastname approver '
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
