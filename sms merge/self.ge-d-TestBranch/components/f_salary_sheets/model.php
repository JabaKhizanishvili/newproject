<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class f_salary_sheetsModel extends Model
{
	/**
	 *
	 * @return type
	 */
	function getList( $id = 0 )
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->org = (int) Request::getState( $this->_space, 'org', 0 );
		$Return->period_type = (int) Request::getState( $this->_space, 'period_type', 0 );
		$Return->period_type_code = (int) Request::getState( $this->_space, 'period_type_code', 0 );
		$Return->data_type = array_diff( Request::getState( $this->_space, 'data_type', array() ), [ '' ] );
		$Return->category = (int) Request::getState( $this->_space, 'category', 0 );
		$Return->benefit = (int) Request::getState( $this->_space, 'benefit', 0 );
		$Return->status = (int) Request::getState( $this->_space, 'status', -1 );

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;

		$where = array();
		if ( $id > 0 )
		{
			$where[] = ' t.id = ' . (int) $id;
		}

		if ( $Return->org > 0 )
		{
			$where[] = ' t.org = ' . $Return->org;
		}

		if ( $Return->period_type > 0 )
		{
			$where[] = ' a.id = ' . $Return->period_type;
		}

		if ( $Return->period_type_code > 0 )
		{
			$where[] = ' p.id = ' . $Return->period_type_code;
		}

		if ( !empty( $Return->data_type ) )
		{
			$where[] = ' t.data_type in (' . implode( ', ', $Return->data_type ) . ')';
		}

		if ( $Return->category > 0 )
		{
			$where[] = ' t.id in ( select ccc.sheet_id from lib_salary_calculations ccc left join lib_f_benefit_types bbb on bbb.id = ccc.data_id where bbb.benefit = ' . (int) $Return->category . ' group by ccc.sheet_id )';
		}

		if ( $Return->benefit > 0 )
		{
			$where[] = ' t.id in ( select scc.sheet_id from lib_salary_calculations scc where scc.data_id = ' . (int) $Return->benefit . ' group by scc.sheet_id )';
		}

		if ( $Return->status > -1 )
		{
			$where[] = ' t.status = ' . $Return->status;
		}

		$where[] = ' t.active = 1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(*) from lib_f_salary_sheets t '
						. ' left join slf_pay_periods p on p.id = t.period '
						. ' left join lib_f_accuracy_periods a on a.id = p.pid '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );

		$Query = 'select '
						. ' t.*, '
						. ' to_char(t.rec_date, \'yyyy-mm-dd\') record_date, '
						. ' a.lib_title || \' - \' || to_char(p.p_start, \'yyyy-mm-dd\') || \' / \' || to_char(p.p_end, \'yyyy-mm-dd\') period_name '
						. ' from lib_f_salary_sheets t '
						. ' left join slf_pay_periods p on p.id = t.period '
						. ' left join lib_f_accuracy_periods a on a.id = p.pid '
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

	public function SalarySheet( $id )
	{
		$sheet_data = C::_( 0, $this->getList( $id )->items, [] );

		$Return = $this->getReturn();
		$Return->sheet_id = C::_( 'ID', $sheet_data );
		$Return->sheet_name = C::_( 'LIB_TITLE', $sheet_data );

		$where = array();
//		$where[] = '';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$countQuery = 'select count(1) from( '
						. 'select '
						. 'c.worker '
						. 'from '
						. 'lib_salary_calculations c '
						. 'where '
						. 'c.sheet_id = ' . (int) $id
						. 'group by '
						. 'c.worker '
						. ') a'
//						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );

		$Query = ' select'
						. ' w.id, '
						. ' p.firstname || \' \' || p.lastname worker, '
						. ' p.private_number, '
						. ' sc.lib_title staff_schedule, '
						. ' sc.schedule_code, '
						. ' po.lib_title position, '
						. ' u.lib_title unit, '
						. ' u.unit_code, '
						. ' (select '
						. ' max(tt.lib_title) title '
						. ' from lib_units tt '
						. ' left join lib_unittypes ut on ut.id = tt.type '
						. ' left join lib_units uu on uu.lft >= tt.lft and uu.rgt <= tt.rgt'
						. '  where '
						. ' tt.active > 0 '
						. ' and uu.id is not null '
						. ' and ut.def = 1'
						. ' and uu.id = u.id '
						. ' and tt.org = w.org '
						. ') main_unit,'
						. ' w.tablenum, '
						. ' w.p_code worker_code, '
						. ' w.changedate assignment_date, '
						. ' (select min(rpo.assignment_date) from rel_person_org rpo where rpo.id = w.orgpid and rpo.active = 1) company_assignment_date, '
						. ' w.salary,'
						. ' t.salary_net,'
						. ' t.taxable_sum,'
						. ' t.income_tax_sum,'
						. ' t.worker_pension_tax_sum,'
						. ' t.company_pension_tax_sum,'
						. ' (t.worker_pension_tax_sum + t.company_pension_tax_sum) pension_tax_sum,'
						. ' (t.taxable_sum + t.income_tax_sum + t.worker_pension_tax_sum + t.company_pension_tax_sum) full_sum,'
						. ' t.pay_sum '
						. ' from '
						. ' ( '
						. ' select '
						. ' c.worker,'
						. ' (select nvl(cc.net, 0) from lib_salary_calculations cc where cc.worker = c.worker and cc.data_type = 0 and cc.sheet_id = ' . (int) $id . ') salary_net, '
//						. ' c.benefits '
						. ' sum(c.tax_base) taxable_sum, '
						. ' sum(c.real_income_tax) income_tax_sum, '
						. ' sum(c.real_worker_pension) worker_pension_tax_sum, '
						. ' sum(c.real_company_pension) company_pension_tax_sum, '
						. ' sum(c.cash) pay_sum '
						. ' from '
						. ' lib_salary_calculations c '
						. ' where '
						. ' c.sheet_id = ' . (int) $id
						. ' group by '
						. ' c.worker '
						. ' ) t '
						. ' left join slf_worker w on w.id = t.worker '
						. ' left join slf_persons p on p.id = w.person '
						. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
						. ' left join lib_positions po on po.id = sc.position '
						. ' left join lib_units u on u.id = sc.org_place '
						. $whereQ
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
		$categories = [];
		$Return->benefits = $this->load_benefits( $id, $categories );
		$Return->categories = $categories;
		return $Return;

	}

	public function load_fields()
	{
		$q = 'select ca.id, ca.lib_title, ca.fields from lib_f_benefits ca';
		$categories = DB::LoadObjectList( $q, 'ID' );
		$category_fields = [];
		foreach ( $categories as $each )
		{
			$ex = explode( ',', $each->FIELDS );
			$category_fields[$each->ID]['NAME'] = $each->LIB_TITLE;
			$category_fields[$each->ID]['FIELDS'] = $ex;
		}

		return $category_fields;

	}

	public function load_benefits( $sheet_id = 0, &$categories = [] )
	{
		$category_fields = $this->load_fields();
		$query = ' select '
						. ' c.worker, '
						. ' nvl(bn.benefit, 0) category, '
						. ' sum(c.worker_share) worker_share, '
						. ' sum(c.company_share) company_share, '
						. ' sum(c.net) cost, '
						. ' sum(c.worker_pension + c.company_pension) pension, '
						. ' sum(c.worker_income) income, '
						. ' sum(c.net + c.worker_income + c.worker_pension + c.company_pension) absolute_cost '
						. ' from '
						. ' lib_salary_calculations c '
						. ' left join lib_f_benefit_types bn on bn.id = c.data_id '
						. ' where '
						. ' c.sheet_id = ' . (int) $sheet_id
						. ' and c.data_type != 0 '
						. ' group by '
						. ' c.worker, '
						. ' bn.benefit '
		;

		$benefits = DB::LoadObjectList( $query );

		$collect = [];
		$categories = [];
		foreach ( $benefits as $data )
		{
			$category_name = C::_( $data->CATEGORY . '.NAME', $category_fields );
			$fields = C::_( $data->CATEGORY . '.FIELDS', $category_fields );
			$include = [];
			foreach ( $fields as $field )
			{
				$include[$field] = C::_( $field, $data, 0 );
			}
			$collect[$data->WORKER][$data->CATEGORY] = $include;
			if ( !in_array( $data->CATEGORY, $categories ) )
			{
				$categories[$data->CATEGORY]['NAME'] = $category_name;
				$categories[$data->CATEGORY]['FIELDS'] = array_keys( $include );
			}
		}

		return $collect;

	}

}
