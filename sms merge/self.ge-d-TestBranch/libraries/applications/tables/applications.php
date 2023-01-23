<?PHP

class ApplicationsTable extends TableHrs_applicationsInterface
{
	public $_DATE_FIELDS = array(
			'START_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'END_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'REC_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'APPROVE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'SYNC_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'DEL_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_applications', 'ID', 'procedures.nextval' );

	}

	public function check()
	{
		$this->START_DATE = trim( $this->START_DATE );
		$this->END_DATE = trim( $this->END_DATE );
		$this->REC_DATE = trim( $this->REC_DATE );
		if ( is_null( $this->STATUS ) )
		{
			$this->STATUS = 0;
		}
		if ( is_null( $this->APPROVE ) )
		{
			$this->APPROVE = 0;
		}
		if ( is_null( $this->SYNC ) )
		{
			$this->SYNC = 0;
		}
		if ( is_null( $this->DEL_USER ) )
		{
			$this->DEL_USER = 0;
		}
		if ( empty( $this->WORKER ) )
		{
			return false;
		}
		if ( empty( $this->START_DATE ) )
		{
			XError::setError( 'start date not entered!' );
			return false;
		}

		if ( empty( $this->END_DATE ) )
		{
			XError::setError( 'end date not entered!' );
			return false;
		}
		$RecDate = new PDate( $this->REC_DATE );
		$this->REC_DATE = $RecDate->toFormat( '%Y-%m-%d %H:%M:%S' );
		return true;

	}

}
