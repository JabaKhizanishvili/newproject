<?PHP

class Person_orgTable extends TableSlf_workerInterface
{
	public $_DATE_FIELDS = array(
			'CONTRACTS_DATE' => 'yyyy-mm-dd',
			'CONTRACT_END_DATE' => 'yyyy-mm-dd',
	);

	public function __construct()
	{
		parent::__construct( 'slf_worker', 'ID', 'sqs_slf_worker.nextval' );

	}

	public function check()
	{
		$this->ORG = (int) trim( $this->ORG );
		$this->WORKER = trim( $this->WORKER );
		$this->STAFF_SCHEDULE = (int) trim( $this->STAFF_SCHEDULE );
		$this->SALARY = trim( $this->SALARY );
		$this->SALARY_PAYMENT_TYPE = (int) trim( $this->SALARY_PAYMENT_TYPE );
		$this->TABLENUM = trim( $this->TABLENUM );
		$this->ACCOUNTING_OFFICES = trim( $this->ACCOUNTING_OFFICES );
		$this->CONTRACTS_DATE = PDate::Get( $this->CONTRACTS_DATE )->toFormat( '%Y-%m-%d' );
		$this->CONTRACT_END_DATE = PDate::Get( $this->CONTRACT_END_DATE )->toFormat( '%Y-%m-%d' );
		$this->CALCULUS_REGIME = (int) trim( $this->CALCULUS_REGIME );
		$this->CATEGORY_ID = (int) trim( $this->CATEGORY_ID );
		$this->GRAPHTYPE = (int) trim( $this->GRAPHTYPE );
//		$this->GRAPHGROUP = (int) trim( $this->GRAPHGROUP );

		if ( $this->SALARY < 0 )
		{
			return false;
		}
//		if ( $this->GRAPHGROUP < 0 )
//		{
//			return false;
//		}
		if ( $this->GRAPHTYPE < 0 )
		{
			return false;
		}
		if ( $this->ORG < 0 )
		{
			return false;
		}
//		if ( !$this->WORKER )
//		{
//			return false;
//		}
		if ( $this->STAFF_SCHEDULE < 0 )
		{
			return false;
		}

		return true;

	}

}
