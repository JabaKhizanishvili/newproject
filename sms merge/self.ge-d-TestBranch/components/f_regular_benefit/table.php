<?php

class f_regular_benefitTable extends Tableslf_worker_benefitsInterface
{
	public function __construct()
	{
		parent::__construct( 'slf_worker_benefits', 'ID', 'sqs_worker_benefits.nextval' );

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
		return true;

	}

}
