<?PHP

class f_benefitTable extends TableLib_f_benefitsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_f_benefits', 'ID', 'sqs_f_benefits.nextval' );

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
		if ( $this->FIELDS )
		{
			$this->FIELDS = implode( ',', $this->FIELDS );
		}
		return true;

	}

}
