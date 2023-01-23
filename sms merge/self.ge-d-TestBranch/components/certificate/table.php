<?PHP

class certificateTable extends TableRel_worker_certInterface
{
	public $_DATE_FIELDS = array(
			'CERT_DATE' => 'yyyy-mm-dd',
			'CERT_DUE' => 'yyyy-mm-dd',
	);

	public function __construct()
	{
		parent::__construct( 'rel_worker_cert', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->WORKER = trim( $this->WORKER );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		if ( empty( $this->WORKER ) )
		{
			return false;
		}
		if ( empty( $this->CERT_DUE ) )
		{
			return false;
		}
		if ( empty( $this->CERT_NUMBER ) )
		{
			return false;
		}
		if ( empty( $this->CERT_DATE ) )
		{
			return false;
		}
		$this->CERT_DATE = PDate::Get( trim( $this->CERT_DATE ) )->toFormat( '%Y-%m-%d' );
		$this->CERT_DUE = PDate::Get( trim( $this->CERT_DUE ) )->toFormat( '%Y-%m-%d' );
		return true;

	}

}
