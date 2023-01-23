<?PHP

class TasksTable extends Tablehrs_tasksInterface
{
	public $_DATE_FIELDS = array(
			'TASK_CREATE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'TASK_DUE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'TASK_COMPLETE_DATE' => 'yyyy-mm-dd hh24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_tasks', 'TASK_ID', 'procedures.nextval' );

	}

	public function check()
	{
		$this->TASK_TITLE = trim( $this->TASK_TITLE );
		$this->STATE = (int) trim( $this->STATE );
		$this->WORKFLOW_ID = trim( $this->WORKFLOW_ID );
		$this->TASK_COMPLETE_DATE = trim( $this->TASK_COMPLETE_DATE );
		if ( !empty( $this->TASK_COMPLETE_DATE ) )
		{
			$Complete_Date = new PDate( $this->TASK_COMPLETE_DATE );
			$this->TASK_COMPLETE_DATE = $Complete_Date->toFormat();
		}
		$this->TASK_DUE_DATE = trim( $this->TASK_DUE_DATE );
		if ( !empty( $this->TASK_DUE_DATE ) )
		{
			$Complete_Date = new PDate( $this->TASK_DUE_DATE );
			$this->TASK_DUE_DATE = $Complete_Date->toFormat();
		}
		$this->TASK_CREATE_DATE = trim( $this->TASK_CREATE_DATE );
		if ( empty( $this->TASK_CREATE_DATE ) )
		{
			$Date = new PDate();
			$this->TASK_CREATE_DATE = $Date->toFormat();
		}
		if ( empty( $this->TASK_TITLE ) )
		{
			return false;
		}

		return true;

	}

}
