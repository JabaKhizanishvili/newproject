<?PHP

class GraphTable extends TableHrs_graphInterface
{
	public $_DATE_FIELDS = array(
			'REAL_DATE' => 'yyyy-mm-dd'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_graph', 'ID', 'library.nextval' );

	}

	public function SaveData()
	{
		$check = $this->_CheckData( $this->WORKER, $this->GT_DAY, $this->GT_YEAR, $this->TIME_ID );
		switch ( $check )
		{
			case false:
				return $this->_insertData( $this->WORKER, $this->GT_DAY, $this->GT_YEAR, $this->TIME_ID );
			default:
				return $this->_updateData( $this->WORKER, $this->GT_DAY, $this->GT_YEAR, $this->TIME_ID );
		}

	}

	protected function _CheckData( $worker, $day, $year )
	{
		$Query = 'select g.worker from ' . DB_SCHEMA . '.hrs_graph g '
						. ' where '
						. ' g.worker = ' . $worker
						. ' and g.gt_day = ' . $day
						. ' and g.gt_year = ' . $year
		;
		return DB::LoadResult( $Query );

	}

	protected function _insertData( $worker, $day, $year, $time_id )
	{
		$Query = 'insert into ' . DB_SCHEMA . '.hrs_graph (worker, gt_day, gt_year, time_id, real_date) '
						. ' values ('
						. $worker . ','
						. $day . ','
						. $year . ','
						. $time_id . ','
						. ' to_date(\'' . $this->REAL_DATE . '\', \'yyyy-mm-dd\') '
						. ')'
		;
		return DB::Insert( $Query );

	}

	protected function _updateData( $worker, $day, $year, $time_id )
	{
		$Query = 'update ' . DB_SCHEMA . '.hrs_graph g set '
						. ' g.time_id = ' . $time_id
						. ' where '
						. ' g.worker = ' . $worker
						. ' and g.gt_day = ' . $day
						. ' and g.gt_year = ' . $year
		;
		return DB::Update( $Query );

	}

}
