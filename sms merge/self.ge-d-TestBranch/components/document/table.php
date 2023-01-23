<?PHP

class DocumentTable extends TableLib_documents_uploadsInterface
{
	public function __construct()
	{
		parent::__construct( 'lib_documents_uploads', 'ID', 'sqs_documents_uploads.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
        $this->WORKER = trim( $this->WORKER );
        $this->MUST = trim($this->MUST);
        if ( empty( $this->WORKER ) )
        {
            return false;
        }
        if (empty($this->FILES)) {
            return false;
        }
        if ( is_array( $this->FILES ) )
        {
            $this->FILES = implode( '|', $this->FILES );
        }
        else
        {
            $this->FILES = '';
        }
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

}
