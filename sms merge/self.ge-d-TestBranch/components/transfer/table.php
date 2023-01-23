<?PHP

class hrs_transfers extends TableHrs_transfersInterface
{
	public $_DATE_FIELDS = array(
			'APPROVE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'TRANSFER_DATE' => 'yyyy-mm-dd',
			'REC_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'APPROVE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'DEL_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_transfers', 'ID', 'library.nextval' );

	}

	public function check()
	{
		if ( empty( $this->WORKER ) )
		{
			return false;
		}
		if ( empty( $this->CHIEF ) )
		{
			return false;
		}
		if ( empty( $this->REC_DATE ) )
		{
			$this->REC_DATE = PDate::Get()->toFormat();
			$this->REC_USER = XGraph::getWorkerIDsByOrg( $this->ORG );
		}
		return true;

	}
	
	public function check_2()
	{
		$this->ORG_PLACE = (int) trim( $this->ORG_PLACE );
		$this->GRAPHTYPE = (int) trim( $this->GRAPHTYPE );
		$this->WORK_TYPE = (int) trim( $this->WORK_TYPE );
		
		if (empty($this->ORG_PLACE ) )
		{
			return false;
		}
		if ($this->GRAPHTYPE < 0 )
		{
			return false;
		}
		if ( empty($this->WORK_TYPE  ))
		{
			return false;
		}
		if ( empty( $this->TRANSFER_DATE ) )
		{
			return false;
		}
		
		return true;
	}

}
