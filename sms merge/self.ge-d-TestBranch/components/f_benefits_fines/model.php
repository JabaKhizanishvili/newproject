<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class f_benefits_finesModel extends Model
{
	/**
	 *
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->firstname = trim( Request::getState( $this->_space, 'firstname', '' ) );
		$Return->lastname = trim( Request::getState( $this->_space, 'lastname', '' ) );
		$Return->private_number = trim( Request::getState( $this->_space, 'private_number', '' ) );
		$Return->position = trim( Request::getState( $this->_space, 'position', '' ) );
		$Return->accuracy_period = (int) Request::getState( $this->_space, 'accuracy_period', '0' );
		$Return->period_type_code = (int) Request::getState( $this->_space, 'period_type_code', '0' );
		$Return->org = (int) Request::getState( $this->_space, 'org', '0' );
		$Return->unit = (int) Request::getState( $this->_space, 'unit', '0' );
		$Return->staffschedule = (int) Request::getState( $this->_space, 'staffschedule', '0' );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $Return->accuracy_period > 0 )
		{
			$where[] = ' p.pid = ' . $Return->accuracy_period;
		}

		if ( $Return->period_type_code > 0 )
		{
			$where[] = ' t.period_id = ' . $Return->period_type_code;
		}

		if ( $Return->org > 0 )
		{
			$where[] = ' w.org = ' . $Return->org;
		}

		if ( $Return->unit )
		{
			$where[] = ' w.org_place in( '
							. ' select '
							. ' t.id '
							. ' from lib_units t '
							. ' left join lib_units u on u.lft <= t.lft and u.rgt >= t.rgt and u.id = ' . $Return->unit
							. ' where '
							. ' t.active = 1 '
							. ' and u.id is not null )'
			;
		}

		if ( $Return->staffschedule > 0 )
		{
			$where[] = ' w.staff_schedule = ' . $Return->staffschedule;
		}

		if ( $Return->firstname )
		{
			$where[] = ' w.firstname like ' . DB::Quote( '%' . $Return->firstname . '%' );
		}

		if ( $Return->lastname )
		{
			$where[] = ' w.lastname like ' . DB::Quote( '%' . $Return->lastname . '%' );
		}

		if ( $Return->private_number != '' )
		{
			$where[] = ' w.private_number like ' . DB::Quote( '%' . $Return->private_number . '%' );
		}

		if ( $Return->position )
		{
			$where[] = ' w.position like ' . DB::Quote( '%' . $Return->position . '%' );
		}

//		$where[] = 't.status > 0 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from slf_daily_salary t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		
          // ვიცი ვიცი, ეს სელექთი გამოსაცვლელია... და გრიდიც არ უნდა იყოს მასეთი - შედგენილი გრიდი იქნება საჭირო როგორც ნამუშევარ დროშია... ეს თემა გავიაროთ აქამდე რომ მოვალთ.
		
		$Query = 'select '
						. ' \'0\' as worker_share, '
						. ' \'0\' as company_share, '
						. ' \'0\' as cost, '
						. ' \'0\' as pension, '
						. ' \'0\' as income, '
						. ' \'0\' as absolute_cost, '
						. ' max(w.firstname ) firstname,'
						. ' max(pp.lib_title ) periodTitle,'
						. ' max(w.lastname) lastname,'
						. ' max(w.private_number) private_number,'
						. ' max(w.org) org,'
						. ' max(w.position) position,'
						. ' max(w.staff_schedule) staff_schedule,'
						. ' max(st.lib_title) salarytype,'
						. ' max(w.salary) salary,'
						. ' max(to_char( p.p_start, \'yyyy-mm-dd\') || \' \' || to_char( p.p_end, \'yyyy-mm-dd\')) period,'
						. ' (select sum(b.g_net) from slf_daily_salary b where b.worker = t.worker and b.status > 0) g_net, '
						. ' (select sum(b.g_pension_person) from slf_daily_salary b where b.worker = t.worker and b.status > 0) g_pension_person, '
						. ' (select sum(b.g_pension_org) from slf_daily_salary b where b.worker = t.worker and b.status > 0) g_pension_org, '
						. ' (select sum(b.g_income_tax) from slf_daily_salary b where b.worker = t.worker and b.status > 0) g_income_tax '
						. ' from slf_daily_salary t '
						. ' left join slf_pay_periods p on p.id = t.period_id '
						. ' left join lib_F_accuracy_periods pp on pp.id = p.pid '
						. ' left join hrs_workers_sch w on w.id = t.worker '
						. ' left join slf_worker  sw on sw.id = w.id '
						. ' left join lib_f_salary_types  st on st.id = sw.salarytype '
						. ' left join lib_staff_schedules  sc on sc.id = w.staff_schedule '
						. $whereQ
						. ' group by t.worker '
//						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
//		echo '<pre>';
//		print_r($Return);
//		echo '</pre>';
//		die();
		return $Return;

	}

}
