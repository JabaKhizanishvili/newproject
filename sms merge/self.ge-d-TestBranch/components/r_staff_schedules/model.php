<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class R_staff_schedulesModel extends Model
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
		$Return->ulevel = (int) trim( Request::getState( $this->_space, 'ulevel', '' ) );
		$Return->org = (int) trim( Request::getState( $this->_space, 'org', '1' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$Return->org_place = (int) trim( Request::getState( $this->_space, 'org_place', '0' ) );
		$Return->staffschedule = trim( Request::getState( $this->_space, 'staffschedule', '' ) );
		$Return->start_date = trim( Request::getState( $this->_space, 'start_date', '0' ) );
		$where = array();
		$Return->loaded = 0;
		if ( $Return->org > 0 )
		{
			$s_date = !empty( $Return->start_date ) ? $Return->start_date : '';
			$start_date = PDate::Get( $s_date )->toFormat( '%Y-%m-%d' );

			$where[] = 't.org = ' . DB::Quote( $Return->org );
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
			if ( $Return->staffschedule )
			{
				$where[] = ' t.id in (' . $this->_search( $Return->staffschedule, [ 'lib_title' ], 'lib_staff_schedules' ) . ')';
			}
			if ( $Return->ulevel > 0 )
			{
				$where[] = ' t.ulevel =  ' . $Return->ulevel;
			}

			$where[] = ' t.active=1 ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*) from lib_staff_schedules t '
							. ' left join lib_units lu on t.org_place = lu.id  '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' t.*, '
//							. ' (SELECT '
//							. ' max(tt.lib_title) title '
//							. ' from LIB_UNITS tt '
//							. ' left join lib_unittypes ut on ut.id = tt.type '
//							. ' left join lib_units u on u.lft >= tt.lft and u.rgt <= tt.rgt'
//							. '  where '
//							. ' tt.active > 0 '
//							. ' and u.id is not null '
//							. ' and ut.def = 1'
//							. ' and u.id = t.org_place '
//							. ' and tt.org = t.org '
//							. ') MAINUNIT,'
							. ' lu.ulevel u_level,'
							. ' nvl(cc.c, 0) s_count, '
							. ' nvl(cc.real_salary, 0) real_salary, '
							. ' (t.quantity * t.salary) sumsalary, '
							. ' round(nvl(cc.real_salary, 0) / nvl(cc.c, 1), 2) mid_salary '
							. ' from lib_staff_schedules t '
							. ' left join lib_units lu on t.org_place = lu.id  '
							. ' left join (select  swh.staff_schedule , count(1) as c from slf_worker_hist swh where '
							. ' swh.active = 1 '
							. ' and to_date(' . DB::Quote( $start_date ) . ', \'yyyy-mm-dd\') between trunc(swh.hist_start_date) and nvl(trunc(swh.hist_end_date) - 1/24/60/60, to_date(\'2050-01-01\', \'yyyy-mm-dd\')) '
							. ' group by swh.staff_schedule ) cc on cc.staff_schedule = t.id '
							. ' left join (select  swh.staff_schedule , sum(swh.salary) as real_salary from slf_worker_hist swh where '
							. ' swh.active = 1 '
							. ' and to_date(' . DB::Quote( $start_date ) . ', \'yyyy-mm-dd\') between trunc(swh.hist_start_date) and nvl(trunc(swh.hist_end_date) - 1/24/60/60, to_date(\'2050-01-01\', \'yyyy-mm-dd\')) '
							. ' group by swh.staff_schedule ) cc on cc.staff_schedule = t.id '
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

			foreach ( $Return->items as $key => $Item )
			{
				if ( $Item->U_LEVEL )
				{
					$Item->TITLE = str_repeat( '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $Item->U_LEVEL ) . '|_ ' . XTranslate::_( $Item->LIB_TITLE );
				}
				else
				{
					$Item->TITLE = XTranslate::_( $Item->LIB_TITLE );
				}
				$Return->items[$key] = $Item;
			}
			if ( count( $Return->items ) )
			{
				$Return->loaded = 1;
			}
		}
		return $Return;

	}

}
