<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class hrgraphtimesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->name = trim( Request::getState( $this->_space, 'name', '' ) );
		$Return->ORG = (int) trim( Request::getState( $this->_space, 'ORG', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();

		if ( $Return->ORG > 0 )
		{
//            if (!Helper::CheckTaskPermision( 'admin', $this->_option))  {
//                $UserID = Users::GetUserID();
//                $where[] = 't.id in( select distinct tg.time_id from REL_TIME_GROUP tg right join REL_WORKERS_GROUPS wg on wg.group_id = tg.group_id left join rel_worker_chief wc on wc.worker = wg.worker where wg.worker  in (select m.worker from rel_worker_chief m where m.chief_pid =' . $UserID . '))';
//            }
			$where[] = ' t.org = ' . $Return->ORG;
		}

		if ( $Return->name )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->name, [ 'lib_title' ], 'lib_graph_times' ) . ')';
		}
		$where[] = ' t.active >-1 ';
		$where[] = ' t.type = 0 ';
		$where[] = ' t.owner >0 '; //. implode( ',', Helper::getChiefGroups() ) . ')';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  lib_graph_times t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_graph_times t '
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
