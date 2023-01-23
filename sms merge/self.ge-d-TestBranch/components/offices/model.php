<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class OfficesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '1' ) );
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, 'LIB_TITLE', 'lib_offices' ) . ')';
		}
		
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . DB::Quote( $Return->active );
		}

		$where[] = 't.active >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  lib_offices t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. 't.* '
						. ' from lib_offices t '
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
