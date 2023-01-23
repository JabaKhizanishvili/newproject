<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class PTimesAsHRsModel extends Model
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
		$Return->org_place = trim( Request::getState( $this->_space, 'org_place', '0' ) );
		$Return->org = trim( Request::getState( $this->_space, 'org', '0' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
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

		if ( $Return->private_number )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}

		if ( $Return->org )
		{
			$where[] = ' w.org = ' . DB::Quote( $Return->org );
		}

		if ( $Return->org_place > 0 )
		{
			$where[] = ' w.id in (select ww.orgpid from hrs_workers_sch ww where '
							. '  ww.org = ' . DB::Quote( $Return->org )
							. ' and ww.org_place = ' . DB::Quote( $Return->org_place )
							. ' and ww.active = 1 )'
			;
		}
		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$DirectTreeUnion = ' or t.worker in (select rpo.id from rel_person_org rpo where rpo.person in (' . XStaffSchedule::GetChiefSubordinationsTree() . ')) ';
			}
			if ( $AdditionalTree )
			{
				$AdditionalTreeUnion = ' or t.worker in (select rpo.id from rel_person_org rpo where rpo.person in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ')) ';
			}

			$where[] = ' t.worker in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}

		$where[] = 't.type = ' . 2;
		$where[] = 't.status >-1 ';
		$where[] = 'w.active > -1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. ' left join hrs_workers w on w.id = t.worker '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') day_date, '
						. ' to_char(t.start_date, \'hh24:mi\') start_date, '
						. ' to_char(t.start_date, \'yyyy-mm-dd\') day, '
						. ' to_char(t.end_date, \'hh24:mi\') end_date, '
						. ' t.rec_user, '
						. ' t.day_count, '
						. ' t.status, '
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
