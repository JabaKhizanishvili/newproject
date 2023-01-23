<?php

class transferTable extends TableHrs_transfersInterface
{
	public function __construct()
	{
		parent::__construct( 'hrs_transfers', 'ID', 'library.nextval' );

	}

}
