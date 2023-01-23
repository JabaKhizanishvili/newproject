<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class Staff_schedulesModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$order_by = ' order by lu.lft asc, t.ordering asc ';

		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->schedule_code = trim( Request::getState( $this->_space, 'schedule_code', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '1' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '0' ) );
		$where = array();
		$Return->loaded = 0;
		if ( $Return->org > 0 )
		{
			$where[] = 't.org = ' . DB::Quote( $Return->org );
			if ( $Return->lib_title )
			{
				$where[] = ' t.id in (' . $this->_search( $Return->lib_title, [ 'lib_title' ], 'lib_staff_schedules' ) . ')';
			}
			if ( $Return->schedule_code )
			{
				$where[] = ' t.schedule_code like ' . DB::Quote( '%' . $Return->schedule_code . '%' );
			}
			if ( $Return->org_place > 0 )
			{
				$where[] = ' t.org_place in( '
								. ' select '
								. ' t.id '
								. ' from lib_units t '
								. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . DB::Quote( $Return->org_place )
								. ' where '
								. ' t.active = 1 '
								. ' and u.id is not null )'
				;
			}
			if ( $Return->active > -1 )
			{
				$where[] = ' t.active= ' . $Return->active;
			}
			else
			{
				$where[] = 't.active >-1 ';
			}
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*) from lib_staff_schedules t '
							. ' left join lib_units lu on t.org_place = lu.id  '
							. $whereQ
			;
			$Return->total = XRedis::getDBCache( 'lib_staff_schedules', $countQuery, 'LoadResult' );
//			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' t.*, '
							. ' lu.ulevel u_level '
							. ' from lib_staff_schedules t '
							. ' left join lib_units lu on t.org_place = lu.id  '
							. $whereQ
							. $order_by
			;
			$Limit_query = 'select * from ( '
							. ' select '
							. ' a.*, '
							. ' rownum rn '
							. ' from (' .
							$Query
							. ') a) where rn > '
							. $Return->start
							. ' and rn <= ' . $Return->limit;
			$Return->items = XRedis::getDBCache( 'lib_staff_schedules', $Limit_query, 'LoadObjectList' );
//			$Return->items = DB::LoadObjectList( $Limit_query );
			foreach ( $Return->items as $Key => $Item )
			{
				if ( $Item->U_LEVEL )
				{
					$Item->TITLE = str_repeat( '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $Item->U_LEVEL ) . '|_ ' . XTranslate::_( $Item->LIB_TITLE );
				}
				else
				{
					$Item->TITLE = XTranslate::_( $Item->LIB_TITLE );
				}
				$Return->items[$Key] = $Item;
			}
			if ( count( $Return->items ) )
			{
				$Return->loaded = 1;
			}
		}
		return $Return;

	}

}
