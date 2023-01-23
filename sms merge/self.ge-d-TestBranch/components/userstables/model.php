<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class userstablesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->bill_id = (int) trim( Request::getState( $this->_space, 'bill_id', '-1' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->year = (int) trim( Request::getState( $this->_space, 'year', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->loaded = 0;
		if ( $Return->bill_id > 0 )
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

			$where = array();
			$where[] = ' w.active = 1 ';
			$where[] = ' sw.calculus_type = 2 ';
			$where[] = ' w.id IN (SELECT wc.worker FROM rel_worker_chief wc WHERE wc.chief_pid = ' . DB::Quote( Users::GetUserID() ) . ' AND wc.CLEVEL IN (0, 1))' . $DirectTreeUnion . $AdditionalTreeUnion;
			if ( $Return->firstname )
			{
				$where[] = ' sw.person in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
			}
			if ( $Return->lastname )
			{
				$where[] = ' sw.person in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
			}

			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*) '
							. ' from hrs_workers_sch w '
							. ' left join hrs_table t on w.id = t.worker and t.bill_id =  ' . DB::Quote( $Return->bill_id )
							. ' left join hrs_workers_sch wc on wc.id = t.approve '
							. ' left join slf_worker sw on sw.id = w.id '
							. ' left join slf_persons sp on sp.id = t.approve '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' w.id, '
							. ' w.org,'
							. ' w.private_number, '
							. ' w.position,'
							. ' w.firstname wfirstname, '
							. ' w.lastname wlastname,  '
							. ' sp.firstname afirstname, '
							. ' sp.lastname alastname,  '
							. ' to_char(t.approve_date, \'dd-mm-yyyy\') approve_date , '
							. ' nvl(t.STATUS, -1) status, '
							. ' t.sumhour, '
							. ' t.holiday, '
							. ' t.bulletins, '
							. ' t.overtimehour, '
							. DB::Quote( $Return->bill_id ) . ' bill_id'
							. ' from hrs_workers_sch w '
							. ' left join hrs_table t on w.id = t.worker and t.bill_id =  ' . DB::Quote( $Return->bill_id )
							. ' left join hrs_workers_sch wc on wc.id = t.approve '
							. ' left join slf_worker sw on sw.id = w.id '
							. ' left join slf_persons sp on sp.id = t.approve '
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
			$Return->loaded = 1;
		}
		else
		{
			$Return->bill_id = '';
		}
		return $Return;

	}

}
