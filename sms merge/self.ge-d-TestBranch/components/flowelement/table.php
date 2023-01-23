<?PHP

class FlowElementTable extends TableLib_flow_elementsInterface
{
	public function __construct()
	{
		$this->ORDERING = 999;
		parent::__construct( 'lib_flow_elements', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->LIB_GROUP = (int) $this->LIB_GROUP;
//		$this->LIB_TASK = (int) $this->LIB_TASK;
		$this->ORDERING = (int) $this->ORDERING;
		if ( $this->LIB_TASK == 1000 || $this->LIB_TASK == 999 || $this->LIB_TASK == 998 )
		{
			$this->ORDERING = 0;
		}
		if ( $this->LIB_TASK == 1001 || $this->LIB_TASK == 1002 || $this->LIB_TASK == 1003 )
		{
			$this->ORDERING = 999999;
		}

		$this->ACTIVE = (int) trim( $this->ACTIVE );
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
