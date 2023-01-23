<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once PATH_BASE . DS . 'defines/ModelReturn.php';

class orgunitsModel extends Model
{
	function getList()
	{
		$Return = $this->getReturn();
		$order_by = ' order by lft asc ';
//		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
//		$Return->type = (int) trim( Request::getState( $this->_space, 'type', '' ) );
//		$Return->ulevel = (int) trim( Request::getState( $this->_space, 'ulevel', '' ) );
//		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '-1' ) );
		$Return->NO = false;
		$where = array();
		if ( !($Return->org > 0 ) )
		{
			$Return->items = array();
			$Return->NO = true;
			return $Return;
		}
//		if ( $Return->lib_title )
//		{
//			$where[] = ' t.lib_title like ' . DB::Quote( '%' . $Return->lib_title . '%' );
//		}
//		if ( $Return->ulevel > 0 )
//		{
//			$where[] = ' t.ulevel <=  ' . $Return->ulevel;
//		}
//		if ( $Return->type > 0 )
//		{
//			$where[] = ' t.type =  ' . $Return->type;
//		}
		if ( $Return->org > 0 )
		{
			$where[] = ' t.org =  ' . $Return->org;
		}
//		if ( $Return->active > -1 )
//		{
//			$where[] = ' t.active= ' . $Return->active;
//		}
//		else
//		{
//		}
		$where[] = 't.active >0 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from lib_units t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.lib_title TITLE, '
						. ' t.*, '
						. ' ut.lib_title unit_type '
						. ' from lib_units t '
						. ' left join lib_unittypes ut on ut.id = t.type '
						. $whereQ
						. $order_by
		;
		$Items = DB::LoadObjectList( $Query );
		$Return->items = array();
		foreach ( $Items as $Item )
		{
			$Parent = C::_( 'PARENT_ID', $Item );
			$Return->items[$Parent] = C::_( $Parent, $Return->items, array() );
			$Return->items[$Parent][] = $Item;
		}
		return $Return;

	}

}
