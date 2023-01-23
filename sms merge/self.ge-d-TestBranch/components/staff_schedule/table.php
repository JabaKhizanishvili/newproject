<?PHP

class Staff_schedulesTable extends TableLib_staff_schedulesInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_staff_schedules', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ORG_PLACE = (int) trim( $this->ORG_PLACE );
		$this->POSITION = (int) trim( $this->POSITION );
		$this->JD = (int) trim( $this->JD );
		$this->QUANTITY = (int) trim( $this->QUANTITY );
		$this->SALARY = (float) trim( $this->SALARY );
		$this->REPLACE_SCHEDULE = (int) trim( $this->REPLACE_SCHEDULE );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->ORDERING = trim( $this->ORDERING );

		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		if ( empty( $this->ORG_PLACE ) )
		{
			return false;
		}
		if ( $this->QUANTITY < 0 )
		{
			return false;
		}
		if ( empty( $this->POSITION ) )
		{
			return false;
		}
		return true;

	}

}
