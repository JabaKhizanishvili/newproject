<?PHP

class ConfigTable extends TableSystem_configInterface
{
	public function __construct()
	{
		parent::__construct( 'system_config', 'key' );

	}

	public function Clear( $option )
	{
		$query = 'delete ' . $this->_tbl . ' where c_scope=' . DB::Quote( $option );
		return DB::Query( $query );

	}

	public function Store( $updateNulls = false )
	{
		$R = $this->insertObject( $this->_tbl, $this );
		return $R;

	}

}
