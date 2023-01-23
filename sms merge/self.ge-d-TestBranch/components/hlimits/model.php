<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class hlimitsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->hperiod = trim( Request::getState( $this->_space, 'hperiod', '-1' ) );
//		$Return->year = (int) trim( Request::getState( $this->_space, 'year', PDate::Get()->toFormat( '%Y' ) ) );
		$Return->htype = (int) trim( Request::getState( $this->_space, 'htype', -1 ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '0' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '1' ) );
		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->private_number )
		{
			$where[] = 'w.private_number = ' . DB::Quote( $Return->private_number );
		}
		if ( $Return->hperiod > -1 )
		{
			$List = str_split( $Return->hperiod, 2 );

			$Start_date = new PDate( '20' . C::_( '2', $List ) . '-' . C::_( '1', $List ) . '-' . C::_( '0', $List ) );
			$where[] = ' l.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') '
							. ' or   to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')  between l.start_date and l.end_date ';

			$EndDate = new PDate( '20' . C::_( '5', $List ) . '-' . C::_( '4', $List ) . '-' . C::_( '3', $List ) );
			$where[] = ' l.end_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')'
							. ' or   to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')  between l.start_date and l.end_date '
			;
		}
		if ( $Return->org > 0 )
		{
			$where[] = 'w.org=' . DB::Quote( $Return->org );
		}
		if ( $Return->htype > -1 )
		{
			$where[] = 'lt.id=' . DB::Quote( $Return->htype );
		}
		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$where[] = ' w.id in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1) ) ';
		}
		if ( $Return->active != -1 )
		{
			$where[] = ' sw.active = ' . $Return->active;
		}
		else
		{
			$where[] = ' sw.active = 1 ';
		}

		$where[] = 'lt.active =1 ';
		$where[] = 'w.id > 0 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(1) from hrs_workers_all w   '
						. ' left join (select ww.orgpid, case  when max(ww.active) = -2 then -2 else 1 end as active from slf_worker ww group by ww.orgpid) sw on sw.orgpid = w.id '
						. ' left join lib_user_holiday_limit l on w.id = l.worker '
						. ' LEFT JOIN lib_limit_app_types lt ON l.htype = lt.id '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );

		$Query = 'select '
						. ' l.*, '
						. ' lt.id type, '
						. ' w.private_number, '
						. ' w.firstname, '
						. ' w.lastname,'
						. ' w.org_name, '
						. ' lt.lib_title htitle, '
						. ' lt.wage_type '
						. ' from hrs_workers_all w '
						. ' left join (select ww.orgpid, case  when max(ww.active) = -2 then -2 else 1 end as active from slf_worker ww group by ww.orgpid) sw on sw.orgpid = w.id '
						. ' left join lib_user_holiday_limit l  on w.id = l.worker '
						. ' LEFT JOIN lib_limit_app_types lt ON l.htype = lt.id '
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
