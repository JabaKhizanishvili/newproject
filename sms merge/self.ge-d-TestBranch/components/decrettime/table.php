<?PHP

class DecretTimeTable extends TableHrs_decret_hourInterface
{
	public $_DATE_FIELDS = array(
			'REC_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'APPROVE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'START_DATE' => 'yyyy-mm-dd',
			'END_DATE' => 'yyyy-mm-dd'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_decret_hour', 'ID', 'sqs_applications.nextval' );

	}

	public function check()
	{
		$this->WORKER = (int) trim( $this->WORKER );
		$this->TYPE = (int) trim( $this->TYPE );
		$this->DEL_USER = (int) trim( $this->DEL_USER );
		$this->STATUS = 1;
		$this->START_DATE = trim( $this->START_DATE );
		$this->END_DATE = trim( $this->END_DATE );
		if ( empty( $this->WORKER ) )
		{
			return false;
		}
		if ( empty( $this->TYPE ) )
		{
			return false;
		}
		if ( empty( $this->START_DATE ) )
		{
			return false;
		}
		if ( empty( $this->END_DATE ) )
		{
			return false;
		}
		$StartDate = new PDate( $this->START_DATE );
		$this->START_DATE = $StartDate->toFormat( '%Y-%m-%d' );
		$EndDate = new PDate( $this->END_DATE );
		$this->END_DATE = $EndDate->toFormat( '%Y-%m-%d' );
		$Now = new PDate( );
		$this->APPROVE_DATE = $Now->toFormat();
		$this->APPROVE = Users::GetUserID();
		if ( empty( $this->REC_DATE ) )
		{
			$this->REC_DATE = $Now->toFormat();
		}
		return true;

	}

}
