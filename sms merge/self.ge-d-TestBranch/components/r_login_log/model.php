<?php
// Edited by Irakli Gzirishvili 21-10-2021.
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_login_logModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList( $Full = false )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->log_ip = trim( Request::getState( $this->_space, 'log_ip', '' ) );
		$Return->browser_name = trim( Request::getState( $this->_space, 'browser_name', '' ) );
		$Return->os_name = trim( Request::getState( $this->_space, 'os_name', '' ) );
		$Return->log_prev_user_name = trim( Request::getState( $this->_space, 'log_prev_user_name', '' ) );
		$Return->log_user_name = trim( Request::getState( $this->_space, 'log_user_name', '' ) );
		$Return->login_ident = trim( Request::getState( $this->_space, 'login_ident', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '' ) );
		$Return->end_date = trim( Request::getState( $this->_space, 'end_date', '' ) );
		
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->start_date )
		{
			$StartDate = new PDate( $Return->start_date );
			$where[] = ' w.log_date >= to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') ';
		}
		if ( $Return->end_date )
		{
			$EndDate = new PDate( $Return->end_date );
			$where[] = ' w.log_date <= to_date(\'' . $EndDate->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\') ';
		}
		if ( $Return->login_ident == '1' )
		{
			$where[] = ' w.log_prev_user_name = w.log_user_name ';
		}
		if ( $Return->login_ident == '0' )
		{
			$where[] = ' w.log_prev_user_name != w.log_user_name ';
		}
		if ( $Return->log_ip )
		{
			$where[] = ' w.log_ip like ' . DB::Quote( '%' . $Return->log_ip . '%' );
		}
		if ( $Return->browser_name )
		{
			$where[] = ' w.browser_name like ' . DB::Quote( '%' . $Return->browser_name . '%' );
		}
		if ( $Return->os_name )
		{
			$where[] = ' w.os_name like ' . DB::Quote( '%' . $Return->os_name . '%' );
		}
		if ( $Return->log_prev_user_name )
		{
			$where[] = ' w.log_prev_user_name like ' . DB::Quote( '%' . $Return->log_prev_user_name . '%' );
		}
		if ( $Return->log_user_name )
		{
			$where[] = ' w.log_user_name like ' . DB::Quote( '%' . $Return->log_user_name . '%' );
		}
		
		$where[] = ' w.id >0 ';
		$where[] = ' w.log_user_id >0 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from HRS_LOGIN_LOG w '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' w.* '
						. ' from HRS_LOGIN_LOG w '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		if ( $Full )
		{
			$Return->items = DB::LoadObjectList( $Query );
		}
		else
		{
			$Return->items = DB::LoadObjectList( $Limit_query );
		}
		return $Return;

	}

}
