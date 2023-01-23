<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class CWSHRModel extends Model
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
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->org = trim( Request::getState( $this->_space, 'org', '0' ) );
		$Return->org_place = trim( Request::getState( $this->_space, 'org_place', '0' ) );

		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->hperiod = trim( Request::getState( $this->_space, 'hperiod', -1 ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->hperiod > -1 )
		{
			$List = str_split( $Return->hperiod, 2 );

			$Start_date = new PDate( '20' . C::_( '2', $List ) . '-' . C::_( '1', $List ) . '-' . C::_( '0', $List ) );
			$where[] = ' w.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') '
							. ' or   to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')  between w.start_date and w.end_date ';

			$EndDate = new PDate( '20' . C::_( '5', $List ) . '-' . C::_( '4', $List ) . '-' . C::_( '3', $List ) );
			$where[] = ' w.end_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')'
							. ' or   to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')  between w.start_date and w.end_date '
			;
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
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->private_number != '' )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->start_date )
		{
			$StartDate = new PDate( $Return->start_date );
			$where[] = ' w.start_date >= to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') ';
		}
		if ( $Return->end_date )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' w.end_date <= to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') ';
		}

		if ( $Return->private_number )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}

//		$where[] = ' w.type = 2 ';
//		$where[] = ' w.status >-1 ';
//		$where[] = ' w.id is not null ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from hrs_workers w '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' w.*, '
						. ' w.id orgpid, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname '
						. ' from hrs_workers w '
						. $whereQ
						. $order_by
		;

//		$Date = new PDate();
		$Limit_query = 'select k.* '
//						. ' getChiefsByWorker(k.id) all_chiefs,  '
//						. ' nvl((select ul.count from lib_user_holiday_limit ul where ul.worker = k.id and ul.htype = 0 and ul.year = ' . $Date->toFormat( '%Y' ) . '), getconfig(\'holiday_wage\')) ||\' ' . Text::_( 'day' ) . '\' hwage, '
//						. ' nvl((select ul.count from lib_user_holiday_limit ul where ul.worker = k.id and ul.htype = 1 and ul.year = ' . $Date->toFormat( '%Y' ) . '), getconfig(\'holiday_wageless\'))  ||\' ' . Text::_( 'day' ) . '\' hwageless '
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
