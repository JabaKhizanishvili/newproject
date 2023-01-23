<?php
// Edited by Irakli Gzirishvili 21-10-2021.
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class R_user_in_outModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->author = trim( Request::getState( $this->_space, 'author', '' ) );
		$Return->worker = trim( Request::getState( $this->_space, 'worker', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->author )
		{
			$where[] = ' t.rec_user in( select id from slf_persons where id in (' . $this->_search( $Return->author, [ 'firstname', 'lastname' ], 'slf_persons' ) . '))';
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' ww.org= ' . DB::Quote( $Return->org );
		}

		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' ww.staff_schedule=  ' . DB::Quote( $Return->staffschedule );
		}

		if ( $Return->worker )
		{
			$where[] = ' ww.parent_id in (' . $this->_search( $Return->worker, [ 'firstname', 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->start_date )
		{
			$StartDate = new PDate( $Return->start_date );
			$where[] = ' t.log_date >= to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') ';
		}
		if ( $Return->end_date )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.log_date <= to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') ';
		}

//		$where[] = ' t.action in(1, 2) ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from hrs_transported_data_log t '
						. ' left join hrs_workers_sch hw on hw.id = t.rec_user '
						. ' left join hrs_workers_sch ww on ww.id = t.user_id '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*, '
						. ' ww.org, '
						. ' ww.staff_schedule, '
						. ' hw.firstname || \' \' || hw.lastname author, '
						. ' ww.firstname wfirstname, '
						. ' ww.lastname wlastname  '
						. ' from hrs_transported_data_log t '
						. ' left join hrs_workers_sch hw on hw.id = t.rec_user '
						. ' left join hrs_workers_sch ww on ww.id = t.user_id '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		if ( $Full )
		{
			$Return->items = DB::LoadObjectList( $Query );
		}
		else
		{
			$Return->items = DB::LoadObjectList( $Limit_query );
		}
		return $Return;

	}

	public function Delete( $data )
	{
		$all = [];
		foreach ( $data as $key => $id )
		{
			$all[] = DB::Quote( $id );
		}
		$Q = 'DELETE FROM SYSTEM_SESSIONS WHERE ID IN (' . implode( ',', $all ) . ')';
		return DB::Delete( $Q );

	}

	public function DeleteAll()
	{
		$Q = 'DELETE FROM SYSTEM_SESSIONS WHERE SESSION_ID != ' . DB::Quote( Session::getSessionID() );
		return DB::Delete( $Q );
		;

	}

}
