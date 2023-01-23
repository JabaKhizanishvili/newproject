<?PHP

class OvertimealertTable extends TableHrs_overtime_alertsInterface
{
	public $_DATE_FIELDS = array(
			'START_DATE' => 'yyyy-mm-dd',
			'END_DATE' => 'yyyy-mm-dd',
			'CHIEF_APPROVE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'USER_APPROVE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'CREATE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'DEL_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'Hrs_overtime_alerts', 'ID', 'library.nextval' );

	}

	public function check()
	{
		if ( $this->USER_APPROVE == 1 and $this->CHIEF_APPROVE == 1 )
		{
			$this->RESOLUTION = 1;
		}
		return true;

	}

}
