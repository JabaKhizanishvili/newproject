<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_checklistModel extends Model
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
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->category = (int) trim( Request::getState( $this->_space, 'category', '-1' ) );
		$Return->user_role = (int) Request::getState( $this->_space, 'user_role', '-1' );
		$Return->orgid = (int) trim( Request::getState( $this->_space, 'orgid', '' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$where = array();
		$WQ = array();
		$whr = array();

		if ( $Return->firstname )
		{
			$where[] = ' p.id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' p.id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->orgid > 0 )
		{
			$WQ[] = ' ss.active=1 ';
			$WQ[] = ' ss.org =' . $Return->orgid;
			$whr[] = ' sw.org = ' . DB::Quote( $Return->orgid );
		}
		if ( $Return->category > 0 )
		{
			$WQ[] = ' ss.category_id =' . $Return->category;
			$whr[] = ' sw.category_id = ' . DB::Quote( $Return->category );
		}
		if ( $Return->user_role > 0 )
		{
			$where[] = ' p.user_role = ' . DB::Quote( $Return->user_role );
		}
		if ( $Return->org_place > 0 )
		{
			$WQ[] = ' ls.org_place =' . $Return->org_place;
			$whr[] = ' ls.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->org_place
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}
		if ( count( $WQ ) > 0 )
		{
			$wQ = count( $WQ ) ? ' WHERE (' . implode( ') AND (', $WQ ) . ')' : '';
			$where[] = ' p.id in ( select ss.person from slf_worker ss left join lib_staff_schedules ls on ls.id = ss.staff_schedule ' . $wQ . ')';
		}
		$whr[] = ' lu.active = 1';
		$where[] = 'p.active = 1 ';
		$where[] = 'p.id > -1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from (select '
						. ' p.*, '
						. ' p.firstname wfirstname, '
						. ' p.lastname wlastname,  '
						. ' lc.lib_title as country '
						. ' from slf_persons p '
						. ' left join lib_country lc on lc.lib_code = p.country_code '
						. $whereQ
						. ' and (p.id in ( select ss.person from slf_worker ss where ss.org in (select cc.id from lib_unitorgs cc where cc.active =1 )))'
//						. $order_by
						. ' UNION ALL '
						. ' select '
						. ' p.*, '
						. ' p.firstname wfirstname, '
						. ' p.lastname wlastname,  '
						. ' lc.lib_title as country '
						. ' from slf_persons p '
						. ' left join lib_country lc on lc.lib_code = p.country_code '
						. $whereQ
						. ' and (p.id not in ( select ss.person from slf_worker ss))) dum '
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select* from (select '
						. ' p.*, '
						. ' p.firstname wfirstname, '
						. ' p.lastname wlastname,  '
						. ' lc.lib_title as country '
						. ' from slf_persons p '
						. ' left join lib_country lc on lc.lib_code = p.country_code '
						. $whereQ
						. ' and (p.id in ( select ss.person from slf_worker ss where ss.org in (select cc.id from lib_unitorgs cc where cc.active =1 )))'
//						. $order_by
						. ' UNION ALL '
						. ' select '
						. ' p.*, '
						. ' p.firstname wfirstname, '
						. ' p.lastname wlastname,  '
						. ' lc.lib_title as country '
						. ' from slf_persons p '
						. ' left join lib_country lc on lc.lib_code = p.country_code '
						. $whereQ
						. ' and (p.id not in ( select ss.person from slf_worker ss))) dum '
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from ('
						. $Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query, 'ID' );
		$Keys = array_keys( $Return->items );
		$Collect = XHelp::getAssignedWorkers( implode( ', ', $Keys ), $whr );
		foreach ( $Keys as $Key )
		{
			$Return->items[$Key]->ORG = C::_( $Key, $Collect, array() );
		}

		return $Return;

	}

	public function E_xport()
	{
		global $DisableSF;
		$DisableSF = 1;
		$Data = $this->getList( true );
		$Rows = HTML::renderExport( $Data->items, PATH_BASE . DS . 'components' . DS . $this->_option . DS . 'tmpl' . DS . 'export.xml' );
		$name = C::_( 'FILENAME', $Rows ) . '-' . date( 'Y-m-d' ) . '-' . time() . '.xlsx';
		$EData = array();
		$Cols = count( $Rows['HEADER'] );
		if ( empty( $Cols ) )
		{
			return false;
		}
		$EData[] = $Rows['HEADER'];
		foreach ( $Rows['ROWS'] as $d )
		{
			$add = array();
			if ( is_array( $d ) || is_object( $d ) )
			{
				foreach ( $d as $v )
				{
					if ( is_null( $v ) )
					{
						$v = Text::_( '' );
					}
					$add[] = $v;
				}
				$EData[] = $add;
			}
		}
		$XLSX = SimpleXLSXGen::fromArray( $EData );
		$XLSX->downloadAs( $name );
		die;

	}

}
