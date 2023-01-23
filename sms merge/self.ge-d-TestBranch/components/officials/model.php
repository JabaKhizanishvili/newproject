<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class OfficialsModel extends Model
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
		$Return->author = trim( Request::getState( $this->_space, 'author', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->year = (int) trim( Request::getState( $this->_space, 'year', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';

		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();

		$where[] = 't.type = ' . APP_OFFICIAL;
		$where[] = 't.status >-1 ';
		$where[] = 'w.id is not null ';

		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$DirectTreeUnion = ' or w.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
			}
			if ( $AdditionalTree )
			{
				$AdditionalTreeUnion = ' or w.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
			}

			$where[] = ' t.worker in (select wc.worker from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}

		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->author )
		{
			$where[] = ' t.rec_user in( select id from slf_persons where id in (' . $this->_search( $Return->author, [ 'firstname', 'lastname' ], 'slf_persons' ) . '))';
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org= ' . DB::Quote( $Return->org );
		}
		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' w.staff_schedule=  ' . DB::Quote( $Return->staffschedule );
		}

		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
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
			$StartDate = new PDate( $Return->start_date );
			$where[] = ' t.start_date >= to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') ';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.end_date <= to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') ';
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. ' left join hrs_workers_sch w on w.id = t.worker '
						. ' left join slf_persons app on app.id = t.approve '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' w.org, '
						. ' w.staff_schedule, '
						. ' to_char(t.start_date, \'dd-mm-yyyy hh24:mi\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy hh24:mi\') end_date, '
						. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
						. ' t.rec_user, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.info, '
						. ' t.ucomment, '
						. ' t.approve, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. ' app.firstname||\' \'|| app.lastname approver '
						. ' from hrs_applications t '
						. ' left join hrs_workers_sch w on w.id = t.worker '
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
