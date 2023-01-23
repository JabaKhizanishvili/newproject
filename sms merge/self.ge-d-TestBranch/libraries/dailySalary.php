<?php

class DailySalary
{
	public static $Slf_pay_periods = null;
	public static $Slf_daily_salary = null;
	public static $Salary_calculations = null;

	public static function GetData( $status = 0 )
	{
		$query = 'select s.* from slf_daily_salary s where s.status = ' . (int) $status;
		return DB::LoadObjectList( $query );

	}

	public static function CollectRecordData()
	{
		$m_day = PDate::Get()->toFormat();
		$PeriodData = DB::LoadObject( 'select pa.* from slf_pay_periods pa where '
										. '  to_date(\'' . $m_day . '\', \'yyyy-mm-dd hh24:mi:ss\')  between pa.p_start and pa.p_end '
										. ' ' );
		$Start = PDate::Get( C::_( 'P_START', $PeriodData ) );
		$End = PDate::Get( C::_( 'P_END', $PeriodData ) );
		$Days = Helper::GetDays( $Start->toFormat(), $End->toFormat() );
		$Return = [];
		foreach ( $Days as $Day )
		{
			$Day = PDate::Get( $Day )->toFormat();
			$Date = PDate::Get( $Day )->toFormat( '%Y-%m-%d' );
			$select = 'select '
							. ' w.id worker, '
							. ' w.org, '
							. ' ap.id period_id, '
							. ' s.id salary_type, '
							. ' w.salary_payment_type payment_type, '
							. ' w.salary, '
							. ' s.taxes_yn, '
							. ' pp.pay_pension '
							. ' from slf_worker w '
							. ' left join slf_persons pp on pp.id = w.person '
							. ' left join lib_f_salary_types s on s.id = w.salarytype '
							. ' left join lib_f_accuracy_periods p on p.id = s.accuracy_period '
							. ' left join (select pa.* from slf_pay_periods pa where '
							. '  to_date(\'' . $Day . '\', \'yyyy-mm-dd hh24:mi:ss\')  between trunc(pa.p_start) and trunc(pa.p_end) '
							. ' ) ap on ap.pid = s.accuracy_period '
							. ' where '
							. ' w.salarytype > 0 '
							. ' and s.active = 1 '
							. ' and p.active = 1 '
							. ' and (select count(*) from slf_daily_salary where to_char( rec_date, \'yyyy-mm-dd\') =' . DB::Quote( $Date ) . ' ) = 0  '
			;
			$R = DB::LoadObjectList( $select );
			if ( $R )
			{
				$Return[$Day] = $R;
			}
		}

		return $Return;

	}

	public static function Record()
	{
		$GetData = self::CollectRecordData();
		if ( empty( $GetData ) )
		{
			return false;
		}

		$Table = self::get_table( 'slf_daily_salary' );
		foreach ( $GetData as $DBDate => $GData )
		{

			$Date = PDate::Get( $DBDate )->toFormat();
			foreach ( $GData as $data )
			{
				$Table->resetAll();
				$Table->bind( $data );
				$Table->REC_DATE = $Date;
				$Table->DATA_DAY = PDate::Get( $Date )->toFormat();
				$Table->STATUS = 0;
				$Table = self::Generate_FixedSalary( $data, $Table );
				$Table->store();
			}
		}

		return true;

	}

	public static function CollectPeriods( $type = 0, $next = 1, $period_start = 1, $date_str = '' )
	{
		$date = PDate::Get();
		if ( !empty( $date_str ) )
		{
			$date = PDate::Get( $date_str );
		}

		$Y = (int) $date->toFormat( '%Y' );
		$YY = (int) substr( $Y, 2 );

		$s_time = Helper::getConfig( 'SalarySheet_work_start', '09:00' ) . ':00';
		$hh = (int) explode( ':', $s_time )[0];
		$ii = (int) explode( ':', $s_time )[1];
		$e_time = PDate::Get( PDate::Get( $s_time )->toUnix() - 1 )->toFormat( '%H:%M:%S' );
		$x_day = false;
		if ( $hh == 0 && $ii == 0 )
		{
			$x_day = true;
		}

		switch ( $type )
		{
//			case 1:
//				$week = $date->toFormat( '%W' );
//				$result = [];
//				for ( $i = 1; $i <= $next + 1; ++$i )
//				{
//					$key = $YY . '-' . str_pad( $week, 2, '0', STR_PAD_LEFT );
//					$result[$key]['START'] = explode( ' ', (string) $date->setISODate( $Y, $week, 1 ) )[0] . ' ' . $s_time;
//					$end = explode( ' ', (string) $date->setISODate( $Y, $week + 1, 1 ) )[0] . ' ' . $e_time;
//					$result[$key]['END'] = $x_day ? (string) PDate::Get( $end . ' -1 day' ) : $end;
//					$s = explode( ' ', $date->setISODate( $Y, $week, $period_start ) )[0];
//					$y = (int) date( 'Y', strtotime( $s . ' +1 week' ) );
//					if ( $y != $Y )
//					{
//						$Y++;
//						$YY++;
//						$week = 0;
//					}
//					$week++;
//				}
//				return $result;
//				break;

			case 2:
				$month = (int) $date->toFormat( '%m' );
				$result = [];
				for ( $i = 1;
								$i <= $next + 1;
								++$i )
				{
					if ( $month > 12 )
					{
						$month = 1;
						$YY++;
						$Y++;
					}
					$key = $YY . str_pad( $month, 2, '0', STR_PAD_LEFT );
					$start = $Y . '-' . str_pad( $month, 2, '0', STR_PAD_LEFT ) . '-' . str_pad( $period_start, 2, '0', STR_PAD_LEFT );
					$result[$key]['START'] = $start . ' ' . $s_time;
					$result[$key]['END'] = PDate::Get( $start . ' +1 month -1 day' )->toFormat( '%Y-%m-%d' ) . ' ' . $e_time;
					$month++;
				}
				return $result;
				break;

//			case 3:
//				$m = (int) $date->toFormat( '%m' );
//				$d = (int) $date->toFormat( '%d' );
//				$q = 1;
//				if ( $d > 15 )
//				{
//					$q = 2;
//				}
//				$nn = 0;
//				for ( $i = 1; $nn < $next + 1; ++$i )
//				{
//					$mm = str_pad( $m, 2, '0', STR_PAD_LEFT );
//					$s = $YY . $mm;
//					if ( $q == 1 )
//					{
//						$result[$s . '-' . $q]['START'] = $Y . '-' . $mm . '-01' . ' ' . $s_time;
//						$result[$s . '-' . $q]['END'] = $Y . '-' . $mm . ($x_day ? '-15' : '-16') . ' ' . $e_time;
//						$q = 2;
//						$nn++;
//					}
//					if ( $q == 2 )
//					{
//						$result[$s . '-2']['START'] = $Y . '-' . $mm . '-16' . ' ' . $s_time;
//						$end = date( 'Y-m-t', strtotime( $Y . '-' . $mm . '-16' ) ) . ' ' . $e_time;
//						$result[$s . '-2']['END'] = !$x_day ? (string) PDate::Get( $end . ' +1 day' ) : $end;
//						$q = 1;
//						$nn++;
//					}
//					$m++;
//
//					if ( $m > 12 )
//					{
//						$m = 1;
//						$YY++;
//						$Y++;
//					}
//				}
//				return $result;
//				break;
//
//			case 4:
//				$dt = $date->toFormat( '%Y-%m-%d' );
//				$month = date( "n", strtotime( $dt ) );
//				$Quarter = (int) ceil( $month / 3 );
//				$result = [];
//				for ( $i = 1; $i <= $next + 1; ++$i )
//				{
//					if ( $Quarter > 4 )
//					{
//						$Quarter = 1;
//						$YY++;
//						$Y++;
//					}
//					$set = $YY . str_pad( $Quarter, 2, '0', STR_PAD_LEFT );
//					$result[$set]['START'] = date( 'Y-m-d', strtotime( $Y . '-' . (($Quarter * 3) - 2) . '-1' ) ) . ' ' . $s_time;
//					$end = date( 'Y-m-t', strtotime( $Y . '-' . (($Quarter * 3)) . '-1' ) ) . ' ' . $e_time;
//					$result[$set]['END'] = !$x_day ? (string) PDate::Get( $end . ' +1 day' ) : $end;
//					$Quarter++;
//				}
//				return $result;
//
//			case 5:
//				$result = [];
//				for ( $i = 0; $i < $next + 1; ++$i )
//				{
//					$result[$YY . '00']['START'] = $Y . '-01-01' . ' ' . $s_time;
//					$end = (string) PDate::Get( 'Last day of December ' . $Y . '-01-01' )->toFormat( '%Y-%m-%d' ) . ' ' . $e_time;
//					$result[$YY . '00']['END'] = !$x_day ? (string) PDate::Get( $end . ' +1 day' ) : $end;
//					$YY++;
//					$Y++;
//				}
//				return $result;
//				break;

			default:
				return (string) $date;
				break;
		}

	}

	public static function RegisterPeriods( $pid, $type, $codes )
	{
		if ( !count( $codes ) )
		{
			return false;
		}

		$Table = self::get_table( 'slf_pay_periods' );
		foreach ( $codes as $code => $period )
		{
			$Table->resetAll();
			$Table->PID = $pid;
			$Table->P_TYPE = $type;
			$Table->P_CODE = $code;
			$Table->P_START = C::_( 'START', $period, '' );
			$Table->P_END = C::_( 'END', $period, '' );
			$Table->STATUS = 0;
			$Table->store();
		}
		return true;

	}

	public static function GeneratePeriods()
	{
		$query = 'select '
						. ' p.* '
						. ' from lib_f_accuracy_periods p '
						. ' where '
						. ' p.active = 1'
						. ' and p.id not in (select a.pid from slf_pay_periods a) '
		;
		$result = DB::LoadObjectList( $query );

		if ( !count( $result ) )
		{
			return false;
		}

		foreach ( $result as $data )
		{
			$p_id = C::_( 'ID', $data, 0 );
			$type = C::_( 'PERIOD_TYPE', $data, 0 );
			$next = (int) C::_( 'PERIOD_GENERATOR', $data, 1 );
			$period_start = C::_( 'PERIOD_START', $data, 1 );
			$Codes = self::CollectPeriods( $type, $next, $period_start );
			self::RegisterPeriods( $p_id, $type, $Codes );
		}

		return true;

	}

	public static function UpdatePeriods()
	{
		$query = 'update slf_pay_periods p set '
						. ' p.status = 1 '
						. ' where '
						. ' sysdate > p.p_end '
		;
		DB::Update( $query );

		$select = 'select '
						. ' p.pid, '
						. ' max(p.p_type) type, '
						. ' (max(a.period_generator) + 1) - count(1) next, '
						. ' max(a.period_start) period_start, '
						. ' to_char(add_months(max(p.p_end), 1) + 1, \'yyyy-mm-dd\') start_date '
						. ' from slf_pay_periods p '
						. ' left join lib_f_accuracy_periods a on a.id = p.pid '
						. ' where '
						. ' p.status = 0 '
						. ' and p.p_end > sysdate '
						. ' group by '
						. ' p.pid '
						. ' having '
						. ' (max(a.period_generator) + 1) - count(1) > 0 '
		;
		$result = DB::LoadObjectList( $select, 'PID' );

		if ( empty( $result ) )
		{
			return false;
		}

		foreach ( $result as $data )
		{
			$Codes = self::CollectPeriods( $data->TYPE, $data->NEXT - 1, $data->PERIOD_START, $data->START_DATE );
			self::RegisterPeriods( $data->PID, $data->TYPE, $Codes );
		}

		return true;

	}

	public static function Generate_FixedSalary( $data, $Table )
	{
		// Static Params
		$income_tax = Helper::getConfig( 'salarysheet_income_tax' );
		$pension_tax = Helper::getConfig( 'salarysheet_employee_share' );

		// Dinamic Params
		$taxes_yn = (int) C::_( 'TAXES_YN', $data, -1 );
		$salary = (int) C::_( 'SALARY', $data, 0 );
		$pay_pension = (int) C::_( 'PAY_PENSION', $data, 0 );
		$data_day = C::_( 'DATA_DAY', $Table );
		$days = PDate::Get( $data_day )->__get( 'daysinmonth' );
		// Net Salary
		$G_NET = 0;
		if ( $taxes_yn == 1 )
		{
			$G_NET = ($salary / $days) * (1 - ($income_tax / 100));
		}

		if ( $taxes_yn == 2 )
		{
			if ( $pay_pension == 1 )
			{
				$G_NET = ($salary / $days) * (1 - ($pension_tax / 100)) * (1 - ($income_tax / 100));
			}
			else
			{
				$G_NET = ($salary / $days) * (1 - ($income_tax / 100));
			}
		}

		if ( $taxes_yn == 0 )
		{
			$G_NET = ($salary / $days);
		}

		// Income Tax
		$G_INCOME_TAX = ($G_NET / (1 - ($income_tax / 100))) - $G_NET;

		// Pension Person Part
		$G_PENSION_PERSON = 0;
		if ( $pay_pension == 1 )
		{
			$G_PENSION_PERSON = (($G_NET + $G_INCOME_TAX) / (1 - ($pension_tax / 100))) - ($G_NET + $G_INCOME_TAX);
		}

		// Pension Organization Part 
		$G_PENSION_ORG = $G_PENSION_PERSON;

		// Save Values
		$Table->G_NET = $G_NET;
		$Table->G_INCOME_TAX = $G_INCOME_TAX;
		$Table->G_PENSION_PERSON = $G_PENSION_PERSON;
		$Table->G_PENSION_ORG = $G_PENSION_ORG;
		return $Table;

	}

	public static function get_table( $table )
	{
		switch ( $table )
		{
			case 'slf_pay_periods':
				if ( is_null( self::$Slf_pay_periods ) )
				{
					self::$Slf_pay_periods = new TableSlf_pay_periodsInterface( 'slf_pay_periods', 'ID', 'sqs_pay_periods.nextval' );
				}
				return self::$Slf_pay_periods;
				break;

			case 'slf_daily_salary':
				if ( is_null( self::$Slf_daily_salary ) )
				{
					self::$Slf_daily_salary = new TableSlf_daily_salaryInterface( 'slf_daily_salary', 'ID', 'sqs_daily_salary.nextval' );
				}
				return self::$Slf_daily_salary;
				break;

			case 'lib_salary_calculations':
				if ( is_null( self::$Salary_calculations ) )
				{
					self::$Salary_calculations = new TableLib_salary_calculationsInterface( 'lib_salary_calculations', 'ID', 'sqs_salary_calculations.nextval' );
				}
				return self::$Salary_calculations;
				break;
		}

	}

	public static function getSalary( $period = 0, $org = 0, $gen_hash = '' )
	{
		$query = 'select '
						. ' s.org, '
						. ' s.worker, '
						. ' sum(s.g_net) net, '
						. ' sum(s.g_income_tax) income_tax, '
						. ' min(p.pay_pension) pay_pension '
						. ' from slf_daily_salary s '
						. ' left join slf_worker w on w.id = s.worker '
						. ' left join slf_persons p on p.id = w.person '
						. ' where '
						. ' s.period_id = ' . (int) $period
						. ' and s.org = ' . (int) $org
						. ' and s.status = 0 or s.gen_hash = ' . DB::Quote( $gen_hash )
						. ' group by s.worker, s.org '
		;

		return DB::LoadObjectList( $query, 'WORKER' );

	}

	public static function insert_generated_salary( $sheet_id, $worker, $type, $calculated = [] )
	{
		$Table = self::get_table( 'lib_salary_calculations' );

		$Table->resetAll();
		$load = [];
		$load['SHEET_ID'] = $sheet_id;
		$load['WORKER'] = $worker;
		$load['DATA_TYPE'] = $type;
		$load['DATA_ID'] = C::_( 'DATA_ID', $calculated );

		$Table->loads( $load );
		$Table->SHEET_ID = (int) $sheet_id;
		$Table->WORKER = (int) $worker;
		$Table->DATA_TYPE = (int) $type;
		$Table->bind( $calculated );

		if ( !$Table->store() )
		{
			return false;
		}

		return $Table->insertid();

	}

	public static function getRegullarBenefits( $ids = '', $org = 0, $period = 0, $gen_hash = '' )
	{
		$query = 'select '
						. ' db.* '
						. ' from slf_daily_benefits db where '
						. ' db.org = ' . (int) $org
						. ' and db.period_id = ' . (int) $period
						. ' and db.benefit_id in (' . implode( ', ', $ids ) . ') '
						. ' and db.status = 0 or db.gen_hash = ' . DB::Quote( $gen_hash )
		;

		return DB::LoadObjectList( $query );

	}

	public static function getIregullarBenefits( $ids = '', $org = 0, $period = 0, $worker = 0, $gen_hash = '' )
	{

		if ( empty( $org ) || empty( $period ) )
		{
			return [];
		}

		static $getIregullarBenefits = null;
		if ( is_null( $getIregullarBenefits ) )
		{
			$query = 'select'
							. ' wb.*,'
							. ' p.pay_pension '
							. ' from slf_worker_benefits wb '
							. ' left join slf_worker w on w.id = wb.worker '
							. ' left join slf_persons p on p.id = w.person '
							. ' where '
							. ' wb.org = ' . (int) $org
							. ' and wb.period_id = ' . (int) $period
							. ' and wb.benefit_id in (' . implode( ', ', $ids ) . ') '
							. ' and wb.status = 0 or wb.gen_hash = ' . DB::Quote( $gen_hash )
			;
			$getIregullarBenefits = DB::LoadObjectList( $query, 'WORKER' );
		}

		if ( $worker > 0 )
		{
			return C::_( $worker, $getIregullarBenefits );
		}

		return $getIregullarBenefits;

	}

	public static function CollectType_iRegular( $iregular = null, $income_tax = 0, $pension_tax = 0 )
	{
		$b_type = C::_( 'TYPE', $iregular );
		$gross_type = C::_( 'GROSS_TAX_TYPE', $iregular );
		$pay_pension = C::_( 'PAY_PENSION', $iregular );

		// Params
		$benefit_id = C::_( 'BENEFIT_ID', $iregular );
		$worker_share_type = C::_( 'TYPE', $iregular );
		$real_worker_pension = 0;
		$net = C::_( 'COST', $iregular );
		$worker_share = C::_( 'WORKER_SHARE', $iregular );

		$company_share = C::_( 'COMPANY_SHARE', $iregular );
		$worker_income = C::_( 'ACCRUED_INCOME', $iregular );
		$worker_pension = C::_( 'ACCRUED_PENSION', $iregular );
		$tax_base = 0;
		$real_income_tax = 0;
		if ( $b_type == 1 )
		{
			$worker_share = (int) C::_( 'WORKER_SHARE', $iregular );
		}
		if ( $b_type == 2 )
		{
			$worker_share = ( (int) C::_( 'WORKER_SHARE', $iregular ) ) * -1;
		}

		$NotPenalty = (bool) ( $worker_income + $worker_pension ) ? 1 : 0;

		if ( $NotPenalty == 1 )
		{
			if ( $worker_share > 0 || $company_share > 0 )
			{
				$tax_base = $worker_share + $company_share;
			}

			if ( in_array( $gross_type, [ 1, 2 ] ) )
			{
				$real_income_tax = $tax_base / ((100 - $income_tax) / 100) - $tax_base;
			}
		}
		if ( $NotPenalty == 0 )
		{
			$tax_base = $worker_share + $company_share;
			$real_income_tax = $tax_base / ((100 - $income_tax) / 100) - $tax_base;
		}

		if ( $gross_type == 1 && $pay_pension == 1 )
		{
			$real_worker_pension = Helper::FormatBalance( ($tax_base + $worker_income) / ((100 - $pension_tax) / 100) - ($tax_base + $worker_income) );
		}

		$real_company_pension = $real_worker_pension;
		$cash = $tax_base;

		$calculated = [];
		$calculated['DATA_ID'] = $benefit_id;
		$calculated['NET'] = $net;
		$calculated['WORKER_SHARE'] = $worker_share;
		$calculated['COMPANY_SHARE'] = $company_share;
		$calculated['WORKER_SHARE_TYPE'] = $worker_share_type;
		$calculated['WORKER_INCOME'] = $worker_income;
		$calculated['WORKER_PENSION'] = $worker_pension;
		$calculated['COMPANY_PENSION'] = $worker_pension;
		$calculated['NOT_PENALTY'] = $NotPenalty;
		$calculated['TAX_BASE'] = $tax_base;
		$calculated['REAL_WORKER_PENSION'] = $real_worker_pension;
		$calculated['REAL_COMPANY_PENSION'] = $real_company_pension;
		$calculated['REAL_INCOME_TAX'] = $real_income_tax;
		$calculated['CASH'] = $cash;

		return $calculated;

	}

	public static function CollectType_Regular( $regular = null, $benefits_in_period = null, $records = null, $income_tax = 0, $pension_tax = 0, $worker = 0, $period = 0, $edit = false )
	{
		$benefit_data = Benefits::get_benefit_types( $regular );
		$b_type = C::_( 'TYPE', $benefit_data );
		$gross_type = C::_( 'GROSS_TAX_TYPE', $benefit_data );
		$pay_pension = C::_( 'PAY_PENSION', $records );
		// Params
		$benefit_id = $regular;
		$worker_share_type = $b_type;
		$real_worker_pension = 0;
		$net = 0;
		$real_income_tax = 0;
		$worker_share = $edit ? C::_( 'WORKER_SHARE', $records ) : 0;
		$company_share = $edit ? C::_( 'COMPANY_SHARE', $records ) : 0;
		$worker_income = 0;
		$worker_pension = 0;
		$tax_base = 0;

		// Calculations
		$Cost = C::_( (int) $worker . '|' . (int) $period . '|' . (int) $regular . '.COST', $benefits_in_period );
		if ( $Cost )
		{
			$net = $Cost;
		}

		if ( in_array( $gross_type, [ 1, 2 ] ) )
		{
			$c_income = $net / ((100 - $income_tax) / 100 - $net);
			$worker_income = $c_income > 0 ? $c_income : 0;
		}

		if ( $gross_type == 1 && $pay_pension == 1 )
		{
			$c_pension = ($net + $worker_income) * ($pension_tax / 100);
			$worker_pension = $c_pension > 0 ? $c_pension : 0;
		}

		$worker_share_by = (int) C::_( 'C_WORKER_SHARE', $benefit_data );
		if ( $b_type == 1 && !$edit )
		{
			$worker_share = $worker_share_by;
		}

		if ( $b_type == 2 )
		{
			$worker_share = ( $edit ? $worker_share : $worker_share_by ) * -1;
		}

		$company_pension = $worker_pension;

		$not_penalty = !(bool) ( $worker_income + $worker_pension ) ? 1 : 0;

		if ( $not_penalty == 1 )
		{
			if ( $worker_share > 0 && $company_share > 0 )
			{
				$tax_base = $worker_share + $company_share;
			}

			if ( in_array( $gross_type, [ 1, 2 ] ) )
			{
				$real_income_tax = $tax_base / ((100 - $income_tax) / 100) - $tax_base;
			}
		}

		if ( $not_penalty == 0 )
		{
			$tax_base = $worker_share + $company_share;
			$real_income_tax = $tax_base / ((100 - $income_tax) / 100) - $tax_base;
		}

		if ( $gross_type == 1 && $pay_pension == 1 )
		{
			$real_worker_pension = ($tax_base + $worker_income) / ((100 - $pension_tax) / 100) - ($tax_base + $worker_income);
		}

		$real_company_pension = $real_worker_pension;
		$cash = $tax_base;

		$calculated = [];
		$calculated['DATA_ID'] = $benefit_id;
		$calculated['NET'] = $net;
		$calculated['WORKER_SHARE'] = $worker_share;
		$calculated['COMPANY_SHARE'] = $company_share;
		$calculated['WORKER_SHARE_TYPE'] = $worker_share_type;
		$calculated['WORKER_INCOME'] = $worker_income;
		$calculated['WORKER_PENSION'] = $worker_pension;
		$calculated['COMPANY_PENSION'] = $company_pension;
		$calculated['NOT_PENALTY'] = $not_penalty;
		$calculated['TAX_BASE'] = $tax_base;
		$calculated['REAL_WORKER_PENSION'] = $real_worker_pension;
		$calculated['REAL_COMPANY_PENSION'] = $real_company_pension;
		$calculated['REAL_INCOME_TAX'] = $real_income_tax;
		$calculated['CASH'] = $cash;

		return $calculated;

	}

	public static function CollectType_Salary( $records = null, $income_tax = 0, $pension_tax = 0, $edit = false )
	{
		echo '<pre><pre>';
		print_r( $records );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";

		// Params
		$benefit_id = 0;
		$net = C::_( 'NET', $records );
		$pay_pension = C::_( 'PAY_PENSION', $records );
		$worker_pension = 0;
		$company_share = $edit ? C::_( 'COMPANY_SHARE', $records ) : 0;
		$tax_base = 0;
		$real_worker_pension = 0;
		$real_income_tax = 0;
		$worker_share_type = 0;

		// Calculations
		$c_income = ($net / ((100 - $income_tax) / 100)) - $net;
		$worker_income = $c_income > 0 ? $c_income : 0;
		$worker_share = $net;
		$Gross = $worker_share + $worker_income;
		if ( $pay_pension == 1 )
		{
			$c_pension = Helper::FormatBalance( $Gross / ((100 - $pension_tax) / 100) - $Gross );
			$worker_pension = $c_pension > 0 ? $c_pension : 0;
		}
		$company_pension = $worker_pension;
		$not_penalty = (bool) ( $worker_income + $worker_pension ) ? 1 : 0;

		if ( $not_penalty == 1 )
		{

			$tax_base = $worker_share + $company_share;
			$real_income_tax = Helper::FormatBalance( $tax_base / ((100 - $income_tax) / 100) - $tax_base );
		}
		if ( $not_penalty == 1 )
		{
			$tax_base = $worker_share + $company_share;
			$real_income_tax = Helper::FormatBalance( $tax_base / ((100 - $income_tax) / 100) - $tax_base );
		}
		if ( $not_penalty == 1 && $pay_pension == 1 )
		{
			$real_worker_pension = Helper::FormatBalance( ($tax_base + $worker_income) / ((100 - $pension_tax) / 100) - ($tax_base + $worker_income) );
		}
		$real_company_pension = $real_worker_pension;
		$cash = $tax_base;

		$calculated = [];
		$calculated['DATA_ID'] = $benefit_id;
		$calculated['NET'] = $net;
		$calculated['WORKER_SHARE'] = $worker_share;
		$calculated['COMPANY_SHARE'] = $company_share;
		$calculated['WORKER_SHARE_TYPE'] = $worker_share_type;
		$calculated['WORKER_INCOME'] = $worker_income;
		$calculated['WORKER_PENSION'] = $worker_pension;
		$calculated['COMPANY_PENSION'] = $company_pension;
		$calculated['NOT_PENALTY'] = $not_penalty;
		$calculated['TAX_BASE'] = $tax_base;
		$calculated['REAL_WORKER_PENSION'] = $real_worker_pension;
		$calculated['REAL_COMPANY_PENSION'] = $real_company_pension;
		$calculated['REAL_INCOME_TAX'] = $real_income_tax;
		$calculated['CASH'] = $cash;
		return $calculated;

	}

	public static function GenerateSalary( $sheet_id = 0, $data_types = [], $regulars = [], $iregulars = [], $period = 0, $org = 0, $gen_hash = '' )
	{
		$income_tax = Helper::getConfig( 'salarysheet_income_tax' );
		$pension_tax = Helper::getConfig( 'salarysheet_employee_share' );

		$Benefits_in_period = DB::LoadObjectList( 'select db.cost, db.worker ||\'|\'|| db.PERIOD_ID ||\'|\'|| db.BENEFIT_ID WPB from slf_daily_benefits db', 'WPB' );
		$done = [];
		foreach ( $data_types as $type )
		{
			if ( $type == 0 )
			{
				$getSalary = self::getSalary( $period, $org, $gen_hash );
				foreach ( $getSalary as $worker => $records )
				{
					$calculated = self::CollectType_Salary( $records, $income_tax, $pension_tax );
					$done[] = self::insert_generated_salary( $sheet_id, $worker, $type, $calculated );
				}
			}
			elseif ( $type == 1 )
			{
				$getRegulars = self::getRegullarBenefits( $regulars, $org, $period, $gen_hash );
				foreach ( $getRegulars as $regular )
				{
					$worker = C::_( 'WORKER', $regular );
					$benefit_id = C::_( 'BENEFIT_ID', $regular );
					$calculated = self::CollectType_Regular( $benefit_id, $Benefits_in_period, $regular, $income_tax, $pension_tax );
					$done[] = self::insert_generated_salary( $sheet_id, $worker, $type, $calculated );
				}
			}
			elseif ( $type == 2 )
			{
				$getIregulars = self::getIregullarBenefits( $iregulars, $org, $period, 0, $gen_hash );
				foreach ( $getIregulars as $iregular )
				{
					$worker = C::_( 'WORKER', $iregular );
					$calculated = self::CollectType_iRegular( $iregular, $income_tax, $pension_tax );
					$done[] = self::insert_generated_salary( $sheet_id, $worker, $type, $calculated );
				}
			}
		}

		if ( empty( $done ) )
		{
			return false;
		}

		return $done;

	}

}
