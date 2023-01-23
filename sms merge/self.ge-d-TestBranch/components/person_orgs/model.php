<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class Person_orgsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList( $Full = false, $tmpl = '' )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->staff_schedule = trim( Request::getState( $this->_space, 'staff_schedule', '' ) );
		$Return->chief_schedule = trim( Request::getState( $this->_space, 'chief_schedule', '' ) );
		$Return->chief_firstname = trim( Request::getState( $this->_space, 'chief_firstname', '' ) );
		$Return->chief_lastname = trim( Request::getState( $this->_space, 'chief_lastname', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->tablenum = trim( Request::getState( $this->_space, 'tablenum', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->userrole = (int) trim( Request::getState( $this->_space, 'userrole', '' ) );
		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '' ) );
		$Return->permit_id = trim( Request::getState( $this->_space, 'permit_id', '' ) );
		$Return->graphtype = (int) trim( Request::getState( $this->_space, 'graphtype', '-1' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '1' ) );
		$Return->calculus_type = (int) trim( Request::getState( $this->_space, 'calculus_type', '-1' ) );
		$Return->regime = (int) trim( Request::getState( $this->_space, 'regime', '-1' ) );
		$Return->staff_schedule = (int) trim( Request::getState( $this->_space, 'staff_schedule', 0 ) );
		$Return->hist_date = trim( Request::getState( $this->_space, 'hist_date ', '' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', 0 ) );
		$attributes = array_filter( Request::getState( $this->_space, 'attributes', [] ), function ( $i )
		{
			return !empty( $i );
		} );
		$Return->attributes = implode( ',', $attributes );
//		$order_by = ' order by sc.change_date desc';
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' pr.id in (' . $this->_search( $Return->firstname, 'FIRSTNAME', 'slf_persons' ) . ')';
		}
		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' t.staff_schedule=  ' . DB::Quote( $Return->staffschedule );
		}
		if ( $Return->lastname )
		{
			$where[] = ' pr.id in (' . $this->_search( $Return->lastname, 'LASTNAME', 'slf_persons' ) . ')';
		}
		if ( $Return->staff_schedule )
		{
			$where[] = ' t.staff_schedule in (select lss.id from lib_staff_schedules lss where lss.lib_title like ' . DB::Quote( '%' . $Return->staff_schedule . '%' ) . ')';
		}
		if ( $Return->chief_schedule )
		{
			$where[] = ' ls.lib_title like ' . DB::Quote( '%' . $Return->chief_schedule . '%' );
		}

		if ( !empty( $Return->attributes ) )
		{
			$where[] = ' t.id in ( 
                    SELECT 
                         ra.item_id
                    FROM rel_attributes ra
                    left join lib_attributes la on la.id = ra.attribute_id
                    where 
                        ra.item_type = 2
                    and
                        la.active = 1
                    and 
                        la.id in ( ' . $Return->attributes . ')
                ) ';
		}
		if ( $Return->chief_firstname )
		{
			$where[] = ' t.id in 
            (
                select
                    rwc.worker 
                from 
                    rel_worker_chief rwc 
                where rwc.chief_pid in (' . $this->_search( $Return->chief_firstname, 'FIRSTNAME', 'slf_persons' ) . ') and rwc.clevel in (0, 1))';
		}
		if ( $Return->chief_lastname )
		{
			$where[] = ' t.id in 
            (
                select
                    rwc.worker 
                from 
                    rel_worker_chief rwc 
                where rwc.chief_pid in (' . $this->_search( $Return->chief_lastname, 'LASTNAME', 'slf_persons' ) . ') and rwc.clevel in (0, 1))';
		}
		if ( $Return->private_number )
		{
			$where[] = ' pr.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->position )
		{
			$where[] = ' lss.position in (select p.id from lib_positions p where p.lib_title like ' . DB::Quote( '%' . $Return->position . '%' ) . ')';
		}
		if ( $Return->tablenum != '' )
		{
			$where[] = ' t.tablenum like ' . DB::Quote( '%' . $Return->tablenum . '%' );
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' t.org= ' . DB::Quote( $Return->org );
		}
		if ( $Return->staff_schedule > 0 )
		{
			$where[] = ' t.staff_schedule = ' . DB::Quote( $Return->staff_schedule );
		}
		if ( !empty( $Return->hist_date ) )
		{
			$where[] = ' to_date(' . DB::Query( PDate::Get( $Return->hist_date )->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\') between t.hist_start_date and nvl( t.hist_end_date , to_date(\'2050-01-01\', \'yyyy-mm-dd\')) ';
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
		if ( $Return->userrole > 0 )
		{
			$where[] = ' pr.user_role=  ' . DB::Quote( $Return->userrole );
		}
		if ( $Return->category > 0 )
		{
			$where[] = ' t.category_id=  ' . DB::Quote( $Return->category );
		}
		if ( $Return->permit_id )
		{
			$where[] = ' pr.permit_id like ' . DB::Quote( '%' . $Return->permit_id . '%' );
		}
		if ( $Return->graphtype > -1 )
		{
			$where[] = ' t.graphtype=  ' . DB::Quote( $Return->graphtype );
		}
		if ( $Return->regime > -1 )
		{
			$where[] = ' t.calculus_regime=  ' . DB::Quote( $Return->regime );
		}
		if ( $Return->calculus_type > -1 )
		{
			$where[] = ' t.calculus_type=  ' . DB::Quote( $Return->calculus_type );
		}

		if ( empty( $tmpl ) && $Return->active != -1 )
		{
			$where[] = ' t.active = ' . $Return->active;
		}
		elseif ( !empty( $tmpl ) )
		{
			$where[] = ' t.active = 1 ';
		}
		else
		{
			$where[] = ' t.active = 1 ';
		}

		$add = '';
		if ( $tmpl == 'modal' )
		{
			$add = '_hist';
			$Return->hist_date = Request::getVar( 'date', '' );
			$s_date = !empty( $Return->hist_date ) ? $Return->hist_date : '';
			$in_date = PDate::Get( $s_date )->toFormat( '%Y-%m-%d' );
			$where[] = ' to_date(' . DB::Quote( $in_date ) . ', \'yyyy-mm-dd\') between trunc(t.hist_start_date) and nvl(trunc(t.hist_end_date) - 1/24/60/60, to_date(\'2050-01-01\', \'yyyy-mm-dd\')) ';
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  slf_worker' . $add . ' t '
						. ' left join slf_changes sc on sc.id = t.change_id '
						. ' left join lib_staff_schedules lss on lss.id = t.staff_schedule '
						. ' left join slf_persons pr on pr.id = t.person '
						. ' left join lib_units un on un.id = lss.org_place '
						. $whereQ
		;
		$Return->total = XRedis::getDBCache( 'slf_worker', $countQuery, 'LoadResult' );
//		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*, '
						. ' lss.position, '
						. ' lss.chief_schedule, '
						. ' pr.firstname, '
						. ' pr.lastname, '
						. ' pr.private_number, '
						. ' dd.change_date assignment_date, '
						. ' sc.change_date,'
						. ' lss.org_place, '
						. ' un.lib_title org_place_name '
						. ' from slf_worker' . $add . ' t '
						. ' left join slf_changes sc on sc.id = t.change_id '
						. ' left join lib_staff_schedules lss on lss.id = t.staff_schedule '
						. ' left join lib_staff_schedules ls on ls.id = lss.chief_schedule '
						. ' left join slf_persons pr on pr.id = t.person '
						. ' left join lib_units un on un.id = lss.org_place '
						. ' left join (select * from slf_changes where change_type in (1, 5) and status = 1 ) dd on dd.org = t.org and dd.worker_id = t.id '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, '
						. 'rownum rn from (' .
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
//			$Return->items = XRedis::getDBCache( 'slf_worker', $Limit_query, 'LoadObjectList' );
			$Return->items = DB::LoadObjectList( $Limit_query );
		}
		return $Return;

	}

}
