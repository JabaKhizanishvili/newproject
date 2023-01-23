<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class f_regular_benefitsModel extends Model
{
	/**
	 *
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->org = (int) Request::getState( $this->_space, 'org', '0' );
		$Return->period = array_diff( Request::getState( $this->_space, 'period', [] ), [ '' ] );

		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->active = (int) Request::getState( $this->_space, 'active', '-1' );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$Return->stop = 2;
		if ( $Return->org > 0 )
		{
			$where[] = ' w.org = ' . $Return->org;
			$Return->stop--;
		}

		if ( count( $Return->period ) > 0 )
		{
			$where[] = ' t.period_id in (' . implode( ', ', $Return->period ) . ') ';
			$Return->stop--;
		}

		if ( $Return->lib_title )
		{
			$where[] = ' t.lib_title like ' . DB::Quote( '%' . $Return->lib_title . '%' ) . ' OR t.lib_desc like ' . DB::Quote( '%' . $Return->lib_title . '%' );
		}

		if ( $Return->active > -1 )
		{
			$where[] = ' t.active= ' . $Return->active;
		}

		if ( $Return->stop != 0 )
		{
			return $Return;
		}

		$where[] = ' t.regularity in (1, 3) ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_worker_benefits t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from slf_worker_benefits t '
						. ' left join slf_worker w on w.id = t.worker '
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
