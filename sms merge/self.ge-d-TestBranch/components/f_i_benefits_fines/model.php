<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class f_i_benefits_finesModel extends Model
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
		$Return->unit = (int) trim( Request::getState( $this->_space, 'unit', '' ) );
		$Return->staff_schedule = (int) Request::getState( $this->_space, 'staff_schedule', 0 );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->period_type = (int) Request::getState( $this->_space, 'period_type', 0 );
		$Return->period_type_code = (int) Request::getState( $this->_space, 'period_type_code', 0 );
		$Return->private_number = Request::getState( $this->_space, 'private_number', '' );
		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();

		if ( $Return->org > 0 )
		{
			if ( $Return->unit > 0 )
			{
				$where[] = ' w.org_place in( '
								. ' select '
								. ' t.id '
								. ' from lib_units t '
								. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . (int) $Return->unit
								. ' where '
								. ' t.active = 1 '
								. ' and u.id is not null )'
				;
			}

			if ( $Return->staff_schedule > 0 )
			{
				$where[] = ' w.staff_schedule = ' . (int) $Return->staff_schedule;
			}

			if ( $Return->position )
			{
				$where[] = ' w.position like ' . DB::Quote( '%' . $Return->position . '%' );
			}

			if ( $Return->period_type > 0 )
			{
				$where[] = ' pp.id = ' . $Return->period_type;
			}

			if ( $Return->period_type_code > 0 )
			{
				$where[] = ' t.period_id = ' . $Return->period_type_code;
			}

			if ( $Return->private_number != '' )
			{
				$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
			}

			$where[] = ' t.org = ' . $Return->org;

			$where[] = ' t.status > -1 ';
			$where[] = ' t.regularity = 2 ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
			$countQuery = 'select count(*) from  slf_worker_benefits t '
							. ' left join hrs_workers_sch w on w.id = t.worker '
							. $whereQ
			;
			$Return->total = DB::LoadResult( $countQuery );
			$Query = 'select '
							. ' t.*, '
							. ' pp.lib_title || \' / \' || to_char(p.p_start, \'yyyy-mm-dd\') || \' - \' || to_char(p.p_end, \'yyyy-mm-dd\') as period, '
							. ' w.staff_schedule, '
							. ' w.org_place, '
							. ' w.position, '
							. ' w.private_number, '
							. ' ca.lib_title category, '
							. ' bt.lib_title benefit, '
							. ' w.firstname, '
							. ' w.lastname '
							. ' from slf_worker_benefits t '
							. ' left join hrs_workers_sch w on w.id = t.worker '
							. ' left join slf_pay_periods p on p.id = t.period_id '
							. ' left join lib_f_accuracy_periods pp on pp.id = p.pid '
							. ' left join lib_f_benefit_types bt on bt.id = t.benefit_id '
							. ' left join lib_f_benefits ca on ca.id = bt.benefit '
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
			$Return->loaded = 1;
		}
		else
		{
			$Return->loaded = 0;
		}

		return $Return;

	}

}
