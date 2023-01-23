<?PHP

class GlobalLinkTable extends Tablelib_linksInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_links', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->TITLE = trim( $this->TITLE );
		$this->ACTIVE = 1;
		if ( empty( $this->TITLE ) )
		{
			return false;
		}
		$this->USER_ID = 0;
		return true;

	}

}
