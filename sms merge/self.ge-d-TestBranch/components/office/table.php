<?PHP

class OfficeTable extends TableLib_officesInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_offices', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->LAT = trim( $this->LAT );
		$this->LNG = trim( $this->LNG );
		$this->RADIUS = trim( $this->RADIUS );
		$this->OUTER_RADIUS = trim( $this->OUTER_RADIUS );

//		if ( !is_numeric( $this->LAT ) )
//		{
//			return false;
//		}
//		if ( !is_numeric( $this->LNG ) )
//		{
//			return false;
//		}
//		if ( !is_numeric( $this->RADIUS ) )
//		{
//			return false;
//		}
//		if ( !is_numeric( $this->OUTER_RADIUS ) )
//		{
//			return false;
//		}

		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
