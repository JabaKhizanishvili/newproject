<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class FlowElementsModel extends Model
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
		$Return->title = mb_strtolower( trim( Request::getState( $this->_space, 'title', '' ) ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->FLOW = (int) Request::getState( $this->_space, 'FLOW', '' );
		$where = array();
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
		else
		{
			$where[] = 't.active >-1 ';
		}
		if ( $Return->title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->title, [ 'lib_title' ], 'lib_flow_elements' ) . ')';
		}
		if ( $Return->FLOW > -1 )
		{
			$where[] = ' t.flow = ' . $Return->FLOW;
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  lib_flow_elements t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*, '
//						. ' tk.LIB_TITLE as task_title, '
//						. ' tk.LIB_FILE, '
						. ' w.LIB_TITLE FLOWTITLE '
						. ' from lib_flow_elements t '
						. ' left join LIB_LIMIT_APP_TYPES w on w.id=t.FLOW '
//						. ' left join LIB_TASKS tk on tk.id=t.LIB_TASK '
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
