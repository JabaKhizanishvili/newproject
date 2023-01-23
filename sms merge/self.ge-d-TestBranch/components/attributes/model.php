<?php
defined('PATH_BASE') or die('Restricted access');

class attributesModel extends Model
{
    /**
     *
     * @return array
     */
    public function getList()
    {
        /* @var $Return ModelReturn */
        $Return = $this->getReturn();

        $dir = ($Return->dir == 1) ? 'desc' : 'asc';
        $order = $Return->order;
        $order_by = ' order by ' . $order . ' ' . $dir;
        $Return->lib_title = trim(Request::getState($this->_space, 'lib_title', ''));
        $Return->active = (int)trim(Request::getState($this->_space, 'active', '-1'));
        $Return->show_in_profile = (int)trim(Request::getState($this->_space, 'show_in_profile', '-1'));
        $Return->destination = (int)trim(Request::getState($this->_space, 'destination', '-1'));

        $where = array();

        if ($Return->lib_title) {
            $where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title', 'lib_desc' ], 'lib_attributes' ) . ')';

        }
        if ($Return->show_in_profile > -1) {
            $where[] = ' t.show_in_profile = ' . $Return->show_in_profile;
        }
        if ($Return->destination > -1) {
            $where[] = ' t.destination = ' . $Return->destination;
        }
        if ($Return->active > -1) {
            $where[] = ' t.active= ' . $Return->active;
        } else {
            $where[] = 't.active >-1 ';
        }

        $whereQ = count($where) ? ' WHERE (' . implode(') AND (', $where) . ')' : '';

        $countQuery = 'select count(*) from lib_attributes t '
            . $whereQ;

        $Return->total = DB::LoadResult($countQuery);

        $Query = 'select '
            . ' t.* '
            . ' from lib_attributes t '
            . $whereQ
            . $order_by;

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
