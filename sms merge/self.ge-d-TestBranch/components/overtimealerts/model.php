<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class overtimealertsModel extends Model
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
		$where[] = 't.status=1 ';
		$where[] = 't.worker = ' . Users::GetUserID();
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_overtime_alerts t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );

		$Query = 'select t.id, '
						. ' t.type, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date, '
						. ' to_char(t.create_date, \'dd-mm-yyyy hh24:mi:ss\') create_date, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.resolution, '
						. ' t.info, '
						. ' t.chief_approve, '
						. ' to_char(t.chief_approve_date, \'dd-mm-yyyy hh24:mi:ss\') chief_approve_date, '
						. ' t.user_approve, '
						. ' to_char(t.user_approve_date, \'dd-mm-yyyy hh24:mi:ss\') user_approve_date, '
						. ' w.firstname||\' \'|| w.lastname worker,  '
						. ' app.firstname||\' \'|| app.lastname chief_approver, '
						. ' appu.firstname||\' \'|| appu.lastname user_approver, '
						. ' appc.firstname||\' \'|| appc.lastname create_user '
						. ' from hrs_overtime_alerts t '
						. ' left join slf_persons w on w.id = t.worker '
						. ' left join slf_persons app on app.id = t.chief_approve'
						. ' left join slf_persons appu on appu.id = t.user_approve'
						. ' left join slf_persons appc on appc.id = t.create_user'
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
