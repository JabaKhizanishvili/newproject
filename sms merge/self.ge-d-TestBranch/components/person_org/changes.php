<?PHP

class ChangesTable extends TableSlf_changesInterface
{
	public $_DATE_FIELDS = array(
			'CHANGE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'RELEASE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'CONTRACTS_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'CONTRACT_END_DATE' => 'yyyy-mm-dd HH24:mi:ss',
	);

	public function __construct()
	{
		parent::__construct( 'slf_changes', 'ID', 'sqs_slf_change.nextval' );

	}

	public function check()
	{
		$this->collect();

		if ( empty( $this->ORG ) )
		{
			return false;
		}
		if ( empty( $this->STAFF_SCHEDULE ) )
		{
			return false;
		}
		if ( $this->CONTRACT_TYPE < 0 )
		{
			return false;
		}
		if ( empty( $this->CONTRACTS_DATE ) )
		{
			return false;
		}
		else
		{
			$this->CONTRACTS_DATE = PDate::Get( $this->CONTRACTS_DATE )->toFormat( '%Y-%m-%d' );
		}
		if ( empty( $this->CONTRACT_END_DATE ) )
		{
			return false;
		}
		else
		{
			$this->CONTRACT_END_DATE = PDate::Get( $this->CONTRACT_END_DATE )->toFormat( '%Y-%m-%d' );
		}
		if ( $this->CALCULUS_TYPE < 0 )
		{
			return false;
		}
		if ( $this->GRAPHTYPE < 0 )
		{
			return false;
		}
		if ( empty( $this->CHANGE_DATE ) )
		{
			return false;
		}
		else
		{
			$this->CHANGE_DATE = PDate::Get( $this->CHANGE_DATE )->toFormat();
		}

		return true;

	}

	public function checkChanges()
	{
		$this->collect();

		if ( !empty( $this->CONTRACTS_DATE ) )
		{
			$this->CONTRACTS_DATE = PDate::Get( $this->CONTRACTS_DATE )->toFormat( '%Y-%m-%d 00:00:00' );
		}

		if ( !empty( $this->CONTRACT_END_DATE ) )
		{
			$this->CONTRACT_END_DATE = PDate::Get( $this->CONTRACT_END_DATE )->toFormat( '%Y-%m-%d 00:00:00' );
		}

		if ( empty( $this->CHANGE_DATE ) )
		{
			return false;
		}
		else
		{
			$this->CHANGE_DATE = PDate::Get( $this->CHANGE_DATE )->toFormat();
		}

		return true;

	}

	public function checkRelease()
	{
		$this->collect();

		if ( empty( $this->CHANGE_DATE ) )
		{
			return false;
		}
		else
		{
			$this->CHANGE_DATE = PDate::Get( $this->CHANGE_DATE )->toFormat();
			$this->RELEASE_DATE = $this->CHANGE_DATE;
		}

		if ( empty( $this->RELEASE_TYPE ) )
		{
			return false;
		}

		return true;

	}

	public function collect()
	{
		if ( is_array( $this->ACCOUNTING_OFFICES ) )
		{
			$this->ACCOUNTING_OFFICES = implode( ',', $this->ACCOUNTING_OFFICES );
		}
		$this->ORG = (int) trim( $this->ORG );
		$this->PERSON = trim( $this->PERSON );
		$this->STAFF_SCHEDULE = (int) trim( $this->STAFF_SCHEDULE );
		$this->SALARY = trim( $this->SALARY );
		$this->AUTO_OVERTIME = (int) trim( $this->AUTO_OVERTIME );
		$this->CALCULUS_TYPE = (int) trim( $this->CALCULUS_TYPE );
		$this->TABLENUM = trim( $this->TABLENUM );
		$this->SALARY_PAYMENT_TYPE = (int) trim( $this->SALARY_PAYMENT_TYPE );
		$this->IBAN = trim( $this->IBAN );
		$this->ACCOUNTING_OFFICES = trim( $this->ACCOUNTING_OFFICES );
		$this->CHANGE_DATE = trim( $this->CHANGE_DATE );
		$this->CONTRACTS_DATE = trim( $this->CONTRACTS_DATE );
		$this->CONTRACT_END_DATE = trim( $this->CONTRACT_END_DATE );
		$this->CALCULUS_REGIME = (int) trim( $this->CALCULUS_REGIME );
		$this->CATEGORY_ID = (int) trim( $this->CATEGORY_ID );
		$this->GRAPHTYPE = (int) trim( $this->GRAPHTYPE );
		$this->CONTRACT_TYPE = (int) trim( $this->CONTRACT_TYPE );
		$this->AUTO_OVERTIME = (int) trim( $this->AUTO_OVERTIME );
		$this->RELEASE_TYPE = (int) trim( $this->RELEASE_TYPE );
		$this->RELEASE_COMMENT = trim( $this->RELEASE_COMMENT );

	}

}
