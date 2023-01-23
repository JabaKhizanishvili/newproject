<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class overtimesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$where[] = 't.type = ' . APP_OVERTIME;
		$where[] = 't.status >-1 ';
		$myids = XGraph::getWorkerORGIDs();
		$where[] = 't.worker in (' . implode( ',', $myids ) . ')';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker '
						. ' left join hrs_workers app on app.id = t.approve'
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
						. ' t.rec_user, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.info, '
						. ' ww.org_place_name, '
						. ' ww.staff_schedule, '
						. ' w.org, '
						. ' t.approve, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. ' app.firstname afirstname, '
						. ' app.lastname alastname '
						. ' from hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker '
						. ' left join hrs_workers_sch ww on ww.id = t.worker_id '
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
