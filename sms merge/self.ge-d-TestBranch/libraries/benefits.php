<?php

class Benefits
{
	protected static $Table = null;
	protected static $ChangesTable = null;
	protected static $BenefitBinding = null;

	public static function Record()
	{
		$prev_Date = PDate::Get( ' -1 day' )->toFormat( '%Y-%m-%d' );
		$generation_data = self::get_generation_data( $prev_Date );

		if ( empty( $generation_data ) )
		{
			return false;
		}

		$Table = new TableSlf_daily_benefitsInterface( 'slf_daily_benefits', 'ID', 'sqs_daily_benefits.nextval' );
		foreach ( $generation_data as $data )
		{
			$b_type = C::_( 'TYPE', $data );
			$cost = (int) C::_( 'COST', $data );
			$share_type = C::_( 'SHARE_TYPE', $data );
			$gross_tax_type = C::_( 'GROSS_TAX_TYPE', $data );
			$worker_share = (int) C::_( 'WORKER_SHARE', $data );
			$company_share = (int) C::_( 'COMPANY_SHARE', $data );
			$pay_pension = C::_( 'PAY_PENSION', $data, '' );

			$Table->resetAll();
			$Table->bind( $data );
			$Table->ID = '';
			$Table->STATUS = 0;
			$Table->REC_DATE = PDate::Get()->toFormat();
			$Table->CALC_DATE = $prev_Date;

			if ( $b_type == 2 )
			{
				if ( $share_type == 1 )
				{
					$Table->WORKER_MINUS = $cost * ($worker_share / 100);
				}

				if ( $share_type == 2 )
				{
					$Table->WORKER_MINUS = $worker_share;
				}
			}

			if ( $b_type == 1 )
			{
				if ( $share_type == 1 )
				{
					$Table->WORKER_PLUS = $cost * ($worker_share / 100);
				}

				if ( $share_type == 2 )
				{
					$Table->WORKER_PLUS = $worker_share;
				}
			}

			if ( $share_type == 1 )
			{
				$Table->COMPANY_PLUS = $cost * ($company_share / 100);
			}

			if ( $share_type == 2 )
			{
				$Table->COMPANY_PLUS = $company_share;
			}

			if ( $gross_tax_type == 1 || $gross_tax_type == 2 )
			{
				$Table->ACCRUED_INCOME = ($cost / 0.8) - $cost;
			}

			if ( $gross_tax_type == 1 && $pay_pension == 1 )
			{
				$Table->ACCRUED_PENSION = ($cost / 0.8 / 0.98) - $cost - $Table->ACCRUED_INCOME;
			}

			$Table->store();
		}

		return true;

	}

	public static function insert_worker_benefits( $org = null, $period = [] )
	{
		echo '<strong>დოკუმენტაცია მინდა ამაზე, გასავლელია რაღაცეები.</strong><br><br>';
		die;

		$Q = 'select '
						. ' * '
						. ' from slf_daily_salary s '
						. ' left join slf_worker w on w.id = s.worker '
//						. ' left join slf_pay_periods p on p.id = s.period_id '
						. ' where '
						. ' w.org = ' . (int) $org
						. ' and s.period_id in (' . implode( ',', $period ) . ')'
		;
		$result = DB::LoadObjectList( $Q );
		$collect = [];
		foreach ( $result as $value )
		{
			$collect[$value->ID] = $value;
		}
		return true;

	}

	public static function get_generation_data( $prev_Date = null )
	{
		$query = ' select '
						. ' b.id bind_id, '
						. ' b.worker, '
						. ' b.org, '
						. ' t.id benefit_id, '
						. ' ( select pa.id from slf_pay_periods pa where sysdate between pa.p_start and pa.p_end ) period_id,'
						. ' p.pay_pension, '
						. ' t.* '
						. ' from '
						. ' lib_benefit_binding b '
						. ' left join lib_f_benefit_types t on t.id = b.benefit '
						. ' left join slf_worker w on w.id = b.worker '
						. ' left join slf_persons p on p.id = w.person '
						. ' where '
						. ' b.active = 1 '
						. ' and b.worker not in (select worker from slf_daily_benefits where to_char(to_date(\'' . $prev_Date . '\', \'yyyy-mm-dd\'), \'yyyy-mm-dd\') = to_char(calc_date, \'yyyy-mm-dd\') ) '
		;

		return DB::LoadObjectList( $query, 'BIND_ID' );

	}

	public static function get_benefit_types( $id = 0 )
	{
		$get_benefit_types = null;
		if ( is_null( $get_benefit_types ) )
		{
			$Q = 'select '
							. ' bff.* '
							. ' from lib_f_benefit_types bff '
							. ' where '
							. ' bff.active > 0 ';
			;
			$get_benefit_types = DB::LoadObjectList( $Q, 'ID' );
		}

		if ( $id > 0 )
		{
			return C::_( $id, $get_benefit_types, [] );
		}

		return $get_benefit_types;

	}

	public static function set_tables()
	{
		if ( is_null( self::$Table ) )
		{
			self::$Table = new TableSlf_workerInterface( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );
		}

		if ( is_null( self::$ChangesTable ) )
		{
			self::$ChangesTable = new TableSlf_changesInterface( 'slf_changes', 'ID', 'sqs_slf_change.nextval' );
		}

		if ( is_null( self::$BenefitBinding ) )
		{
			self::$BenefitBinding = new TableLib_benefit_bindingInterface( 'lib_benefit_binding', 'ID', 'sqs_benefit_binding.nextval' );
		}

	}

	public static function register_benefits( $data = null )
	{
		if ( empty( $data ) )
		{
			return false;
		}

		$workers = C::_( 'WORKERS', $data );
		if ( empty( $workers ) )
		{
			return false;
		}

		$sub_type = C::_( 'CHANGE_SUB_TYPE', $data );
		$benefit_types = C::_( 'BENEFIT_TYPES', $data, [] );

		self::set_tables();
		foreach ( $benefit_types as $worker => $binded_data )
		{
			foreach ( $binded_data as $each )
			{
				if ( isset( $each['ACTIVE_BENEFIT'] ) )
				{
					unset( $each['ACTIVE_BENEFIT'] );
				}

				$benefit = (int) C::_( 'BENEFIT', $each );
				$change_date = C::_( 'CHANGE_DATE', $each );
				if ( $benefit < 0 )
				{
					continue;
				}

				if ( $sub_type == 3 && empty( $change_date ) )
				{
					continue;
				}

				$change_date = PDate::Get( C::_( 'CHANGE_DATE', $each ) )->toFormat( '%Y-%m-%d' );
				$next_date = Job::next_change_date( $worker, $change_date );
				if ( $next_date )
				{
					$change_date = $next_date;
				}

				$each['WORKER'] = (int) $worker;
				$binding = json_encode( $each );
				self::register_benefit_operation( $worker, $sub_type, $binding, $change_date );
			}
		}

		return true;

	}

	public static function register_benefit_operation( $worker = 0, $sub_type = 0, $benefit_types = '', $change_date = null )
	{
		self::$Table->resetAll();
		self::$Table->load( $worker );
		self::$ChangesTable->bind( self::$Table );

		self::$ChangesTable->ID = '';
		self::$ChangesTable->WORKER_ID = $worker;
		self::$ChangesTable->STATUS = 0;
		self::$ChangesTable->CHANGE_TYPE = 4;
		self::$ChangesTable->BENEFIT_TYPES = $benefit_types;
		self::$ChangesTable->CHANGE_SUB_TYPE = $sub_type;
		self::$ChangesTable->CHANGE_DATE = $change_date;
		self::$ChangesTable->CREATE_DATE = PDate::Get()->toFormat();
		self::$ChangesTable->CREATOR_PERSON = Users::GetUserID();

		if ( !self::$ChangesTable->store() )
		{
			return false;
		}

		return true;

	}

	public static function register_binding( $binded_data = [], $w_data = null )
	{
		if ( empty( $binded_data ) || empty( $w_data ) )
		{
			return false;
		}

		self::set_tables();
		$e = (array) explode( '|', C::_( 'BENEFIT', $binded_data ) );
		$idd = (int) C::_( 'ID', $binded_data );
		$worker = (int) C::_( 'WORKER', $binded_data );
		$category = (int) C::_( 0, $e );
		$benefit = (int) C::_( 1, $e );
		$start_date = C::_( 'CHANGE_DATE', $binded_data, '' );
		if ( !empty( $start_date ) )
		{
			$start_date = PDate::Get( $start_date )->toFormat();
		}

		$end_date = C::_( 'BENEFIT_END_DATE', $binded_data, '' );
		if ( !empty( $end_date ) )
		{
			$start_date = PDate::Get( $start_date )->toFormat();
		}

		self::$BenefitBinding->resetAll();
		self::$BenefitBinding->WORKER = $worker;
		self::$BenefitBinding->PERSON = (int) C::_( 'PERSON', $w_data );
		self::$BenefitBinding->ORGPID = (int) C::_( 'ORGPID', $w_data );
		self::$BenefitBinding->ORG = (int) C::_( 'ORG', $w_data );
		self::$BenefitBinding->BENEFIT = $benefit;
		self::$BenefitBinding->IDENTIFIER = C::_( 'IDENTIFIER', $binded_data, '' );
		self::$BenefitBinding->START_DATE = $start_date;
		self::$BenefitBinding->END_DATE = $end_date;
		self::$BenefitBinding->ACTIVE = 1;

		if ( !self::$BenefitBinding->store() )
		{
			return false;
		}
		$benefit_bind = self::$BenefitBinding->insertid();

		if ( $idd > 0 )
		{
			$update = 'update lib_benefit_binding t set t.active = 0 where t.id = ' . $idd;
			DB::Update( $update );
		}

		$out = [
				'BIND_ID' => $benefit_bind,
				'BENEFIT' => $benefit
		];

		return $out;

	}

	public static function delete_benefits( $data )
	{
		$idd = (int) C::_( 'ID', $data );
		$update = 'update lib_benefit_binding t set t.active = 0 where t.id = ' . $idd;
		if ( !DB::Update( $update ) )
		{
			return false;
		}

		return true;

	}

	public static function load_benefits( $workers )
	{
		$query = 'select '
						. ' r.*, '
						. ' to_char(r.start_date, \'dd-mm-yyyy\') start_date, '
						. ' to_char(r.end_date, \'dd-mm-yyyy\') end_date, '
						. ' t.benefit category '
						. ' from lib_benefit_binding r '
						. ' left join lib_f_benefit_types t on t.id = r.benefit '
						. ' where '
						. ' r.worker in (' . $workers . ') '
						. ' and r.active = 1 '
//						. ' and (select count(1) from lib_f_benefits bb left join lib_f_benefit_types tt on tt.benefit = bb.id where bb.id = t.benefit) > 1 '
		;
		$result = DB::LoadObjectList( $query );
		$collect = [];
		foreach ( $result as $data )
		{
			$rand = substr( str_shuffle( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 5 );
			$collect[$data->WORKER][$rand]['ID'] = $data->ID;
			$collect[$data->WORKER][$rand]['BENEFIT'] = $data->CATEGORY . '|' . $data->BENEFIT;
			$collect[$data->WORKER][$rand]['IDENTIFIER'] = $data->IDENTIFIER;
			$collect[$data->WORKER][$rand]['CHANGE_DATE'] = $data->START_DATE;
			$collect[$data->WORKER][$rand]['BENEFIT_END_DATE'] = $data->END_DATE;
		}

		return $collect;

	}

	public static function check_workers( $workers, $action )
	{
		if ( $action == 1 )
		{
			$query = 'select '
							. ' r.worker '
							. ' from lib_benefit_binding r '
							. ' where '
							. ' r.active = 1 '
			;
			$result = DB::LoadList( $query );
			$ex = Helper::CleanArray( explode( ',', $workers ) );
			return implode( ',', array_diff( $ex, $result ) );
		}

		return $workers;

	}

}
