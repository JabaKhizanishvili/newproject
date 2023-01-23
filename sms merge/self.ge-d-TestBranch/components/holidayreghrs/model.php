<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class HolidayRegHRsModel extends Model
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
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$Return->hperiod = trim( Request::getState( $this->_space, 'hperiod', -1 ) );
		$Return->htype = (int) trim( Request::getState( $this->_space, 'htype', -1 ) );
		$Now = new PDate();
		$StartYear = $Now->toFormat( '%Y' );
//		$Return->year = (int) trim( Request::getState( $this->_space, 'year', $StartYear ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$where[] = ' t.status >-1 ';
//		$where[] = ' lt.active =1 ';
//		$where[] = ' w.id is not null ';

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
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->org )
		{
			$where[] = ' w.org =  ' . $Return->org;
		}
		if ( $Return->htype > -1 )
		{
			$where[] = ' lt.id= ' . DB::Quote( $Return->htype );
		}
		if ( $Return->org_place )
		{
			$where[] = ' w.id in (select ww.orgpid from hrs_workers_sch ww where ww.org_place = ' . DB::Quote( $Return->org_place ) . ' and ww.active > 0)';
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
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') '
							. ' or   to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')  between t.start_date and t.end_date ';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.end_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')'
							. ' or   to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')  between t.start_date and t.end_date '
			;
		}

//		if ( $Return->year )
//		{
//			$where[] = ' to_char(t.start_date, \'yyyy\') = ' . DB::Quote( $Return->year )
//							. ' or '
//							. ' to_char(t.end_date, \'yyyy\') = ' . DB::Quote( $Return->year )
//			;
//		}

		if ( !Helper::CheckTaskPermision( 'admin', $this->_option ) )
		{
			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$DirectTreeUnion = ' or t.worker in ( select rpo.id from rel_person_org rpo where rpo.person in (' . XStaffSchedule::GetChiefSubordinationsTree() . ')) ';
			}
			if ( $AdditionalTree )
			{
				$AdditionalTreeUnion = ' or t.worker in ( select rpo.id from rel_person_org rpo where rpo.person in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ')) ';
			}

			$where[] = ' t.worker in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion;
		}
		$where[] = ' t.type in ( select t.id from lib_limit_app_types t ) ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = ' select count(*) from  hrs_applications t '
						. ' left join lib_limit_app_types lt on t.type = lt.id '
						. ' left join hrs_workers_all w on w.id = t.worker '
						. ' left join slf_persons app on app.id = t.approve '
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
						. ' t.replacing_workers, '
						. ' t.approve, '
						. ' w.private_number, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date, '
						. ' w.firstname wfirstname ,'
						. ' w.lastname wlastname, '
						. ' t.worker as worker_id, '
						. ' app.firstname afirstname, '
						. ' app.lastname alastname '
						. ' from hrs_applications t '
						. ' left join lib_limit_app_types lt on t.type = lt.id '
						. ' left join hrs_workers_all w on w.id = t.worker '
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
