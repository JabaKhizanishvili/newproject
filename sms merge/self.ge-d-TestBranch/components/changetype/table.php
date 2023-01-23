<?PHP

class ChangetypeTable extends TableLib_change_typeInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_change_type', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		if ( $this->FIELDS )
		{
			$this->FIELDS = implode( ',', $this->FIELDS );
		}

		return true;

	}

}
