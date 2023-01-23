<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class WorkerHolidaysModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->hperiod = trim( Request::getState( $this->_space, 'hperiod', -1 ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();

		if ( $Return->org > 0 )
		{
			$where[] = ' w.org =' . $Return->org;
		}
		if ( $Return->hperiod > -1 )
		{
			$List = str_split( $Return->hperiod, 2 );

			$Start_date = new PDate( '20' . C::_( '2', $List ) . '-' . C::_( '1', $List ) . '-' . C::_( '0', $List ) );
			$where[] = ' t.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') '
							. ' or   to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')  between t.start_date and t.end_date ';

			$EndDate = new PDate( '20' . C::_( '5', $List ) . '-' . C::_( '4', $List ) . '-' . C::_( '3', $List ) );
			$where[] = ' t.end_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')'
							. ' or   to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')  between t.start_date and t.end_date '
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

		$where[] = 't.status >-1 ';
		$where[] = ' t.type in (select t.id from lib_limit_app_types t) ';
//		$where[] = 'lt.active =1 ';
		$where[] = 't.worker in ( select id from hrs_workers m where m.parent_id = ' . Users::GetUserID() . ' ) ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_applications t '
						. 'LEFT JOIN lib_limit_app_types lt ON t.type = lt.id'
						. ' left join hrs_workers w on w.id = t.worker '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.id, '
						. ' t.type, '
						. ' t.org, '
						. ' lt.lib_title htitle,'
						. ' lt.wage_type htype,'
						. ' t.w_holiday_comment, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date, '
						. ' to_char(t.rec_date, \'dd-mm-yyyy hh24:mi\') rec_date, '
						. ' t.rec_user, '
						. ' t.day_count, '
						. ' t.status, '
						. ' t.approve, '
						. ' t.replacing_workers, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname,  '
						. ' app.firstname afirstname, '
						. ' app.lastname alastname  '
						. ' from hrs_applications t '
						. 'LEFT JOIN lib_limit_app_types lt ON t.type = lt.id'
						. ' left join hrs_workers w on w.id = t.worker '
//						. ' left join lib_user_holiday_limit l  on w.id = l.worker '
						. ' left join slf_persons app on app.id = t.approve'
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
