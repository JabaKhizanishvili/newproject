<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class Person_operationsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->privnumber = trim( Request::getState( $this->_space, 'privnumber', '' ) );
		$Return->creator = trim( Request::getState( $this->_space, 'creator', '' ) );
		$Return->operation_type = (int) Request::getState( $this->_space, 'operation_type', '-1' );
		$Return->org = (int) Request::getState( $this->_space, 'org', '0' );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );
		$Return->status = (int) trim( Request::getState( $this->_space, 'status', '-1' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.change_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.change_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		}
		if ( $Return->firstname )
		{
			$where[] = ' sp.id in (' . $this->_search( $Return->firstname, 'firstname', 'slf_persons' ) . ')';
		}
		if ( $Return->privnumber )
		{
			$where[] = 'sp.private_number like ' . DB::Quote( '%' . $Return->privnumber . '%' );
		}
		if ( $Return->lastname )
		{
			$where[] = ' sp.id in (' . $this->_search( $Return->lastname, 'lastname', 'slf_persons' ) . ')';
		}
		if ( $Return->creator )
		{
			$where[] = ' sps.id in (' . $this->_search( $Return->creator, [ 'firstname', 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->operation_type > -1 )
		{
			$where[] = ' t.change_type= ' . DB::Quote( $Return->operation_type );
		}
		else
		{
			$where[] = ' t.change_type != 6';
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' t.org= ' . DB::Quote( $Return->org );
		}
		if ( $Return->unit > 0 )
		{
			$where[] = ' lss.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->unit )
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}
		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' t.staff_schedule=  ' . DB::Quote( $Return->staffschedule );
		}
		if ( $Return->status != -1 )
		{
			$where[] = ' t.status=  ' . DB::Quote( $Return->status );
		}
		else
		{
			$where[] = ' t.status !=  -6';
		}

		$where[] = ' sp.id is not null ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  slf_changes t '
						. ' left join slf_persons sp on sp.id = t.person '
						. ' left join slf_persons sps on sps.id = t.creator_person '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*,'
						. ' lss.org_place, '
						. ' lss.position, '
						. ' sp.private_number, '
						. ' sp.firstname wfirstname, '
						. ' sp.lastname wlastname,  '
						. ' sps.firstname afirstname, '
						. ' sps.lastname alastname  '
						. ' from slf_changes t '
						. ' left join slf_persons sp on sp.id = t.person '
						. ' left join slf_persons sps on sps.id = t.creator_person '
						. ' left join lib_staff_schedules lss on lss.id = t.staff_schedule '
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
