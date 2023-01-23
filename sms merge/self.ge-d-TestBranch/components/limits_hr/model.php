<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class Limits_HRModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' t.firstname like ' . DB::Quote( '%' . $Return->firstname . '%' );
		}
		if ( $Return->lastname )
		{
			$where[] = ' t.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' t.org =  ' . $Return->org;
		}
		if ( $Return->unit )
		{
			$where[] = ' t.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->unit
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}
		$where[] = 't.active >-1 ';
		$where[] = 't.id > 0';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from hrs_workers_sch t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from hrs_workers_sch  t '
						. ' left join lib_roles r on r.id = t.user_role '
						. $whereQ
						. $order_by
		;
		$Date = new PDate();
		$Limit_query = 'select k.*,'
						. ' k.firstname || \' \' || k.lastname as worker, '
						. ' getChiefsByWorker(k.id) all_chiefs,  '
						. ' nvl((select ul.count from lib_user_holiday_limit ul where ul.worker = k.id and ul.htype = 0 and ul.year = ' . $Date->toFormat( '%Y' ) . '), getconfig(\'holiday_wage\')) ||\' ' . Text::_( 'day' ) . '\' hwage, '
						. ' nvl((select ul.count from lib_user_holiday_limit ul where ul.worker = k.id and ul.htype = 1 and ul.year = ' . $Date->toFormat( '%Y' ) . '), getconfig(\'holiday_wageless\'))  ||\' ' . Text::_( 'day' ) . '\' hwageless '
						. ' from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) k where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit
		;
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

}
