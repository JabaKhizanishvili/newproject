<?PHP

class F_salary_typeTable extends TableLib_f_salary_typesInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_f_salary_types', 'ID', 'sqs_f_salary_types.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->PAYMENT_TYPE = (int) trim( $this->PAYMENT_TYPE );
        $this->TAXES_YN = trim( $this->TAXES_YN );
//		$this->GROSS_NET = (int) trim( $this->GROSS_NET );
//		$this->ACCURACY_PERIOD = (int) trim( $this->ACCURACY_PERIOD );
		if ( $this->PAYMENT_TYPE < 0 )
		{
			return false;
		}
//		if ( $this->GROSS_NET < 0 )
//		{
//			return false;
//		}
//		if ( $this->ACCURACY_PERIOD < 0 )
//		{
//			return false;
//		}
        if( $this->TAXES_YN == '' ) {
            return false;
        }
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
