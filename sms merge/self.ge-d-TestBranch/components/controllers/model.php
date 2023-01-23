<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class controllersModel extends Model
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
			$where[] = ' t.id in (' . $this->_search( $Return->lib_title, 'LIB_TITLE', 'lib_controllers' ) . ')';
		}
		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
		$where[] = 't.active >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from lib_controllers t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* ,'
						. ' nvl(t.connection_status, 0) connection '
						. ' from lib_controllers t '
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

	function getHistory( $device_id = 0, $action = 'modal' )
	{
		$Return = $this->getReturn();
		$Return->device_id = $device_id;
		$Return->action = $action;
		$where = [];
		if ( $action == 'monitor' )
		{
			$where[] = ' t.rec_date > sysdate - 1 ';
		}

		$where[] = ' t.device_id = ' . (int) $device_id;
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_api_controllers_alerts t '
						. ' left join lib_controllers lc on lc.id = t.device_id '
						. $whereQ
		;

		if ( $action == 'monitor' )
		{
			$countQuery = 'select count(*) from slf_api_controllers t '
							. $whereQ
			;
		}

		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' lc.*,'
						. ' to_char(t.on_date, \'yyyy-mm-dd hh24:mi:ss\') on_date, '
						. ' to_char(t.off_date, \'yyyy-mm-dd hh24:mi:ss\') off_date '
						. ' from slf_api_controllers_alerts t '
						. ' left join lib_controllers lc on lc.id = t.device_id '
						. $whereQ
						. ' order by t.off_date desc '
		;

		if ( $action == 'monitor' )
		{
			$Query = 'select '
							. ' t.*, '
							. ' lc.lib_title controller '
							. ' from slf_api_controllers t '
							. ' left join lib_controllers lc on lc.id = t.device_id '
							. $whereQ
							. ' order by rec_date desc '
			;
		}

		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

	function getUndefineds()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$where = array();
		$where[] = ' t.registered = 0 ';
		$where[] = ' t.id = (select max(m.id) from slf_api_controllers m '
						. ' left join lib_controllers lc on lc.controller_code = m.controller_code and lc.active >-1 '
						. ' where '
						. ' m.registered = 0 '
						. ' and lc.id is null '
						. ' and m.controller_code=t.controller_code) '
		;
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_api_controllers t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from slf_api_controllers t '
						. $whereQ
						. ' order by rec_date desc '
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
