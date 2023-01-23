<?php

class controllerTable extends Tablelib_controllersInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_controllers', 'ID', 'sqs_controllers.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->ALERT = (int) trim( $this->ALERT );
		$this->CONTROLLER_CODE = trim( $this->CONTROLLER_CODE );

		if ( empty( $this->CONTROLLER_CODE ) )
		{
			return false;
		}
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}

		return true;

	}

}
