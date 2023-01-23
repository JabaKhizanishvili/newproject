<?php
// Edited by Irakli Gzirishvili 21-10-2021.
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class r_active_sessions_logModel extends Model
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
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->firstname )
		{
			$where[] = ' wr.id in (' . $this->_search( $Return->firstname, [ 'firstname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->lastname )
		{
			$where[] = ' wr.id in (' . $this->_search( $Return->lastname, [ 'lastname' ], 'slf_persons' ) . ')';
		}
		if ( $Return->log_ip )
		{
			$where[] = ' w.log_ip like ' . DB::Quote( '%' . $Return->log_ip . '%' );
		}
		if ( $Return->browser_name )
		{
			$where[] = ' w.browser_name  like ' . DB::Quote( '%' . $Return->browser_name . '%' );
		}
		if ( $Return->os_name )
		{
			$where[] = ' w.os_name like ' . DB::Quote( '%' . $Return->os_name . '%' );
		}

		$where[] = ' w.log_user_id >0 ';
//		$where[] = ' w.username = ' . DB::Quote( C::_( 'LDAP_USERNAME', Users::getUser() ) ) ;
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from SYSTEM_SESSIONS w '
						. ' left join slf_persons wr on wr.id = w.log_user_id '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' wr.firstname wfirstname, '
						. ' wr.lastname wlastname,  '
						. ' wr.private_number, '
						. ' wr.mobile_phone_number, '
						. ' w.id, '
						. ' w.log_ip, '
						. ' w.browser_name, '
						. ' w.os_name, '
						. ' w.log_user_id, '
						. ' w.start_date, '
						. ' w.end_date, '
						. ' w.log_date '
						. ' from SYSTEM_SESSIONS w '
						. ' left join slf_persons wr on wr.id = w.log_user_id '
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

	public function Delete( $data )
	{
		$all = [];
		foreach ( $data as $key => $id )
		{
			$all[] = DB::Quote( $id );
		}
		$Q = 'DELETE FROM SYSTEM_SESSIONS WHERE ID IN (' . implode( ',', $all ) . ')';
		return DB::Delete( $Q );

	}

	public function DeleteAll()
	{
		$Q = 'DELETE FROM SYSTEM_SESSIONS WHERE SESSION_ID != ' . DB::Quote( Session::getSessionID() );
		return DB::Delete( $Q );;

	}

}
