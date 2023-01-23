<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_doorsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->doors = Request::getState( $this->_space, 'doors', array() );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );
		$Return->card_id = trim( Request::getState( $this->_space, 'card_id', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' w.id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( count( $Return->doors ) > 1 )
		{
			$where[] = ' t.access_point_code in ( \'' . implode( DB::Quote( ',' ), $Return->doors ) . '\')';
		}
		if ( $Return->card_id )
		{
			$where[] = ' t.card_id  =' . DB::Quote( $Return->card_id );
		}
		$add = '';
		if ( $Return->org )
		{
			$add = ' ww.org, ww.orgpid worker_id, ';
			$where[] = ' ww.org =  ' . $Return->org;
		}
		if ( $Return->org_place )
		{
			$where[] = ' ss.org_place in (select t.id from lib_units t left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->org_place ) . ' where t.active = 1and u.id is not null)';
		}
		if ( count( $where ) > 0 )
		{
			if ( Xhelp::checkDate( $Return->start_date ) )
			{
				$Start_date = new PDate( $Return->start_date );
				$where[] = ' t.rec_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
			}
			if ( Xhelp::checkDate( $Return->end_date ) )
			{
				$EndDate = new PDate( $Return->end_date );
				$where[] = ' t.rec_date < to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
			}
			$where[] = ' t.access_point_code is not null ';
			$where[] = ' ww.active = 1 ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

			$countQuery = ' select count(1) '
							. ' from hrs_transported_data t '
							. ' left join slf_persons w on w.id = t.parent_id '
							. ' left join lib_doors d on d.code = t.access_point_code  and d.active = 1 '
							. ' left join slf_worker ww on ww.person = w.id '
							. ' left join lib_staff_schedules ss on ss.id = ww.staff_schedule '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = ' select '
							. ' t.id, '
							. ' w.firstname, '
							. ' w.lastname, '
							. $add
							. ' pp.permit_id , '
							. ' t.rec_date, '
							. ' t.card_id, '
							. ' t.cardname, '
							. ' d.lib_title '
							. ' from hrs_transported_data t '
							. ' left join slf_persons w on w.id = t.parent_id '
							. ' left join lib_doors d on d.code = t.access_point_code  and d.active = 1 '
							. ' left join slf_worker ww on ww.id = t.user_id  '
							. ' left join lib_staff_schedules ss on ss.id = ww.staff_schedule '
							. ' left join rel_person_permit pp on t.card_id = pp.permit_id '
							. $whereQ
							. $order_by
			;
			if ( $Full )
			{
				$Return->items = DB::LoadObjectList( $Query );
			}
			else
			{

				$Limit_query = 'select * from ( '
								. ' select a.*, rownum rn from (' .
								$Query
								. ') a) where rn > '
								. $Return->start
								. ' and rn <= ' . $Return->limit;
				$Return->items = DB::LoadObjectList( $Limit_query );
			}
		}
		else
		{
			$Return->items = array();
		}
		return $Return;

	}

}
