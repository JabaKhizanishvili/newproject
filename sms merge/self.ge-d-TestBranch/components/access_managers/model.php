<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class Access_managersModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->DOOR_CONTROLLERS = trim( Request::getState( $this->_space, 'DOOR_CONTROLLERS', '' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, 'LIB_TITLE', 'lib_access_manager' ) . ')';
		}
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
		else
		{
			$where[] = 't.active >-1 ';
		}
		if ( $Return->DOOR_CONTROLLERS )
		{
			$where[] = ' t.id in ('
							. ' select '
							. ' rw.access_id '
							. ' from REL_ACCESS_MANAGER rw '
							. ' where '
							. ' rw.controller in ('
							. ' select '
							. ' id '
							. ' from lib_doors cw '
							. ' where cw.lib_title like ' . DB::Quote( '%' . $Return->DOOR_CONTROLLERS . '%' )
							. ' ))';
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  lib_access_manager t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_access_manager t '
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
