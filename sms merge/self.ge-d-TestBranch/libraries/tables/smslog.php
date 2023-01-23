<?PHP

class SMSLogTable extends TableSystem_sms_logInterface
{
	public $_DATE_FIELDS = array(
			'LOG_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'LOG_STATUS_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'system_sms_log', 'LOG_ID', 'sqs_system_sms_log.nextval' );

	}

}
