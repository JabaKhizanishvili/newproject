<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class PTimesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->org = trim( Request::getState( $this->_space, 'org', '0' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->org )
		{
			$where[] = ' w.org = ' . DB::Quote( $Return->org );
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

		$where[] = 't.type = ' . APP_PRIVATE_TIME;
		$where[] = 't.status >-1 ';
		$myids = XGraph::getWorkerORGIDs();
		$where[] = ' t.worker in (' . implode( ',', $myids ) . ')';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' to_char(t.start_date, \'dd-mm-yyyy hh24:mi\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy hh24:mi\') end_date, '
						. ' t.rec_user, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.ucomment, '
						. ' t.approve, '
						. ' w.private_number, '
						. ' w.org, '
						. ' w.id worker_id, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. ' app.firstname afirstname, '
						. ' app.lastname alastname  '
						. ' from hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker '
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
