<?PHP

class HolidayTable extends TableHrs_applicationsInterface
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

		if ( is_array( $this->FILES ) )
		{
			$this->FILES = implode( '|', $this->FILES );
		}

		$appType = new TableLib_limit_app_typesInterface( 'lib_limit_app_types', 'ID' );
		$appType->load( $this->TYPE );
		if ( !$this->CheckValue( $this->REPLACING_WORKERS, $appType->REPLACER_FIELD ) )
		{
			return false;
		}
		if ( !$this->CheckValue( $this->W_HOLIDAY_COMMENT, $appType->COMMENT_FIELD ) )
		{
			return false;
		}
		if ( !$this->CheckValue( $this->FILES, $appType->FILES_FIELD ) )
		{
			return false;
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
		if ( empty( $this->WORKER ) )
		{
			return false;
		}

		if ( empty( $this->DAY_COUNT ) )
		{
			XError::setError( 'leave day Count is zero!' );
			return false;
		}
		if ( !Xhelp::checkDate( $this->START_DATE ) )
		{
			XError::setError( 'start date not entered!' );
			return false;
		}

		if ( !Xhelp::checkDate( $this->END_DATE ) )
		{
			XError::setError( 'end date not entered!' );
			return false;
		}
		if ( !Helper::CheckGraphDays( $this->WORKER, $this->START_DATE, $this->END_DATE ) )
		{
			XError::setError( 'Graph Data Not exists!' );
			return false;
		}
		$LimitsTable = new HolidayLimitsTable();
		if ( !$LimitsTable->CheckHolidayLimit( $this->DAY_COUNT, $this->TYPE, $this->WORKER, $this->START_DATE ) )
		{
			XError::setError( 'Holiday Limit  Exhausted!' );
			return false;
		}
		$StartDate = new PDate( $this->START_DATE );
		$EndDate = new PDate( $this->END_DATE );
		$RecDate = new PDate( $this->REC_DATE );
		if ( $StartDate->toFormat( '%Y' ) != $EndDate->toFormat( '%Y' ) )
		{
			XError::setError( 'different year registration is not allowed to leave!' );
			return false;
		}

		$Now = new PDate();
		$NowUnx = new PDate( $Now->toFormat( '%Y-%m-%d 00:00:00' ) );
		$this->START_DATE = $StartDate->toFormat( '%Y-%m-%d 00:00:00' );
		$this->END_DATE = $EndDate->toFormat( '%Y-%m-%d 23:59:59' );
		$this->REC_DATE = $RecDate->toFormat( '%Y-%m-%d %H:%M:%S' );

		if ( $NowUnx->toUnix() > $StartDate->toUnix() )
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
						. ' and t.type in (' . HolidayLimitsTable::GetHolidayIDx() . ') '
						. ' and to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') <= t.end_date '
						. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') >= t.start_date '
						. ' and t.status > -1 '
						. $Add
		;
		if ( DB::LoadResult( $Query ) )
		{
			XError::setError( 'leave Alredy Exists!' );
			return false;
		}



		return true;

	}

	public function CheckValue( &$Value, $Key )
	{
		$Value = trim( $Value );
		switch ( $Key )
		{
			case 0:
				$Value = null;
				break;
			case 1:
				return !empty( $Value );
		}
		return true;

	}

}
