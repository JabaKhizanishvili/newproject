<?PHP

class r_workedtimeTable extends TableHrs_worked_timesInterface
{
	public function __construct()
	{
		parent::__construct( 'hrs_worked_times', 'ID', 'sqs_hrs_worked_times.nextval' );

	}

	public function check()
	{
		$this->WORKED_TIME = floatval( $this->DAY_WORKED_TIME ) + floatval( $this->NIGHT_WORKED_TIME );
		if ( empty( $this->WORKED_TIME ) )
		{
			return false;
		}
		return true;

	}

}
