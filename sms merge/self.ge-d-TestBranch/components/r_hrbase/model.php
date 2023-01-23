<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_hrbaseModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->chief = trim( Request::getState( $this->_space, 'chief', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->permit_id = trim( Request::getState( $this->_space, 'permit_id', '' ) );
		$Return->mobile_phone_number = trim( Request::getState( $this->_space, 'mobile_phone_number', '' ) );
		$Return->email = trim( Request::getState( $this->_space, 'email', '' ) );
		$Return->tablenum = trim( Request::getState( $this->_space, 'tablenum', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', -1 ) );
		$Return->gender = (int) trim( Request::getState( $this->_space, 'gender', -1 ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '0' ) );
		$Return->graphtype = (int) trim( Request::getState( $this->_space, 'graphtype', '-1' ) );
		$Return->staffschedule = (int) trim( Request::getState( $this->_space, 'staffschedule', '' ) );
		$Return->nationality = (int) trim( Request::getState( $this->_space, 'nationality', '-1' ) );
		$Return->category_id = (int) trim( Request::getState( $this->_space, 'category_id', '-1' ) );
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
		if ( $Return->chief )
		{
			$where[] = ' p.id in (select aa.worker_pid from rel_worker_chief aa where aa.chief_pid in (select id from slf_persons where  id in (' . $this->_search( $Return->chief, [ 'lastname', 'lastname' ], 'slf_persons' ) . ')))';
		}
		if ( $Return->email )
		{
			$where[] = ' p.email like ' . DB::Quote( '%' . $Return->email . '%' );
		}
		if ( $Return->mobile_phone_number != '' )
		{
			$where[] = ' p.mobile_phone_number like ' . DB::Quote( '%' . $Return->mobile_phone_number . '%' );
		}
		if ( $Return->private_number != '' )
		{
			$where[] = ' p.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}
		if ( $Return->permit_id != '' )
		{
			$where[] = ' p.permit_id like ' . DB::Quote( '%' . $Return->permit_id . '%' );
		}
		if ( $Return->gender > -1 )
		{
			$where[] = ' p.gender =' . DB::Quote( $Return->gender );
		}
		if ( $Return->nationality > -1 )
		{
			$where[] = ' p.nationality =' . DB::Quote( $Return->nationality );
		}
		if ( $Return->staffschedule )
		{
			$where[] = ' p.id in ( select ss.person from slf_worker ss where ss.staff_schedule  = ' . DB::Quote( $Return->staffschedule ) . ' and ss.active = 1)';
			$whr[] = ' ls.id like ' . DB::Quote( '%' . $Return->staffschedule . '%' );
		}
		if ( $Return->tablenum != '' )
		{
			$where[] = ' p.id in ( select ss.person from slf_worker ss where ss.tablenum  like ' . DB::Quote( '%' . $Return->tablenum . '%' ) . ' and ss.active = 1)';
			$whr[] = ' sw.tablenum like ' . DB::Quote( '%' . $Return->tablenum . '%' );
		}
		if ( $Return->graphtype > -1 )
		{
			$where[] = ' p.id in ( select ss.person from slf_worker ss where ss.graphtype  = ' . DB::Quote( $Return->graphtype ) . ' and ss.active = 1)';
			$whr[] = ' sw.graphtype = ' . DB::Quote( $Return->graphtype );
		}
        if ( $Return->org > 0 )
        {
            $where[] = ' p.id in ( select ss.person from slf_worker ss where ss.org =' . $Return->org . ' and ss.active = 1)';
            $whr[] = ' sw.org = ' . DB::Quote( $Return->org );
        }
        if ( $Return->category_id > 0 ) {
            $where[] = ' p.id in ( 
                                    select ss.person 
                                    from slf_worker ss 
                                    left join lib_unitorgs lu on lu.id = ss.org
                                    where ss.category_id =' . $Return->category_id . ' and ss.active = 1 AND lu.active = 1)';
            $whr[] = ' sw.category_id = ' . DB::Quote( $Return->category_id );
        }
		if ( $Return->org_place > 0 )
		{
			$where[] = ' p.id in ( select ss.person from slf_worker ss '
							. ' left join lib_staff_schedules lm on lm.id = ss.staff_schedule '
							. 'where lm.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->org_place
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null) '
							. ' and ss.active = 1)';
			$whr[] = ' ls.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->org_place
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null)'
			;
		}
		if ( $Return->position )
		{
			$qq = ' ls.position in ( select pp.id from lib_positions  pp where pp.id in (' . $this->_search( $Return->position, [ 'lib_title' ], 'lib_positions' ) . '))';
			$whr[] = $qq;
			$where[] = ' p.id in ( select ss.person from slf_worker ss where ss.staff_schedule in( select ls.id from lib_staff_schedules ls where ' . $qq . '))';
		}
		$whr[] = ' lu.active = 1';
		$where[] = ' p.active = 1 ';
		$where[] = ' p.id > -1 ';
//		$where[] = ' p.id in ( select ss.person from slf_worker ss where ss.active = 1 )';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_persons p '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' p.*, '
						. ' p.firstname wfirstname, '
						. ' p.lastname wlastname,  '
						. ' lc.lib_title as country '
						. ' from slf_persons p '
						. ' left join lib_country lc on lc.lib_code = p.country_code '
						. $whereQ
						. $order_by
		;
		if ( $Full )
		{
			$Return->items = DB::LoadObjectList( $Query, 'ID' );
		}
		else
		{
			$Limit_query = 'select * from ( '
							. ' select a.*, rownum rn from ('
							. $Query
							. ') a) where rn > '
							. $Return->start
							. ' and rn <= ' . $Return->limit;
			$Return->items = DB::LoadObjectList( $Limit_query, 'ID' );
		}
		$Keys = array_keys( $Return->items );
		$KM = array_chunk( $Keys, 500 );
		$Collect = array();
		foreach ( $KM as $km )
		{
			$JJ = XHelp::getAssignedWorkers( implode( ', ', $km ), $whr );
			foreach ( $JJ as $K => $V )
			{
				$Collect [$K] = $V;
			}
		}
		foreach ( $Keys as $Key )
		{
			$Return->items[$Key]->ORG = C::_( $Key, $Collect, array() );
		}
		return $Return;

	}
//
//	public function E_xport()
//	{
//		global $DisableSF;
//		$DisableSF = 1;
//		$Data = $this->getList( true );
//		$Rows = HTML::renderExport( $Data->items, PATH_BASE . DS . 'components' . DS . $this->_option . DS . 'tmpl' . DS . 'export.xml' );
//		$name = C::_( 'FILENAME', $Rows ) . '-' . date( 'Y-m-d' ) . '-' . time() . '.xlsx';
//		$EData = array();
//		$Cols = count( $Rows['HEADER'] );
//		if ( empty( $Cols ) )
//		{
//			return false;
//		}
//		$EData[] = $Rows['HEADER'];
//		foreach ( $Rows['ROWS'] as $d )
//		{
//			$add = array();
//			if ( is_array( $d ) || is_object( $d ) )
//			{
//				foreach ( $d as $v )
//				{
//					if ( is_null( $v ) )
//					{
//						$v = Text::_( '' );
//					}
//					$add[] = $v;
//				}
//				$EData[] = $add;
//			}
//		}
//		$XLSX = SimpleXLSXGen::fromArray( $EData );
//		$XLSX->downloadAs( $name );
//		die;
//
//	}

}
