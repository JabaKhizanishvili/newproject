<?PHP

class LanguageTable extends TableLib_languagesInterface
{
	public $_DATE_FIELDS = [
			'CHANGE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
	];

	public function __construct()
	{
		parent::__construct( 'lib_languages', 'ID', 'sqs_lib_languages.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_CODE = mb_strtolower( trim( $this->LIB_CODE ) );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = intval( $this->ACTIVE );
		if ( empty( $this->LIB_CODE ) )
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
