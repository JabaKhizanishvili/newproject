<?PHP

class wgroupTable extends TableLib_wgroupsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_wgroups', 'ID', 'library.nextval' );

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
//		if ( is_array( $this->CHAPTERS ) )
//		{
//			$this->CHAPTERS = implode( ',', $this->CHAPTERS );
//		}
//		if ( is_array( $this->POSITIONS ) )
//		{
//			$this->POSITIONS = implode( ',', $this->POSITIONS );
//		}
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}

		if ( !($this->WORKERS) )// || $this->CHAPTERS || $this->POSITIONS ) )
		{
			return false;
		}
		return true;

	}

}
