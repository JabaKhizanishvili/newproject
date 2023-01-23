<?PHP

class TaskTable extends Tablehrs_tasksInterface
{
	public $_DATE_FIELDS = array(
			'TASK_COMPLETE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'TASK_CREATE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'TASK_DUE_DATE' => 'yyyy-mm-dd hh24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_tasks', 'TASK_ID', 'library.nextval' );

	}

}
