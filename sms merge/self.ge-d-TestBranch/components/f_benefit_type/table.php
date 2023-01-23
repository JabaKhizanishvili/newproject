<?php

class f_benefit_typeTable extends Tablelib_f_benefit_typesInterface
{
	public $_DATE_FIELDS = array(
			'START_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'END_DATE' => 'yyyy-mm-dd HH24:mi:ss',
	);

	public function __construct()
	{
		parent::__construct( 'lib_f_benefit_types', 'ID', 'sqs_f_benefit_types.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );

		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}

		$this->CLASS = (int) trim( $this->CLASS );
		$this->REGULARITY = (int) trim( $this->REGULARITY );
		$this->COST = (float) trim( $this->COST );
		$this->COMPANY_SHARE = (float) $this->COMPANY_SHARE;
		$this->WORKER_SHARE = (float) $this->WORKER_SHARE;
		$this->SHARE_TYPE = (int) trim( $this->SHARE_TYPE );
		$this->INCOME = (int) trim( $this->INCOME );
		$this->GROSS_TAX_TYPE = (int) trim( $this->GROSS_TAX_TYPE );
		$this->ACCRUAL_TYPE = (int) trim( $this->ACCRUAL_TYPE );
		$this->PERIODICITY = (int) trim( $this->PERIODICITY );
		$this->ACCRUAL_SET = (int) trim( $this->ACCRUAL_SET );
		$this->BENEFIT = (int) trim( $this->BENEFIT );
		$this->TYPE = (int) trim( $this->TYPE );
		$this->CALCULATION_DAY = (int) trim( $this->CALCULATION_DAY );
		$this->START_DATE = trim( $this->START_DATE );
		$this->END_DATE = trim( $this->END_DATE );

		if ( empty( $this->CALCULATION_DAY ) )
		{
			return false;
		}
		if ( empty( $this->TYPE ) )
		{
			return false;
		}
		if ( empty( $this->BENEFIT ) )
		{
			return false;
		}
		if ( !isset( $this->GROSS_TAX_TYPE ) )
		{
			return false;
		}
		if ( !isset( $this->INCOME ) )
		{
			return false;
		}
		if ( empty( $this->SHARE_TYPE ) )
		{
			return false;
		}
//		if ( $this->WORKER_SHARE == '' )
//		{
//			return false;
//		}
//		if ( $this->COMPANY_SHARE == '' )
//		{
//			return false;
//		}
		if ( empty( $this->REGULARITY ) )
		{
			return false;
		}
		if ( $this->REGULARITY != 2 )
		{
			if ( empty( $this->ACCRUAL_SET ) )
			{
				return false;
			}
			if ( empty( $this->COST ) )
			{
				return false;
			}
			if ( empty( $this->ACCRUAL_TYPE ) )
			{
				return false;
			}
			if ( empty( $this->PERIODICITY ) )
			{
				return false;
			}
			if ( empty( $this->START_DATE ) )
			{
				return false;
			}
			else
			{
				$StartDate = new PDate( $this->START_DATE );
				$this->START_DATE = $StartDate->toFormat( '%Y-%m-%d 00:00:00' );
			}
			if ( empty( $this->END_DATE ) )
			{
				return false;
			}
			else
			{
				$EndDate = new PDate( $this->END_DATE );
				$this->END_DATE = $EndDate->toFormat( '%Y-%m-%d 23:59:59' );
			}
		}
		if ( $this->SHARE_TYPE == 1 )
		{
			if ( $this->COMPANY_SHARE + $this->WORKER_SHARE != 100 )
			{
				return false;
			}
		}
		if ( $this->SHARE_TYPE == 2 )
		{
			if ( $this->COMPANY_SHARE + $this->WORKER_SHARE != $this->COST )
			{
				return false;
			}
		}
		if ( empty( $this->CLASS ) )
		{
			return false;
		}
		
		return true;

	}

}
