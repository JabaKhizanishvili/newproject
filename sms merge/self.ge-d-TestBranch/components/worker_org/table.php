<?PHP

class WorkersTable extends Tableslf_personsInterface
{
	public $_DATE_FIELDS = array(
			'CHANGE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'DELETE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'BIRTHDATE' => 'yyyy-mm-dd',
	);

	public function __construct()
	{
		parent::__construct( 'slf_persons', 'ID', 'users_sqs.nextval' );

	}

	public function check()
	{
		$this->FIRSTNAME = trim( $this->FIRSTNAME );
		$this->LASTNAME = trim( $this->LASTNAME );
		$this->LDAP_USERNAME = trim( $this->LDAP_USERNAME );
		$this->BIRTHDATE = trim( $this->BIRTHDATE );
		$this->USER_ROLE = (int) trim( $this->USER_ROLE );
		$this->CATEGORY_ID = (int) trim( $this->CATEGORY_ID );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->MODIFIED = time();
		$this->CHANGER_USER = Users::GetUserID();
		$Now = new PDate();
		$this->CHANGE_DATE = $Now->toFormat();
		if ( empty( $this->BIRTHDATE ) )
		{
			return false;
		}

		$this->BIRTHDATE = PDate::Get( $this->BIRTHDATE )->toFormat( '%Y-%m-%d' );
		if ( empty( $this->FIRSTNAME ) )
		{
			return false;
		}
		if ( empty( $this->LASTNAME ) )
		{
			return false;
		}

//		here
		$this->PRIVATE_NUMBER = trim( $this->PRIVATE_NUMBER );
		if ( $this->checkUnique( 'PRIVATE_NUMBER', $this->PRIVATE_NUMBER, $this->ID ) )
		{
			XError::setError( Text::_( 'PRIVATE_NUMBER' ) . ' ' . Text::_( 'USED' ) );
			return false;
		}
		$this->LDAP_USERNAME = trim( $this->LDAP_USERNAME );
		if ( $this->checkUnique( 'LDAP_USERNAME', $this->LDAP_USERNAME, $this->ID ) )
		{
			XError::setError( Text::_( 'LDAP USERNAME' ) . ' ' . Text::_( 'USED' ) );
			return false;
		}
//		here

		return true;

	}

}
