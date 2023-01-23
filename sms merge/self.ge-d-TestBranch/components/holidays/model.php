<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class HolidaysModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title' ], 'lib_holidays' ) . ')';
		}
		$where[] = 't.active >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  lib_holidays t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.id, '
						. ' t.lib_title, '
						. ' t.lib_month, '
						. ' t.lib_day, '
						. ' t.active'
						. ' from lib_holidays t '
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

		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$collect = [];
		$data = $Return->items;
		foreach ( $data as $item )
		{
			if ( $Return->lib_title && preg_match( '/' . $Return->lib_title . '/i', C::_( 'LIB_TITLE', $item ) ) )
			{
				$collect[] = $item;
			}
		}
		if ( count( $collect ) )
		{
			$Return->items = $collect;
		}

		return $Return;

	}

}
