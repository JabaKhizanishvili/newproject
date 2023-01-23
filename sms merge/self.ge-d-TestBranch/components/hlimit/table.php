<?PHP

class hlimitsTable extends TableLib_user_holiday_limitInterface
{
	public $_DATE_FIELDS = array(
			'END_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'START_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'lib_user_holiday_limit', 'ID', 'sqs_lib_user_holiday_limit.nextval' );

	}

	public function check()
	{
		$this->COUNT = intval( $this->COUNT );
		$this->C_LIMIT =  (float)$this->C_LIMIT;
		$this->AWORKER = Users::GetUserID();
		if ( $this->COUNT < 0 )
		{
			return false;
		}
		if ( $this->C_LIMIT < 0 )
		{
			return false;
		}
		return true;

	}

}
