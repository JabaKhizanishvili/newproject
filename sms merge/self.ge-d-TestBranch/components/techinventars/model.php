<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class techinventarsModel extends Model
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
		$Return->lib_desc = trim( Request::getState( $this->_space, 'lib_desc', '' ) );
		$Return->workingorder = trim( Request::getState( $this->_space, 'workingorder', '' ) );
		$Return->maintenance_start = trim( Request::getState( $this->_space, 'maintenance_start', '' ) );

//		$Return->vnumber = trim( Request::getState( $this->_space, 'vnumber', '' ) );
		$Return->active = (int) Request::getState( $this->_space, 'active', '-1' );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title', 'lib_desc' ], 'lib_techinventar' ) . ')';
		}
        if ( $Return->lib_desc )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_desc, [ 'lib_desc'], 'lib_techinventar' ) . ')';
		}
//
        if ( $Return->workingorder )
        {
            $where[] = ' t.id in (' . $this->_search( $Return->workingorder, [ 'workingorder' ], 'lib_techinventar' ) . ')';
        }

        if ( Xhelp::checkDate( $Return->maintenance_start ) )
        {
            $Start_date = new PDate( $Return->maintenance_start );
            $where[] = ' t.maintenance_start < to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
        }

		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}


		$where[] = 't.active >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  lib_techinventars t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_techinventar t '
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
