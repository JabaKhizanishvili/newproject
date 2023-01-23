<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class UnitsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$order_by = ' order by lft asc ';
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->type = (int) trim( Request::getState( $this->_space, 'type', '' ) );
		$Return->ulevel = (int) trim( Request::getState( $this->_space, 'ulevel', '' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '-1' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '0' ) );
		$Return->unit_code = trim( Request::getState( $this->_space, 'unit_code', '' ) );
		$Return->NO = false;
		$where = array();
		if ( !($Return->org > 0 ) )
		{
			$Return->items = array();
			$Return->NO = true;
			return $Return;
		}
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, 'LIB_TITLE', 'lib_units' ) . ')';
		}
		if ( $Return->unit_code != '' )
		{
			$where[] = ' t.unit_code like ' . DB::Quote( '%' . $Return->unit_code . '%' );
		}
		if ( $Return->ulevel > 0 )
		{
			$where[] = ' t.ulevel <=  ' . $Return->ulevel;
		}
		if ( $Return->type > 0 )
		{
			$where[] = ' t.type =  ' . $Return->type;
		}
		if ( $Return->org > 0 )
		{
			$where[] = ' t.org =  ' . $Return->org;
		}
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
		else
		{
			$where[] = 't.active >-1 ';
		}
		if ( $Return->org_place > 0 )
		{
			$where[] = ' t.id in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->org_place )
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from lib_units t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_units t '
						. $whereQ
						. $order_by
		;

		$Items = DB::LoadObjectList( $Query );
		$Return->items = array();
		foreach ( $Items as $Item )
		{
			if ( $Item->ULEVEL > 0 )
			{
				$Item->TITLE = str_repeat( '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $Item->ULEVEL ) . '|_ ' . XTranslate::_( $Item->LIB_TITLE );
			}
			else
			{
				$Item->TITLE = XTranslate::_( $Item->LIB_TITLE );
			}
			$Return->items[] = $Item;
		}
		return $Return;

	}

}
