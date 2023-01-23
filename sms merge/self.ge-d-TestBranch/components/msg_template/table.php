<?PHP

class msg_templateTable extends TableLib_msg_templatesInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_msg_templates', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
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
