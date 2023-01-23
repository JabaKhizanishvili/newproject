<?PHP

class GlobalGraphTable extends TableLib_standard_graphsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_standard_graphs', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->MONDAY = (int) trim( $this->MONDAY );
		$this->TUESDAY = (int) trim( $this->TUESDAY );
		$this->WEDNESDAY = (int) trim( $this->WEDNESDAY );
		$this->THURSDAY = (int) trim( $this->THURSDAY );
		$this->FRIDAY = (int) trim( $this->FRIDAY );
		$this->SATURDAY = (int) trim( $this->SATURDAY );
		$this->SUNDAY = (int) trim( $this->SUNDAY );

		$collect = array_slice( (array) $this, 4, 7 );
		foreach ( $collect as $key => $value )
		{
			if ( $this->$key < 0 )
			{
				return false;
			}
		}

		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
