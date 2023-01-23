<?PHP

class Job_descriptionTable extends TableLib_job_descriptionsInterface
{
	public function __construct()
	{
		parent::__construct( 'Lib_job_descriptions', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_GOAL = trim( $this->LIB_GOAL );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		if ( is_array( $this->V_FILE ) )
		{
			$this->V_FILE = implode( '|', $this->V_FILE );
		}
		return true;

	}

	public function SplitText( $SDATA, $Key = '' )
	{
		$TDATAx = array();
		$Index = 0;
		$MaxLength = 3500;
		if ( strlen( $SDATA ) > $MaxLength )
		{
			$MData = explode( ' ', str_replace( '&nbsp;', ' ', trim( $SDATA ) ) );

			foreach ( $MData as $Ph )
			{
				$TDATAxT = C::_( $Key . $Index, $TDATAx ) . ' ' . $Ph;
				$TDATAx[$Key . $Index] = $TDATAxT;
				if ( strlen( $TDATAxT ) >= $MaxLength )
				{
					$Index++;
				}
			}
		}
		else
		{
			$TDATAx[$Key . $Index] = $SDATA;
		}
		return $TDATAx;

	}

}
