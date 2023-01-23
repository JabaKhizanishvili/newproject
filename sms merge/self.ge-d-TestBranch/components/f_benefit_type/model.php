<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class f_benefit_typeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new f_benefit_typeTable( );
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

	public function SaveData( $data )
	{
		if ( C::_( 'REGULARITY', $data ) == 2 )
		{
			$data['SHARE_TYPE'] = 1;
			$data['ACCRUAL_SET'] = '';
		}

		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}

		if ( !$this->Table->check() )
		{
			return false;
		}

		$this->Calculate( $data );

		if ( !$this->Table->store() )
		{
			return false;
		}

		return $this->Table->insertid();

	}

	public function Calculate( $data )
	{
		$b_type = C::_( 'TYPE', $data );
		$cost = (int) C::_( 'COST', $data );
		$share_type = C::_( 'SHARE_TYPE', $data );
		$gross_tax_type = C::_( 'GROSS_TAX_TYPE', $data );
		$worker_share = (int) C::_( 'WORKER_SHARE', $data );
		$company_share = (int) C::_( 'COMPANY_SHARE', $data );

		if ( $b_type == 2 )
		{
			if ( $share_type == 1 )
			{
				$this->Table->C_WORKER_SHARE = $cost * ($worker_share / 100);
			}

			if ( $share_type == 2 )
			{
				$this->Table->C_WORKER_SHARE = $worker_share;
			}
		}

		if ( $b_type == 1 )
		{
			if ( $share_type == 1 )
			{
				$this->Table->C_WORKER_SHARE = $cost * ($worker_share / 100);
			}

			if ( $share_type == 2 )
			{
				$this->Table->C_WORKER_SHARE = $worker_share;
			}
		}

		if ( $share_type == 1 )
		{
			$this->Table->C_COMPANY_SHARE = $cost * ($company_share / 100);
		}

		if ( $share_type == 2 )
		{
			$this->Table->C_COMPANY_SHARE = $company_share;
		}

		if ( $gross_tax_type == 1 || $gross_tax_type == 2 )
		{
			$this->Table->ACCRUED_INCOME = ($cost / 0.8) - $cost;
		}

		if ( $gross_tax_type == 1 )
		{
			$this->Table->ACCRUED_PENSION = ($cost / 0.8 / 0.98) - $cost - $this->Table->ACCRUED_INCOME;
		}

	}

}
