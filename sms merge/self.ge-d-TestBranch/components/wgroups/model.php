<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class wgroupsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->search = trim( Request::getState( $this->_space, 'search', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$where[] = ' t.gtype=0 ';
		if ( !empty( $Return->search ) )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->search, [ 'lib_title' ], 'lib_wgroups' ) . ')';
		}
		$where[] = 't.active >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  lib_wgroups t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
//						. ' k.workers_list '
						. ' from lib_wgroups t '
//						. ' left join ('
//						. ' select '
//						. ' wg.group_id, '
//						. ' listagg(w.firstname || \' \' || w.lastname,\', \') WITHIN GROUP(order by wg.ordering asc) workers_list '
//						. ' from rel_workers_groups wg '
//						. ' left join cws_workers w on w.id = wg.worker and w.active = 1 '
//						. ' group by wg.group_id'
//						. ' ) k  on k.group_id = t.id '
						. $whereQ . ' '
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
