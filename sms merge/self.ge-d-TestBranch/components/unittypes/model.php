<?php

defined('PATH_BASE') or die('Restricted access');

class UnitTypesModel extends Model
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

        $where = array();
        $where[] = 't.active >-1 ';
        $whereQ = count($where) ? ' WHERE (' . implode(') AND (', $where) . ')' : '';
        $countQuery = 'select count(*) from  lib_unittypes t '
                . $whereQ
        ;
        $Return->total = DB::LoadResult($countQuery);
        $Query = 'select '
                . ' t.* '
                . ' from lib_unittypes t '
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
