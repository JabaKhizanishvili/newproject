<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class newsModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->text = trim( Request::getState( $this->_space, 'text', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		$where[] = 't.active >-1 ';

		if ( $Return->text )
		{
			$where[] = ' t.id in (' . $this->_search( $Return->text, [ 'title' ], 'news' ) . ')';
		}

		if ( Xhelp::checkDate( $Return->start_date ) )
		{
			$Start_date = new PDate( $Return->start_date );
			$where[] = ' t.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') '
							. ' or   to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')  between t.start_date and t.end_date ';
		}
		if ( Xhelp::checkDate( $Return->end_date ) )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' t.end_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')'
							. ' or   to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')  between t.start_date and t.end_date '
			;
		}

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from  news t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select t.*, '
						. ' to_char(t.publish_date, \'dd-mm-yyyy\') v_publish_date, '
						. ' to_char(t.unpublish_date, \'dd-mm-yyyy\') v_unpublish_date, '
						. 'w.firstname||\' \'|| w.lastname worker  '
						. ' from news t '
						. ' left join hrs_workers w on w.id = t.modify_user and w.id > 0 '
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
