<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class VisitorsModel extends Model
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
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->state = (int) trim( Request::getState( $this->_space, 'state', '-1' ) );
		$Return->lib_code = trim( Request::getState( $this->_space, 'lib_code', '' ) );
//		$Return->assign = (int) trim( Request::getState( $this->_space, 'assign', '-1' ) );
		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title' ], 'lib_visitors' ) . ')';
		}
		if ( $Return->lib_code )
		{
			$where[] = ' t.code like ' . DB::Quote( '%' . $Return->lib_code . '%' );
		}
		if ( $Return->state > -1 )
		{
			$where[] = ' t.state = ' . DB::Quote( $Return->state );
		}
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
		else
		{
			$where[] = 't.active >-1 ';
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$countQuery = 'select count(*) from  lib_visitors t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_visitors t '
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
