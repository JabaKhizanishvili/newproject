<?php

class workerOrgTable extends TableHrs_workers_ORGInterface
{
	public function __construct()
	{
		parent::__construct( 'hrs_workers_org', 'ID', 'users_sqs.nextval' );

	}

}
