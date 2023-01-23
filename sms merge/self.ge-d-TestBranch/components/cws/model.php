<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class CWSModel extends Model
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
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', -1 ) );
		$where = array();
		$whr = array();
		if ( $Return->firstname )
		{
			$where[] = ' p.id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' p.id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' p.id in ( select ss.person from slf_worker ss where ss.org =' . $Return->org . ')';
			$whr[] = ' sw.org = ' . DB::Quote( $Return->org );
		}
		if ( $Return->position )
		{
			$qq = ' ls.position in ( select pp.id from lib_positions  pp where pp.id in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . '))';
			$whr[] = $qq;
			$where[] = ' p.id in ( select ss.person from slf_worker ss where ss.staff_schedule in( select ls.id from lib_staff_schedules ls where ' . $qq . '))';
		}
		$whr[] = ' lu.active = 1 ';
		$where[] = 'p.active =1 ';
		$where[] = 'p.id > -1 ';
		$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
		$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
		$DirectTreeUnion = '';
		$AdditionalTreeUnion = '';
		if ( $DirectTree )
		{
			$DirectTreeUnion = ' or p.id in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
		}
		if ( $AdditionalTree )
		{
			$AdditionalTreeUnion = ' or p.id in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
		}

		$where[] = ' p.id in '
						. '(select '
						. ' wc.worker_pid '
						. ' from rel_worker_chief wc '
						. ' where wc.chief_pid = ' . Users::GetUserID() . ' and wc.clevel in(0, 1) ) '
						. $DirectTreeUnion
						. $AdditionalTreeUnion
		;
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_persons p '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' p.*'
						. ' from slf_persons p '
						. $whereQ
						. $order_by
		;
		$Date = new PDate();
		$Limit_query = 'select k.*,'
						. ' k.firstname wfirstname, '
						. ' k.lastname wlastname, '
						. ' getChiefsByWorker(k.id) all_chiefs '
//						. ' nvl((select ul.count from lib_user_holiday_limit ul where ul.worker = k.id and ul.htype = 0 and ul.year = ' . $Date->toFormat( '%Y' ) . '), getconfig(\'holiday_wage\')) ||\' ' . Text::_( 'day' ) . '\' hwage, '
//						. ' nvl((select ul.count from lib_user_holiday_limit ul where ul.worker = k.id and ul.htype = 1 and ul.year = ' . $Date->toFormat( '%Y' ) . '), getconfig(\'holiday_wageless\'))  ||\' ' . Text::_( 'day' ) . '\' hwageless '
						. ' from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) k where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit
		;
		$Return->items = DB::LoadObjectList( $Limit_query, 'ID' );
		$Keys = array_keys( $Return->items );
		$Collect = XHelp::getAssignedWorkers( implode( ', ', $Keys ), $whr );
		foreach ( $Keys as $Key )
		{
			$Return->items[$Key]->ORG = C::_( $Key, $Collect, array() );
		}

		return $Return;

	}

}
