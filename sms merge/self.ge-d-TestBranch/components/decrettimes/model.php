<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class DecretTimesModel extends Model
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
		$Return->permitid = trim( Request::getState( $this->_space, 'permitid', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '' ) );
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );

		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '-1' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}


		if ( $Return->org > 0 )
		{
			$where[] = ' t.org= ' . DB::Quote( $Return->org );
		}
		if ( $Return->unit > 0 )
		{
			$where[] = ' t.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units lu '
							. ' left join lib_units u on u.lft <= lu.lft and u.rgt >= lu.rgt and u.id = ' . DB::Quote( $Return->unit )
							. ' where '
							. ' lu.active = 1 '
							. ' and u.id is not null )'
			;
		}



		if ( $Return->category > 0 )
		{
			$where[] = ' w.category_id = ' . $Return->category;
		}
		$where[] = 't.status >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  hrs_decret_hour t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') start_date_d, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') end_date_d, '
						. ' to_char(t.approve_date, \'dd-mm-yyyy hh24:mi:ss\') approve_date_d, '
						. ' w.firstname wfirstname, '
						. ' w.lastname wlastname, '
						. ' app.firstname afirstname, '
						. ' app.lastname alastname '
						. ' from hrs_decret_hour t '
						. ' left join hrs_workers_sch w on w.id = t.worker '
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
