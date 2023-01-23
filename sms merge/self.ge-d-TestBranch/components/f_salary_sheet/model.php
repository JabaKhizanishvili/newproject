<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class f_salary_sheetModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new f_salary_sheetTable( );
		parent::__construct( $params );

	}

	public function getItem( $ID = 0 )
	{
		$re = Request::getVar( 'nid', array() );
		$id = isset( $re[0] ) && !empty( $re[0] ) ? $re[0] : $ID;
		if ( $id > 0 )
		{
			$this->Table->load( $id );
			$this->Table->DATA_TYPE = $this->loadDataTypes( $this->Table );
		}

		return $this->Table;

	}

	public function loadDataTypes( $data = null )
	{
		$collect = [];
		$ex = explode( '|', C::_( 'DATA_TYPE', $data ) );
		foreach ( $ex as $type )
		{
			if ( $type == 1 )
			{
				$collect[$type] = C::_( 'REGULAR_BENEFIT', $data );
			}
			elseif ( $type == 2 )
			{
				$collect[$type] = C::_( 'IREGULAR_BENEFIT', $data );
			}
			else
			{
				$collect[$type] = $type;
			}
		}

		return json_encode( (object) $collect );

	}

	public function load_each( $worker = 0, $sheet_id = 0 )
	{
		$query = 'select '
						. ' c.*, '
						. ' b.lib_title benefit_name,'
						. ' p.pay_pension '
						. ' from lib_salary_calculations c '
						. ' left join lib_f_benefit_types b on b.id = c.data_id '
						. ' left join slf_worker w on w.id = c.worker '
						. ' left join slf_persons p on p.id = w.person '
						. ' where '
						. ' c.worker = ' . (int) $worker
						. ' and c.sheet_id = ' . (int) $sheet_id
						. ' and c.data_type in (0, 1) '
		;
		$result = DB::LoadObjectList( $query, 'ID' );
		$collect = [];
		foreach ( $result as $id => $data )
		{
			$collect[$data->DATA_TYPE][$id] = $data;
		}
		ksort( $collect );
		return $collect;

	}

	public function Edit( $data )
	{
		$pay_pension = C::_( 'PAY_PENSION', $data );
		$sheet_id = C::_( 'SHEET_ID', $data );
		$worker = C::_( 'WORKER', $data );
		$edit = C::_( 'EDIT', $data );

		if ( empty( $sheet_id ) || empty( $worker ) || empty( $edit ) )
		{
			return false;
		}

		$done = [];
		$income_tax = Helper::getConfig( 'salarysheet_income_tax' );
		$pension_tax = Helper::getConfig( 'salarysheet_employee_share' );
		$Benefits_in_period = DB::LoadObjectList( 'select db.cost, db.worker ||\'|\'|| db.PERIOD_ID ||\'|\'|| db.BENEFIT_ID WPB from slf_daily_benefits db', 'WPB' );
		foreach ( $edit as $data )
		{
			$regular = C::_( 'DATA_ID', $data );
			$worker_share = C::_( 'WORKER_SHARE', $data );
			$company_share = C::_( 'COMPANY_SHARE', $data );

			$records = [];
			$records['NET'] = $worker_share + $company_share;
			$records['PAY_PENSION'] = $pay_pension;
			$records['WORKER_SHARE'] = $worker_share;
			$records['COMPANY_SHARE'] = $company_share;

			$type = C::_( 'TYPE', $data );
			if ( $type == 0 )
			{
				$calculated = DailySalary::CollectType_Salary( (object) $records, $income_tax, $pension_tax, true );
				$done[] = DailySalary::insert_generated_salary( $sheet_id, $worker, $type, $calculated );
			}
			elseif ( $type == 1 )
			{
				$calculated = DailySalary::CollectType_Regular( $regular, $Benefits_in_period, (object) $records, $income_tax, $pension_tax, 0, 0, true );
				$done[] = DailySalary::insert_generated_salary( $sheet_id, $worker, $type, $calculated );
			}
		}

		if ( count( $done ) > 0 )
		{
			return true;
		}

		return false;

	}

	public function SaveData( $data )
	{
		$data['GENERATION_TYPE'] = 0;
		$id = C::_( 'ID', $data, 0 );
		$gen_hash = '-';
		if ( $id )
		{
			$this->Table->load( $id );
			$gen_hash = $this->Table->GEN_HASH;
		}
		$this->Table->bind( $data );
		if ( !$this->Table->check() )
		{
			return false;
		}

		$query = 'select '
						. ' to_char(s.p_start, \'yyyy\') year '
						. ' from slf_pay_periods s where '
						. ' s.id = ' . (int) C::_( 'PERIOD', $data );

		$year = (int) DB::LoadResult( $query );

		if ( empty( $id ) )
		{
			$this->Table->REC_DATE = PDate::Get()->toFormat();
			$this->Table->REC_USER = Users::GetUserID();
		}

		$this->Table->STATUS = 0;
		$this->Table->ACTIVE = 1;
		$this->Table->YEAR = $year;

		$period = $this->Table->PERIOD;
		$org = $this->Table->ORG;
		$data_types = explode( '|', $this->Table->DATA_TYPE );
		$regulars = explode( '|', $this->Table->REGULAR_BENEFIT );
		$iregulars = explode( '|', $this->Table->IREGULAR_BENEFIT );
		$done = DailySalary::GenerateSalary( (int) $id, $data_types, $regulars, $iregulars, $period, $org, $gen_hash );

		if ( empty( $done ) )
		{
			XError::setError( 'data not collected!' );
			return false;
		}

		if ( !$this->Table->store() )
		{
			return false;
		}

		$sheet_id = $this->Table->insertid();
		if ( empty( $gen_hash ) )
		{
			$gen_hash = base64_encode( $sheet_id . random_bytes( 40 ) );
		}
		else
		{
			$update = ' Begin ';
			$update .= ' update slf_daily_salary ds set ds.status = 0, ds.gen_hash = \'\', ds.sheet_id = \'\' where ds.gen_hash = ' . DB::Quote( $gen_hash ) . '; ';
			$update .= ' update slf_daily_benefits db set db.status = 0, db.gen_hash = \'\', db.sheet_id = \'\' where db.gen_hash = ' . DB::Quote( $gen_hash ) . '; ';
			$update .= ' update slf_worker_benefits wb set wb.status = 0, wb.gen_hash = \'\', wb.sheet_id = \'\' where wb.gen_hash = ' . DB::Quote( $gen_hash ) . '; ';
			$update .= ' end; ';

			DB::Update( $update );
		}

		if ( !$this->Core_Update( $data_types, $gen_hash, $sheet_id, $period, $org, $done ) )
		{
			return false;
		}

		return true;

	}

	public function Core_Update( $data_types = [], $gen_hash = '', $sheet_id = 0, $period = 0, $org = 0, $ids = [] )
	{
		$update = ' Begin ';
		$update .= in_array( 0, $data_types ) ? ' update slf_daily_salary ss set '
						. ' ss.status = 1, '
						. ' ss.sheet_id = ' . (int) $sheet_id . ', '
						. ' ss.gen_hash = ' . DB::Quote( $gen_hash )
						. ' where '
						. ' ss.period_id  = ' . (int) $period
						. ' and ss.org = ' . (int) $org
						. ' and ss.status = 0 or ss.gen_hash = ' . DB::Quote( $gen_hash )
						. '; ' : ''
		;

		$update .= in_array( 1, $data_types ) ? ' update slf_daily_benefits db set '
						. ' db.status = 1, '
						. ' db.sheet_id = ' . (int) $sheet_id . ', '
						. ' db.gen_hash = ' . DB::Quote( $gen_hash )
						. ' where '
						. ' db.period_id  = ' . (int) $period
						. ' and db.org = ' . (int) $org
						. ' and db.status = 0 or db.gen_hash = ' . DB::Quote( $gen_hash )
						. '; ' : ''
		;

		$update .= in_array( 2, $data_types ) ? ' update slf_worker_benefits ir set '
						. ' ir.status = 1, '
						. ' ir.sheet_id = ' . (int) $sheet_id . ', '
						. ' ir.gen_hash = ' . DB::Quote( $gen_hash )
						. ' where '
						. ' ir.period_id  = ' . (int) $period
						. ' and ir.org = ' . (int) $org
						. ' and ir.status = 0 or ir.gen_hash = ' . DB::Quote( $gen_hash )
						. '; ' : ''
		;
		$Data = array_chunk( $ids, 800 );
		foreach ( $Data as $DD )
		{
			$update .= ' update lib_salary_calculations lsc set '
							. ' lsc.sheet_id = ' . (int) $sheet_id . ', '
							. ' lsc.gen_hash = ' . DB::Quote( $gen_hash )
							. ' where lsc.id in (' . implode( ', ', (array) $DD ) . ') '
							. '; '
			;
		}

		$update .= ' update lib_f_salary_sheets fs set '
						. ' fs.gen_hash = ' . DB::Quote( $gen_hash )
						. ' where fs.id = ' . (int) $sheet_id
						. '; '
		;

		$update .= ' end; ';

		if ( !DB::Update( $update ) )
		{
			return false;
		}
		foreach ( $Data as $DD )
		{
			$delete = 'delete from lib_salary_calculations sc where '
							. ' sc.sheet_id = ' . $sheet_id
							. ' and sc.id not in (' . implode( ', ', (array) $DD ) . ') '
			;
			if ( !DB::Delete( $delete ) )
			{
				return false;
			}
		}

		return true;

	}

	public function D_elete( $ids )
	{
		if ( empty( $ids ) )
		{
			return false;
		}

		$update = 'Begin ';
		$update .= ' update slf_daily_salary ds set ds.status = 0, ds.gen_hash = \'\', ds.sheet_id = \'\' where ds.sheet_id in (' . implode( ', ', $ids ) . '); ';
		$update .= ' update slf_daily_benefits db set db.status = 0, db.gen_hash = \'\', db.sheet_id = \'\' where db.sheet_id in (' . implode( ', ', $ids ) . '); ';
		$update .= ' update slf_worker_benefits wb set wb.status = 0, wb.gen_hash = \'\', wb.sheet_id = \'\' where wb.sheet_id in (' . implode( ', ', $ids ) . '); ';
		$update .= ' update lib_f_salary_sheets sh set sh.active = 0 where sh.id in (' . implode( ', ', $ids ) . '); ';
		$update .= 'end;';

		if ( !DB::Update( $update ) )
		{
			return false;
		}

		$delete = 'delete from lib_salary_calculations lsc where lsc.sheet_id in (' . implode( ', ', $ids ) . ')';
		if ( !DB::Delete( $delete ) )
		{
			return false;
		}

		return true;

	}

	public function Delete_each( $ids, $sheet_id )
	{
		$workers = [];
		foreach ( $ids as $each )
		{
			$ex = (array) explode( '|', $each );
			$workers[] = C::_( 0, $ex, 0 );
		}

		$delete = 'delete from lib_salary_calculations lsc where '
						. ' lsc.worker in (' . implode( ', ', $workers ) . ')'
						. ' and lsc.sheet_id = ' . (int) $sheet_id
		;
		if ( !DB::Delete( $delete ) )
		{
			return false;
		}

		return true;

	}

}
