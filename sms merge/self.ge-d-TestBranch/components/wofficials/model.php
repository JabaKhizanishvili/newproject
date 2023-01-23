<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class WOfficialSModel extends Model
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
		$Return->year = (int) trim( Request::getState( $this->_space, 'year', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$where[] = 't.type = 6 ';
		$where[] = ' t.worker in (select wc.worker from rel_worker_chief wc where wc.chief in (select m.id from hrs_workers m where m.PARENT_ID =   ' . Users::GetUserID() . ' )) ';
		$where[] = 't.status >-1 ';
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
		if ( $Return->start_date )
		{
			$StartDate = new PDate( $Return->start_date );
			$where[] = ' t.start_date >= to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') ';
		}
		if ( $Return->end_date )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.end_date <= to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') ';
		}


		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' to_char(t.start_date, \'dd-mm-yyyy hh24:mi\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy hh24:mi\') end_date, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.info, '
						. ' t.ucomment, '
						. ' t.approve, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. 'app.firstname||\' \'|| app.lastname approver '
						. ' from hrs_applications t '
						. ' left join slf_persons w on w.id = t.worker '
						. ' left join slf_persons app on app.id = t.approve'
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
