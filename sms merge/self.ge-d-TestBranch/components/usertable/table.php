<?PHP

class TableHRS_Table extends TableHrs_tableInterface
{
	public function __construct()
	{
		parent::__construct( 'hrs_table', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->BILL_ID = (int) trim( $this->BILL_ID );
		$this->WORKER = trim( $this->WORKER );
		if ( empty( $this->BILL_ID ) )
		{
			return false;
		}
		if ( empty( $this->WORKER ) )
		{
			return false;
		}
		return true;

	}

}
