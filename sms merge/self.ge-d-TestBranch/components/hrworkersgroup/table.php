<?PHP

class hrworkersgroupTable extends TableLib_workers_groupsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_workers_groups', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		if ( is_array( $this->WORKERS ) )
		{
			$this->WORKERS = implode( ',', $this->WORKERS );
		}
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
