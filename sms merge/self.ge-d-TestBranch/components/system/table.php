<?PHP

class systemTable extends TableLib_systemsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_systems', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->MULTY_SYSTEM = (int) trim( $this->MULTY_SYSTEM );
		$this->EXPIRED_DATE = trim( $this->EXPIRED_DATE );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		$StartDate = new PDate( $this->EXPIRED_DATE );
		$this->EXPIRED_DATE = $StartDate->toFormat( '%Y-%m-%d 00:00:00' );
		return true;

	}

}
