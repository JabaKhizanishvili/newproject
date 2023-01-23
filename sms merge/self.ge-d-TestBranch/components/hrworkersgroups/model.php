<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class hrworkersgroupsModel extends Model
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
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();

		if ( $Return->firstname )
		{
			$where[] = ' t.id in ( select t.id from lib_workers_groups t
                LEFT JOIN REL_WORKERS_GROUPS g on t.id = g.group_id
                LEFT JOIN HRS_WORKERS_SCH s on g.worker = s.id
                    where
                s.parent_id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . '))';
		}
		if ( $Return->lastname )
		{
			$where[] = ' t.id in ( select t.id from lib_workers_groups t
                LEFT JOIN REL_WORKERS_GROUPS g on t.id = g.group_id
                LEFT JOIN HRS_WORKERS_SCH s on g.worker = s.id
                    where
                s.parent_id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . '))';
		}

		if ( $Return->ORG > 0 )
		{

//            if (!Helper::CheckTaskPermision( 'admin', $this->_option))  {
//                $UserID = Users::GetUserID();
//                $where[] = 't.id in('
//                    . ' select '
//                    . ' distinct (wg.group_id) '
//                    . ' from rel_workers_groups wg '
//                    . ' where '
//                    . 't.org = ' . $Return->ORG
//                    . ' and wg.worker in ('
//                    . ' select '
//                    . ' wc.worker '
//                    . ' from rel_worker_chief wc '
//                    . ' where '
//                    . ' wc.chief_pid   =  ' . $UserID
//                    . '))';
//            }
			$where[] = ' t.org = ' . $Return->ORG;
			if ( $Return->name )
			{
				$where[] = ' t.id in (' . $this->_search( $Return->name, [ 'lib_title' ], 'lib_workers_groups' ) . ')';
			}

			$where[] = 't.active >-1 ';
//        $where[] = 't.owner in(' . implode(',', Helper::getChiefSections()) . ')';
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
