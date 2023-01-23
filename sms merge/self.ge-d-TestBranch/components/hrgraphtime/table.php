<?PHP

class hrgraphtimeTable extends TableLib_graph_timesInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_graph_times', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->START_TIME = trim( $this->START_TIME );
		$this->END_TIME = trim( $this->END_TIME );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
        $this->HOLIDAY_YN = trim($this->HOLIDAY_YN);



		if($this->HOLIDAY_YN == '') {
		    return false;
        }
		if ( $this->VACATION_INDEX != (float) $this->VACATION_INDEX )
		{
			return false;
		}
		$this->VACATION_INDEX = (float) trim( $this->VACATION_INDEX );
		if ( $this->VACATION_INDEX < 0 )
		{
			return false;
		}
		if ( empty( $this->OWNER ) )
		{
			$this->OWNER = 1;
		}
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		if ( empty( $this->START_TIME ) )
		{
			return false;
		}
		if ( empty( $this->END_TIME ) )
		{
			return false;
		}
		if ( !preg_match( '/[0-9]*[0-9]:[0-9][0-9]/', $this->START_TIME ) )
		{
			return false;
		}
		if ( !empty( $this->START_BREAK ) && !preg_match( '/[0-9]*[0-9]:[0-9][0-9]/', $this->START_BREAK ) )
		{
			return false;
		}
		if ( !preg_match( '/[0-9]*[0-9]:[0-9][0-9]/', $this->END_TIME ) )
		{
			return false;
		}
		if ( !empty( $this->END_BREAK ) && !preg_match( '/[0-9]*[0-9]:[0-9][0-9]/', $this->END_BREAK ) )
		{
			return false;
		}
		if ( !empty( $this->NEW_DAY ) && !preg_match( '/[0-9]*[0-9]:[0-9][0-9]/', $this->NEW_DAY ) )
		{
			return false;
		}


        return true;

	}

}
