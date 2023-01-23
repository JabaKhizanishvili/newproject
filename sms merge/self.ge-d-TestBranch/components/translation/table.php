<?PHP

class TranslationTable extends TableHrs_translationsInterface
{
	public $_DATE_FIELDS = [
			'REC_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'CHANGE_DATE' => 'yyyy-mm-dd hh24:mi:ss',
			'MODIFY_DATE' => 'yyyy-mm-dd hh24:mi:ss',
	];

	public function __construct()
	{
		parent::__construct( 'hrs_translations', 'ID', 'sqs_hrs_translations.nextval' );

	}

	public function check()
	{
		$this->LIB_FROM = trim( $this->LIB_FROM );
		$this->LIB_TO = trim( $this->LIB_TO );
		$this->FROM_TEXT = trim( $this->FROM_TEXT );
		$this->TO_TEXT = trim( $this->TO_TEXT );
		$this->EDIT_TEXT = trim( $this->EDIT_TEXT );

		if ( empty( $this->EDIT_TEXT ) )
		{
			return false;
		}

		if ( empty( $this->LIB_FROM ) )
		{
			return false;
		}

		if ( empty( $this->LIB_TO ) )
		{
			return false;
		}

		if ( empty( $this->FROM_TEXT ) )
		{
			return false;
		}

		if ( empty( $this->TO_TEXT ) )
		{
			return false;
		}

		return true;

	}

}
