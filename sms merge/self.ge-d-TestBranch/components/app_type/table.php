<?PHP

class App_typeTable extends TableLib_limit_app_typesInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_limit_app_types', 'ID', 'SQS_HOLIDAYS.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->PERIODICITY = (int) trim( $this->PERIODICITY );
		$this->WAGE_TYPE = (int) trim( $this->WAGE_TYPE );
		$this->LIMIT = (int) trim( $this->LIMIT );
		$this->HOLIDAY_RESTART_DAY = (int) trim( $this->HOLIDAY_RESTART_DAY );
		$this->HOLIDAY_RESTART_MONTH = (int) trim( $this->HOLIDAY_RESTART_MONTH );
		$this->HOLIDAY_START_LIMIT = (int) trim( $this->HOLIDAY_START_LIMIT );

		if ( $this->PERIODICITY == 3 )
		{
			if ( empty( $this->HOLIDAY_START_LIMIT ) )
			{
				return false;
			}
			if ( empty( $this->HOLIDAY_RESTART_MONTH ) )
			{
				return false;
			}
			if ( empty( $this->HOLIDAY_RESTART_DAY ) )
			{
				return false;
			}
		}
		else
		{
			$this->HOLIDAY_RESTART_DAY = '';
			$this->HOLIDAY_RESTART_MONTH = '';
			$this->HOLIDAY_START_LIMIT = '';
		}

		if ( empty( $this->LIMIT ) )
		{
			return false;
		}
		if ( $this->PERIODICITY == -1 )
		{
			return false;
		}
		if ( $this->WAGE_TYPE == -1 )
		{
			return false;
		}
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
