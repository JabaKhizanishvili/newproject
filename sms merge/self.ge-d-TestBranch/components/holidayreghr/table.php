<?PHP
require_once PATH_BASE . DS . 'components' . DS . 'workerholiday' . DS . 'table.php';

class HolidayUserTable extends HolidayTable
{
	public function check()
	{
		$this->START_DATE = trim( $this->START_DATE );
		$this->END_DATE = trim( $this->END_DATE );
		$this->REC_DATE = trim( $this->REC_DATE );
		if ( is_array( $this->FILES ) )
		{
			$this->FILES = implode( '|', $this->FILES );
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
		if ( !Helper::CheckGraphDays( $this->WORKER, $this->START_DATE, $this->END_DATE ) )
		{
			XError::setError( 'Graph Data Not exists!' );
			return false;
		}
		$StartDate = new PDate( $this->START_DATE );
		$EndDate = new PDate( $this->END_DATE );
		$RecDate = new PDate( $this->REC_DATE );
		$this->START_DATE = $StartDate->toFormat( '%Y-%m-%d 00:00:00' );
		$this->END_DATE = $EndDate->toFormat( '%Y-%m-%d 23:59:59' );
		$this->REC_DATE = $RecDate->toFormat( '%Y-%m-%d %H:%M:%S' );
		if ( $StartDate->toFormat( '%Y' ) != $EndDate->toFormat( '%Y' ) )
		{
			XError::setError( 'different year registration is not allowed to leave!' );
			return false;
		}
		$LimitsTable = new HolidayLimitsTable();
		if ( !$LimitsTable->CheckHolidayLimit( $this->DAY_COUNT, $this->TYPE, $this->WORKER, $this->START_DATE ) )
		{
			XError::setError( 'Holiday Limit  Exhausted!' );
			return false;
		}

		if ( empty( $this->ID ) )
		{
			$Query = 'select count(1) '
							. ' from hrs_applications t '
							. ' where '
							. ' t.worker = ' . $this->WORKER
							. ' and t.type in (0, 1) '
							. ' and t.status > -1 '
							. ' and to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') <= t.end_date '
							. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') >= t.start_date'
			;
			if ( DB::LoadResult( $Query ) )
			{
				XError::setError( 'leave Alredy Exists!' );
				return false;
			}
		}
		return true;

	}

}
