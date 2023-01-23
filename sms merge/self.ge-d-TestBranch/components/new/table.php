<?PHP

class newsTable extends TableNewsInterface
{
	public $_DATE_FIELDS = array(
			'MODIFY_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'PUBLISH_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'RECORD_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'UNPUBLISH_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'news', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->TITLE = trim( $this->TITLE );
		$this->INTROTEXT = trim( $this->INTROTEXT );
		$this->PUBLISH_DATE = trim( $this->PUBLISH_DATE );
		$this->UNPUBLISH_DATE = trim( $this->UNPUBLISH_DATE );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->MODIFY_DATE = PDate::Get()->toFormat();
		$this->MODIFY_USER = Users::GetUserID();
		if ( empty( $this->TITLE ) )
		{
			return false;
		}
		if ( empty( $this->TEXT0 ) )
		{
			return false;
		}
		if ( empty( $this->PUBLISH_DATE ) )
		{
			return false;
		}
		if ( empty( $this->RECORD_DATE ) )
		{
			$this->RECORD_DATE = PDate::Get()->toFormat();
		}
		if ( empty( $this->UNPUBLISH_DATE ) )
		{
			$this->UNPUBLISH_DATE = '2050-01-01';
		}
		$this->PUBLISH_DATE = PDate::Get( $this->PUBLISH_DATE )->toFormat();
		$this->UNPUBLISH_DATE = PDate::Get( $this->UNPUBLISH_DATE )->toFormat();
		return true;

	}

	public function SplitText( $SDATA )
	{
		$TDATAx = array();
		$Index = 0;
		$MaxLength = 3500;
		if ( strlen( $SDATA ) > $MaxLength )
		{
			$MData = explode( ' ', str_replace( '&nbsp;', ' ', trim( $SDATA ) ) );
			foreach ( $MData as $Ph )
			{
				$TDATAxT = C::_( $Index, $TDATAx ) . ' ' . $Ph;
				$TDATAx[$Index] = $TDATAxT;
				if ( strlen( $TDATAxT ) >= $MaxLength )
				{
					$Index++;
				}
			}
		}
		else
		{
			$TDATAx[] = $SDATA;
		}
		return $TDATAx;

	}

}
