<?PHP

class BlackListTable extends TableHrs_black_listInterface
{
	public $_DATE_FIELDS = array(
			'LISTED_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'EXPIRY_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_black_list', 'ID', 'sqs_hrs_black_list.nextval' );

	}

}
