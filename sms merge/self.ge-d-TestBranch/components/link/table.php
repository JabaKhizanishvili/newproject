<?PHP

class LinkTable extends Tablelib_linksInterface
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
		if ( empty( $this->USER_ID ) )
		{
			$this->USER_ID = Users::GetUserID();
		}
		return true;

	}

}
