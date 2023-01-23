<?PHP

class MissionsTable extends TableHrs_applicationsInterface
{
	public $_DATE_FIELDS = array(
			'START_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'END_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'REC_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'APPROVE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'SYNC_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'DEL_DATE' => 'yyyy-mm-dd HH24:mi:ss'
	);

	public function __construct()
	{
		parent::__construct( 'hrs_applications', 'ID', 'procedures.nextval' );

	}

	public function check()
	{
		$this->START_DATE = trim( $this->START_DATE );
		$this->END_DATE = trim( $this->END_DATE );
		$this->REC_DATE = trim( $this->REC_DATE );
//        if (empty($this->FILES)) {
//            return false;
//        }
		if ( is_array( $this->FILES ) )
		{
			$this->FILES = implode( '|', $this->FILES );
		}
		else
		{
			$this->FILES = '';
		}
		if ( is_null( $this->STATUS ) )
		{
			$this->STATUS = 0;
		}
		if ( is_null( $this->APPROVE ) )
		{
			$this->APPROVE = 0;
		}
		if ( is_null( $this->SYNC ) )
		{
			$this->SYNC = 0;
		}
		if ( is_null( $this->DEL_USER ) )
		{
			$this->DEL_USER = 0;
		}
		if ( empty( $this->ORG ) )
		{
			return false;
		}
		if ( empty( $this->WORKER ) )
		{
			return false;
		}

		if ( empty( $this->START_DATE ) )
		{
			XError::setError( 'start date not entered!' );
			return false;
		}

		if ( empty( $this->END_DATE ) )
		{
			XError::setError( 'end date not entered!' );
			return false;
		}
		$StartDate = new PDate( $this->START_DATE );
		$EndDate = new PDate( $this->END_DATE );
		$RecDate = new PDate( $this->REC_DATE );

		$Now = new PDate();
		$NowUnx = new PDate( $Now->toFormat( '%Y-%m-%d 00:00:00' ) );
		$this->START_DATE = $StartDate->toFormat( '%Y-%m-%d 00:00:00' );
		$this->END_DATE = $EndDate->toFormat( '%Y-%m-%d 23:59:59' );
		$this->REC_DATE = $RecDate->toFormat( '%Y-%m-%d %H:%M:%S' );
		if ( !Helper::CheckTaskPermision( 'admin', 'wmissionsasworker' ) && $this->STATUS != 0 && $NowUnx->toUnix() > $StartDate->toUnix() )
		{
			XError::setError( 'Previous date of registration is not allowed to leave!' );
			return false;
		}
		$Add = '';
		if ( $this->ID )
		{
			$Add = ' and t.id !=' . $this->ID;
		}
		$Query = 'select count(1) '
						. ' from hrs_applications t '
						. ' where '
						. ' t.worker = ' . $this->WORKER
						. ' and t.org = ' . $this->ORG
						. ' and t.type = 7 '
						. ' and t.status > -1'
						. ' and to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') <= t.end_date '
						. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') >= t.start_date'
						. $Add
		;
		if ( DB::LoadResult( $Query ) )
		{
			XError::setError( 'mission alredy exists!' );
			return false;
		}
		$this->DAY_COUNT = Helper::CalculateDayCount( $this->START_DATE, $this->END_DATE );
		return true;

	}

}
