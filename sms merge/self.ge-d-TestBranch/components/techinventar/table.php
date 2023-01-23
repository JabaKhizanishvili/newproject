<?PHP

class techinventarTable extends TableLib_techinventarInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_techinventar', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );

//		if ( empty( $this->DRIVER ) )
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
