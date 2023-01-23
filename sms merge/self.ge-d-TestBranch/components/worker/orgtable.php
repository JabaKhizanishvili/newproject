<?PHP

class WorkersORgTable extends TableHrs_workers_ORGInterface
{
	public $_DATE_FIELDS = array(
			'CALCULUS_DATE' => 'yyyy-mm-dd',
			'CONTRACTS_DATE' => 'yyyy-mm-dd',
			'CONTRACT_END_DATE' => 'yyyy-mm-dd',
	);

	public function __construct()
	{
		parent::__construct( 'hrs_workers_org', 'ID', 'users_sqs.nextval' );

	}

	public function check()
	{
		$this->CALCULUS_DATE = trim( $this->CALCULUS_DATE );
		$this->CALCULUS_DATE = PDate::Get( $this->CALCULUS_DATE )->toFormat( '%Y-%m-%d' );
		$this->CONTRACTS_DATE = PDate::Get( $this->CONTRACTS_DATE )->toFormat( '%Y-%m-%d' );
		$this->CONTRACT_END_DATE = PDate::Get( $this->CONTRACT_END_DATE )->toFormat( '%Y-%m-%d' );
		if ( is_array( $this->FILES ) )
		{
			$this->FILES = implode( '|', $this->FILES );
		}

		return true;

	}

}
