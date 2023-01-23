<?php

class f_salary_sheetTable extends Tablelib_f_salary_sheetsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_f_salary_sheets', 'ID', 'sqs_f_salary_sheets.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->ORG = (int) $this->ORG;
		$this->PERIOD = (int) $this->PERIOD;
		$this->GENERATION_TYPE = trim( $this->GENERATION_TYPE );

		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}

		if ( empty( $this->ORG ) )
		{
			return false;
		}

		if ( empty( $this->PERIOD ) )
		{
			return false;
		}

		if ( empty( $this->DATA_TYPE ) )
		{
			return false;
		}

		if ( $this->GENERATION_TYPE == '' )
		{
			return false;
		}

		if ( in_array( 1, $this->DATA_TYPE ) && empty( $this->REGULAR_BENEFIT ) )
		{
			return false;
		}
		else
		{
			$this->REGULAR_BENEFIT = $this->collectBenefits( C::_( 1, $this->DATA_TYPE, [] ) );
		}

		if ( in_array( 2, $this->DATA_TYPE ) && empty( $this->IREGULAR_BENEFIT ) )
		{
			return false;
		}
		else
		{
			$this->IREGULAR_BENEFIT = $this->collectBenefits( C::_( 2, $this->DATA_TYPE, [] ) );
		}

		$this->DATA_TYPE = implode( '|', array_keys( $this->DATA_TYPE ) );

		return true;

	}

	public function collectBenefits( $data )
	{
		if ( empty( $data ) )
		{
			return '';
		}

		$collect = [];
		foreach ( $data as $each )
		{
			if ( empty( $each ) || !is_array( $each ) )
			{
				continue;
			}

			foreach ( $each as $id )
			{
				$collect[] = $id;
			}
		}

		return implode( '|', $collect );

	}

}
