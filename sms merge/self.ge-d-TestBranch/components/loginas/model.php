<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class LoginASModel extends Model
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
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->sphere = (int) trim( Request::getState( $this->_space, 'sphere', '' ) );
		$Return->department = (int) trim( Request::getState( $this->_space, 'department', '' ) );
		$Return->chapter = (int) trim( Request::getState( $this->_space, 'chapter', '' ) );
//        $Return->active = (int) trim(Request::getState($this->_space, 'active', '-1'));
		$Return->user_role = (int) Request::getState( $this->_space, 'user_role', '-1' );
		$where = array();
		if ( $Return->lib_title )
		{
			$where[] = ' t.firstname like ' . DB::Quote( '%' . $Return->lib_title . '%' ) . ' or  t.lastname like ' . DB::Quote( '%' . $Return->lib_title . '%' );
		}
		if ( $Return->sphere )
		{
			$where[] = ' w.sphere= ' . $Return->sphere;
		}
		if ( $Return->department )
		{
			$where[] = ' w.department= ' . $Return->department;
		}
		if ( $Return->chapter )
		{
			$where[] = ' w.chapter= ' . $Return->chapter;
		}
		if ( $Return->user_role > 0 )
		{
			$where[] = ' t.user_role= ' . $Return->user_role;
		}
		$where[] = 't.active >-1 ';
		$where[] = 't.id>-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_persons t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.*, '
						. ' r.LIB_TITLE role '
						. ' from slf_persons t '
						. ' left join lib_roles r on r.id = t.user_role '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select k.*, '
						. ' getChiefsByWorker(k.id) all_chiefs '
						. ' from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) k where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

}
