<?PHP

class PersonTable extends TableSlf_personsInterface
{
	public $_DATE_FIELDS = array(
			'CHANGE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'DELETE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'BIRTHDATE' => 'yyyy-mm-dd',
	);

	public function __construct()
	{
		parent::__construct( 'slf_persons', 'ID', 'sqs_slf_person.nextval' );

	}

	public function check()
	{
		$this->FIRSTNAME = trim( $this->FIRSTNAME );
		$this->LASTNAME = trim( $this->LASTNAME );
		$this->FATHER_NAME = trim( $this->FATHER_NAME );
		$this->BIRTHDATE = trim( $this->BIRTHDATE );
		$this->IBAN = trim( $this->IBAN );
		$this->PERMIT_ID = trim( $this->PERMIT_ID );
		$this->PRIVATE_NUMBER = trim( $this->PRIVATE_NUMBER );
		$this->LDAP_USERNAME = trim( $this->LDAP_USERNAME );
		$this->MOBILE_PHONE_NUMBER = trim( $this->MOBILE_PHONE_NUMBER );
		$this->PERMIT_ID = trim( $this->PERMIT_ID );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->GENDER = (int) trim( $this->GENDER );
		$this->NATIONALITY = (int) trim( $this->NATIONALITY );
		$this->ACCESSS_TYPE = (int) trim( $this->ACCESSS_TYPE );
//		$this->ACCOUNTING_OFFICES = (int) trim( $this->ACCOUNTING_OFFICES );
		$this->USER_ROLE = (int) trim( $this->USER_ROLE );
		$this->COUNTING_TYPE = (int) trim( $this->COUNTING_TYPE );
		$this->TIMECONTROL = (int) trim( $this->TIMECONTROL );
		$this->LIVELIST = (int) trim( $this->LIVELIST );
		$this->SMS_REMINDER = (int) trim( $this->SMS_REMINDER );
		$this->SMS_WORKER_LATENESS = (int) trim( $this->SMS_WORKER_LATENESS );
		$this->BIRTHDATE = PDate::Get( $this->BIRTHDATE )->toFormat( '%Y-%m-%d' );

		if ( is_array( $this->FILES ) )
		{
			$this->FILES = implode( '|', $this->FILES );
		}
		else
		{
			$this->FILES = '';
		}
		if ( empty( $this->FIRSTNAME ) )
		{
			return false;
		}
		if ( empty( $this->LASTNAME ) )
		{
			return false;
		}
		if ( empty( $this->BIRTHDATE ) )
		{
			return false;
		}
		if ( empty( $this->PRIVATE_NUMBER ) )
		{
			return false;
		}
		if ( empty( $this->LDAP_USERNAME ) )
		{
			return false;
		}

		if ( empty( $this->USER_ROLE ) )
		{
			return false;
		}
		if ( $this->EMAIL && !filter_var( $this->EMAIL, FILTER_VALIDATE_EMAIL ) )
		{
			XError::setError( Text::_( 'Invalid email!' ) );
			return false;
		}

		$PermitID = Helper::CleanArray( explode( '|', $this->PERMIT_ID ), 'Str' );
		foreach ( $PermitID as $Permit )
		{
			if ( $this->checkPermitUnique( $Permit, $this->ID ) )
			{
				XError::setError( Text::_( 'PERMIT_ID' ) . ' ' . Text::_( 'USED' ) );
				return false;
			}
		}
		$this->PERMIT_ID = implode( '|', $PermitID );
		if ( $this->checkUnique( 'PRIVATE_NUMBER', $this->PRIVATE_NUMBER, $this->ID ) )
		{
			XError::setError( Text::_( 'PRIVATE_NUMBER' ) . ' ' . Text::_( 'USED' ) );
			return false;
		}
		if ( $this->checkUnique( 'LDAP_USERNAME', $this->LDAP_USERNAME, $this->ID ) )
		{
			XError::setError( Text::_( 'LDAP USERNAME' ) . ' ' . Text::_( 'USED' ) );
			return false;
		}
		return true;

	}

}
