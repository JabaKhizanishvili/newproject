<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once PATH_BASE . DS . 'defines/ModelReturn.php';

class WorkersModalModel extends Model
{
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = new ModelReturn();
		$Return->start = Request::getState( $this->_space, 'start', 0 );
		$Return->limit = $Return->start + Request::getState( 'items.limit.per.Page', 'pagination_limit', PAGE_ITEMS_LIMIT );
		$Return->order = Request::getState( $this->_space, 'order', $this->_order );
		$Return->dir = Request::getState( $this->_space, 'dir', $this->_dir );

		$Return->org = trim( Request::GetVar( 'org', '0' ) );
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->org_place = (int) Request::getState( $this->_space, 'org_place', '0' );
		$Return->groups = Request::getState( $this->_space, 'groups', '0' );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->groups )
		{
			$where[] = ' wg.worker is null ';
		}
		if ( $Return->org )
		{
			$where[] = ' u.org = ' . (int) $Return->org;
//			$where[] = ' u.enable = 1';
			$where[] = ' u.GRAPHTYPE = 0';
		}
		$where[] = 'u.active >0 ';
		$where[] = 'u.id >0 ';
		if ( !empty( $Return->firstname ) )
		{
			$where[] = 'lower(u.firstname)  like \'%' . mb_strtolower( $Return->firstname ) . '%\' ';
		}
		if ( !empty( $Return->lastname ) )
		{
			$where[] = 'lower(u.lastname)  like \'%' . mb_strtolower( $Return->lastname ) . '%\' ';
		}
		if ( !empty( $Return->position ) )
		{
			$where[] = 'lower(u.position)  like \'%' . mb_strtolower( $Return->position ) . '%\' ';
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from HRS_WORKERS_SCH u '
						. ' left join  rel_workers_groups wg on wg.worker = u.id  '
						. $whereQ
		;

		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' u.id, '
						. ' u.firstname wfirstname, '
						. ' u.lastname wlastname,  '
						. ' u.position, '
						. ' u.mobile_phone_number, '
//						. ' u.user_type, '
						. ' u.email, '
						. ' u.staff_schedule, '
						. ' u.org_place '
						. ' from HRS_WORKERS_SCH u '
						. ' left join rel_workers_groups wg on wg.worker = u.id '
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
