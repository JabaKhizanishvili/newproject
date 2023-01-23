<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class BulletinsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->year = (int) trim( Request::getState( $this->_space, 'year', '' ) );
		$Return->state = trim( Request::getState( $this->_space, 'state', -1 ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
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

			$where[] = ' t.type = ' . APP_BULLETINS;
			$where[] = ' t.worker in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		else
		{
			$where[] = 't.type = 5';
		}

//		$where[] = 'w.active > -1 ';
		$where[] = 'w.id is not null ';

		if ( $Return->firstname )
		{
			$where[] = ' w.person in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.person in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->private_number )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->org )
		{
			$where[] = ' w.org =  ' . $Return->org;
		}
		if ( $Return->org_place )
		{
			$where[] = ' t.worker in (select ww.orgpid from hrs_workers_sch ww where ww.org_place = ' . DB::Quote( $Return->org_place ) . ' and ww.active > 0)';
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
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}

		if ( $Return->end_date )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.end_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}

		$where[] = ' w.active <> 6 ';

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. ' left join rel_person_org w on w.id = t.worker'
						. ' left join slf_persons app on app.id = t.approve '
						. ' left join slf_persons sp on sp.id = w.person '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' t.org, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date, '
						. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
						. ' t.rec_user, '
						. ' t.additional_status, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.ucomment, '
						. ' t.approve, '
						. ' t.files, '
						. ' sp.private_number, '
						. ' sp.mobile_phone_number, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' sp.firstname wfirstname, '
						. ' sp.lastname wlastname,  '
						. ' t.worker as worker_id, '
						. ' app.firstname||\' \'|| app.lastname approver '
						. ' from hrs_applications t '
						. ' left join rel_person_org w on w.id = t.worker'
						. ' left join slf_persons app on app.id = t.approve '
						. ' left join slf_persons sp on sp.id = w.person '
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

}
