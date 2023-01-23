<?PHP

class VisitorTable extends TableLib_visitorsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_visitors', 'ID', 'users_sqs.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->CODE = trim( $this->CODE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
//		$this->ASSIGN = (int) trim( $this->ASSIGN );
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		if ( empty( $this->CODE ) )
		{
			return false;
		}
		return true;

	}

}
