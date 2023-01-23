<?PHP

class WorkerHolidayTable extends TableHrs_workers_holidayInterface
{
	public $_DATE_FIELDS = array(
			'HDATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_workers_holiday', 'ID', 'library.nextval' );

	}

}
