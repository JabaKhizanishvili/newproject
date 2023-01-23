<?php

defined('PATH_BASE') or die('Restricted access');

class ReleasetypesModel extends Model
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
		$Return->active = (int) Request::getState( $this->_space, 'active', '-1' );

        $dir = ($Return->dir == 1) ? 'desc' : 'asc';
        $order = $Return->order;
        $order_by = ' order by ' . $order . ' ' . $dir;

        $where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title' ], 'lib_release_type' ) . ')';
		}
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
        $where[] = 't.active >-1 ';
        $whereQ = count($where) ? ' WHERE (' . implode(') AND (', $where) . ')' : '';
        $countQuery = 'select count(*) from  lib_release_type t '
                . $whereQ
        ;
        $Return->total = DB::LoadResult($countQuery);
        $Query = 'select '
                . ' t.* '
                . ' from lib_release_type t '
                . $whereQ
                . $order_by
        ;
        $Limit_query = 'select * from ( '
                . ' select a.*, rownum rn from (' .
                $Query
                . ') a) where rn > '
                . $Return->start
                . ' and rn <= ' . $Return->limit;
        $Return->items = DB::LoadObjectList($Limit_query);
        return $Return;
    }

}
