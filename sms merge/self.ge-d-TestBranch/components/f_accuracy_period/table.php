<?PHP

class f_accuracy_periodTable extends TableLib_f_accuracy_periodsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_f_accuracy_periods', 'ID', 'sqs_f_accuracy_periods.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->PERIOD_TYPE = (int) trim( $this->PERIOD_TYPE );
		$this->PERIOD_GENERATOR = (int) trim( $this->PERIOD_GENERATOR );
		$this->PERIOD_START = (int) trim( $this->PERIOD_START );
		$this->FIRST_QUARTER_OF_YEAR_MONTH = (int) trim( $this->FIRST_QUARTER_OF_YEAR_MONTH );
		$this->SECOND_QUARTER_OF_YEAR_MONTH = (int) trim( $this->SECOND_QUARTER_OF_YEAR_MONTH );
		$this->THIRD_QUARTER_OF_YEAR_MONTH = (int) trim( $this->THIRD_QUARTER_OF_YEAR_MONTH );
		$this->FOURTH_QUARTER_OF_YEAR_MONTH = (int) trim( $this->FOURTH_QUARTER_OF_YEAR_MONTH );
		$this->YEARLY_DAY_PERIOD_START = (int) trim( $this->YEARLY_DAY_PERIOD_START );
		$this->NAME_OF_YEAR_MONTH = (int) trim( $this->NAME_OF_YEAR_MONTH );
		$this->FIRST_PERIOD_START = (int) trim( $this->FIRST_PERIOD_START );
		$this->SECOND_PERIOD_START = (int) trim( $this->SECOND_PERIOD_START );
		$this->QUARTER_DAY_START = (int) trim( $this->QUARTER_DAY_START );
		$this->START_DATE_OF_YEAR_DAY = (int) trim( $this->START_DATE_OF_YEAR_DAY );
//		$this->START_DATE_OF_YEAR_MONTH = (int) trim( $this->START_DATE_OF_YEAR_MONTH );

		if ( $this->PERIOD_GENERATOR <= 0 )
        {
            return false;
        }
		$type = $this->PERIOD_TYPE;

		if ( $type < 0 )
		{
			return false;
		}
		if ( $type == 2 )
		{
			if ( $this->PERIOD_START < 0 )
			{
				return false;
			}
		}
		if ( $type == 3 )
		{
			if ( $this->FIRST_PERIOD_START < 1 )
			{
				return false;
			}
			if ( $this->SECOND_PERIOD_START < 1 )
			{
				return false;
			}
		}
		if ( $type == 4 )
		{
			if ( $this->FIRST_QUARTER_OF_YEAR_MONTH < 1 )
			{
				return false;
			}
			if ( $this->SECOND_QUARTER_OF_YEAR_MONTH < 1 )
			{
				return false;
			}
			if ( $this->THIRD_QUARTER_OF_YEAR_MONTH < 1 )
			{
				return false;
			}
			if ( $this->FOURTH_QUARTER_OF_YEAR_MONTH < 1 )
			{
				return false;
			}
			if ( $this->QUARTER_DAY_START < 1 )
			{
				return false;
			}
		}
		if ( $type == 6 )
		{
			if ( $this->YEARLY_DAY_PERIOD_START < 1 )
			{
				return false;
			}
			if ( $this->NAME_OF_YEAR_MONTH < 1 )
			{
				return false;
			}
		}

		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
