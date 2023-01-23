<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class f_i_benefit_fineModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new f_i_benefit_fineTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function Calculate( $data, $print_text = false )
	{
		if ( !count( $data ) )
		{
			return [];
		}

		$benefit_id = C::_( 'BENEFIT_ID', $data );
		$cost = C::_( 'COST', $data, null );

		$benefits = Benefits::get_benefit_types();
		$benefit = C::_( $benefit_id, $benefits );

		$worker = XGraph::getWorkerDataSch( (int) C::_( 'WORKER', $data ) );
		$person_id = (int) C::_( 'PARENT_ID', $worker );
		$person = Users::getUser( $person_id );

		$type = C::_( 'TYPE', $benefit, null );
		$gross_tax_type = C::_( 'GROSS_TAX_TYPE', $benefit, null );
		$share_type = C::_( 'SHARE_TYPE', $benefit, null );
		$company_share = (int) C::_( 'COMPANY_SHARE', $benefit );
		$worker_share = (int) C::_( 'WORKER_SHARE', $benefit );
		$pay_pension = C::_( 'PAY_PENSION', $person, null );

		$collect = [];
		$collect['COST'] = $cost;
		$collect['TYPE'] = Xhelp::caseText( $type, [ 1 => 'Supplement', 2 => 'loss' ], $print_text );
		$collect['GROSS_TAX_TYPE'] = Xhelp::caseText( $gross_tax_type, [ 0 => 'none', 1 => 'income_pension', 2 => 'income_tax' ], $print_text );

//		COMPANY_SHARE
		if ( $share_type == 1 && !empty( $company_share ) )
		{
			$collect['COMPANY_SHARE'] = $cost * ($company_share / 100);
		}
		elseif ( $share_type == 2 && !empty( $company_share ) )
		{
			$collect['COMPANY_SHARE'] = $cost;
		}
		else
		{
			$collect['COMPANY_SHARE'] = 0;
		}

//		WORKER_SHARE
		if ( $share_type == 1 && !empty( $worker_share ) )
		{
			$collect['WORKER_SHARE'] = $cost * ($worker_share / 100);
		}
		if ( $share_type == 2 && !empty( $worker_share ) )
		{
			$collect['WORKER_SHARE'] = $cost;
		}
		if ( $worker_share > 0 && empty( $worker_share ) )
		{
			$collect['WORKER_SHARE'] = $cost - $company_share;
		}

//		ACCRUED_INCOME
		if ( $gross_tax_type == 1 || $gross_tax_type == 2 )
		{
			$collect['ACCRUED_INCOME'] = ($cost / 0.8) - $cost;
		}

//		ACCRUED_PENSION
		if ( $gross_tax_type == 1 && $pay_pension == 1 )
		{
			$collect['ACCRUED_PENSION'] = Helper::FormatBalance( ($cost / 0.8 / 0.98) - $cost / 0.8, 2 );
		}

		return $collect;

	}

	public function SaveData( $data )
	{
		$calc = $this->Calculate( $data );
		$merge = array_merge( $data, $calc );
		$benefit_id = C::_( 'BENEFIT_ID', $merge );

		$benefits = Benefits::get_benefit_types();
		$benefit = C::_( $benefit_id, $benefits );
		unset( $benefit->ID );

		$DateTime = PDate::Get();
		$Date = $DateTime->toFormat( '%Y-%m-%d' );
		$prev_Date = PDate::Get( $Date . ' -1 day' )->toFormat( '%Y-%m-%d' );
		$b_type = C::_( 'TYPE', $merge );
		$cost = C::_( 'COST', $merge );
		$share_type = C::_( 'SHARE_TYPE', $benefit );
		$worker_share = C::_( 'WORKER_SHARE', $benefit );
		$company_share = C::_( 'COMPANY_SHARE', $benefit );

		$this->Table->bind( $benefit );
		$this->Table->bind( $merge );
		$this->Table->REC_DATE = $DateTime->toFormat();
		$this->Table->CALC_DATE = $prev_Date;

		if ( $b_type == 2 )
		{
			if ( $share_type == 1 )
			{
				$this->Table->WORKER_MINUS = $cost * ($worker_share / 100);
			}

			if ( $share_type == 2 )
			{
				$this->Table->WORKER_MINUS = $worker_share;
			}
		}

		if ( $b_type == 1 )
		{
			if ( $share_type == 1 )
			{
				$this->Table->WORKER_PLUS = $cost * ($worker_share / 100);
			}

			if ( $share_type == 2 )
			{
				$this->Table->WORKER_PLUS = $worker_share;
			}
		}

		if ( $share_type == 1 )
		{
			$this->Table->COMPANY_PLUS = $cost * ($company_share / 100);
		}
		if ( $share_type == 2 )
		{
			$this->Table->COMPANY_PLUS = $company_share;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}

		return true;

	}

	public function Delete( $data )
	{
		if ( !count( $data ) )
		{
			return false;
		}

		foreach ( $data as $id )
		{
			$this->Table->resetAll();
			$this->Table->load( $id );
			$this->Table->STATUS = -2;
			$this->Table->store();
		}

		return true;

	}

}
