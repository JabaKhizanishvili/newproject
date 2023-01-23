<?PHP

class UnitsTable extends TableLib_unitsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_units', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->TYPE = (int) trim( $this->TYPE );
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->LFT = (int) trim( $this->LFT );
		$this->RGT = (int) trim( $this->RGT );
		$this->PARENT_ID = (int) trim( $this->PARENT_ID );
		$this->ULEVEL = (int) Units::getCurentLevelByParent( $this->PARENT_ID );
		$this->ORG = (int) Units::getCurentOrgByParent( $this->PARENT_ID );
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		if ( empty( $this->PARENT_ID ) )
		{
			return false;
		}
		if ( empty( $this->ORG ) )
		{
			return false;
		}
		if ( empty( $this->TYPE ) )
		{
			return false;
		}
		return true;

	}

}
