<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class certificatesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList( $All = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->cert_number = trim( Request::getState( $this->_space, 'cert_number', '' ) );
		$Return->cert_date = Request::getState( $this->_space, 'cert_date', array() );
		$Return->cert_due = Request::getState( $this->_space, 'cert_due', array() );
		$Return->status = (int) trim( Request::getState( $this->_space, 'status', '-1' ) );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' w.id in (' . $this->_search( $Return->firstname, 'FIRSTNAME', 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' w.id in (' . $this->_search( $Return->lastname, 'LASTNAME', 'slf_persons' ) . ')';
		}
		$DateStart = trim( C::_( 'start', $Return->cert_date ) );
		$DateEnd = trim( C::_( 'end', $Return->cert_date ) );
		if ( $DateStart )
		{
			$where[] = ' t.cert_date >= to_date(' . DB::Quote( PDate::Get( $DateStart )->toFormat( '%Y-%m-%d ' ) ) . ', ' . DB::Quote( 'yyyy-mm-dd' ) . ') ';
		}
		if ( $DateEnd )
		{
			$where[] = ' t.cert_date <= to_date(' . DB::Quote( PDate::Get( $DateEnd )->toFormat( '%Y-%m-%d ' ) ) . ', ' . DB::Quote( 'yyyy-mm-dd' ) . ') ';
		}
		$DueStart = trim( C::_( 'start', $Return->cert_due ) );
		$DueEnd = trim( C::_( 'end', $Return->cert_due ) );
		if ( $DueStart )
		{
			$where[] = ' t.cert_due >= to_date(' . DB::Quote( PDate::Get( $DueStart )->toFormat( '%Y-%m-%d ' ) ) . ', ' . DB::Quote( 'yyyy-mm-dd' ) . ') ';
		}
		if ( $DueEnd )
		{
			$where[] = ' t.cert_due <= to_date(' . DB::Quote( PDate::Get( $DueEnd )->toFormat( '%Y-%m-%d ' ) ) . ', ' . DB::Quote( 'yyyy-mm-dd' ) . ') ';
		}
		if ( $Return->cert_number )
		{
			$where[] = ' t.cert_number like ' . DB::Quote( '%' . $Return->cert_number . '%' );
		}
		if ( $Return->status > -1 )
		{
			$where[] = ' t.active= ' . $Return->status;
		}
		else
		{
			$where[] = 't.active >-1 ';
		}

		$where[] = 't.active >-1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select '
						. ' count(*) '
						. ' from rel_worker_cert t '
						. ' left join slf_persons w on w.id=t.worker '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select'
						. ' w.firstname,'
						. ' w.lastname, '
						. ' w.private_number, '
						. ' t.cert_number, '
						. ' to_char(t.cert_date, \'dd-mm-yyyy\') cert_date, '
						. ' to_char(t.cert_due, \'dd-mm-yyyy\') cert_due, '
						. ' t.active, '
						. ' t.id '
						. ' from rel_worker_cert t '
						. ' left join slf_persons w on w.id=t.worker '
						. $whereQ
						. $order_by
		;
		if ( $All )
		{
			$Limit_query = $Query;
		}
		else
		{
			$Limit_query = 'select * from ( '
							. ' select a.*, rownum rn from (' .
							$Query
							. ') a) where rn > '
							. $Return->start
							. ' and rn <= ' . $Return->limit;
		}
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

}
