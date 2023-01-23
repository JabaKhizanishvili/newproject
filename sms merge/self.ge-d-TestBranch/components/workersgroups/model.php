<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class WorkersGroupsModel extends Model
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
		$UserID = Users::GetUserID();
		$where = array();

		if ( $Return->ORG > 0 )
		{
			$where[] = ' t.org = ' . DB::Quote( $Return->ORG );
			if ( $Return->name )
			{
				$where[] = ' t.id in (' . $this->_search( $Return->name, [ 'lib_title' ], 'lib_workers_groups' ) . ')';
			}

			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$DirectTreeUnion = ' or ww.person in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
			}
			if ( $AdditionalTree )
			{
				$AdditionalTreeUnion = ' or ww.person in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
			}

			$where[] = ' t.active >-1 ';
			$where[] = ' t.id in( '
							. ' select '
							. ' distinct (wg.group_id) '
							. ' from rel_workers_groups wg '
							. ' left join slf_worker ww on ww.id = wg.worker '
							. ' where '
							. ' t.org = ' . $Return->ORG
							. ' and wg.worker in ('
							. ' select '
							. ' wc.worker '
							. ' from rel_worker_chief wc '
							. ' where '
							. ' wc.chief_pid   =  ' . $UserID
							. ' ) ' . $DirectTreeUnion . $AdditionalTreeUnion . ')';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*) from  lib_workers_groups t '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' t.* '
							. ' from lib_workers_groups t '
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
			$Return->loaded = 1;
		}
		else
		{
			$Return->loaded = 0;
		}
		return $Return;

	}

}
