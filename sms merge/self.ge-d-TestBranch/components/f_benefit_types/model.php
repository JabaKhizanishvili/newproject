<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class f_benefit_typesModel extends Model
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
		$Return->regularity = (int) Request::getState( $this->_space, 'regularity', '-1' );
		$Return->category = (int) Request::getState( $this->_space, 'category', '-1' );
		$Return->type = (int) Request::getState( $this->_space, 'type', '-1' );
		$Return->class = (int) Request::getState( $this->_space, 'class', '-1' );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title' ], 'lib_f_benefit_types' ) . ')';
		}
		if ( $Return->class > -1 )
		{
			$where[] = ' t.class= ' . DB::Quote( $Return->class );
		}
		if ( $Return->type > -1 )
		{
			$where[] = ' t.type= ' . DB::Quote( $Return->type );
		}
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . DB::Quote( $Return->active );
		}
		if ( $Return->regularity > -1 )
		{
			$where[] = ' t.regularity= ' . DB::Quote( $Return->regularity );
		}
		if ( $Return->category > -1 )
		{
			$where[] = ' t.benefit= ' . DB::Quote( $Return->category );
		}
		$where[] = ' t.active >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from lib_f_benefit_types t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_f_benefit_types t '
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
