<?php
define( 'X_WEEK_WORKING_TIME', 40 );

class XGraphs extends XObject
{
	protected $WeekTimeLimit = 40;
	protected $TimeBetween = 12;
	protected $RestHours = 24;

	public function __construct()
	{
		$Limit = Helper::getConfig( 'week_working_hour_limit' );
		if ( $Limit )
		{
			$this->WeekTimeLimit = floatval( $Limit );
		}
		$Time = Helper::getConfig( 'time_between_times' );
		if ( $Time )
		{
			$this->TimeBetween = floatval( $Time );
		}

	}

	protected function _doAction( $actionState )
	{
		if ( isset( $actionState->TYPE ) )
		{
			switch ( $actionState->TYPE )
			{
				case '1':
					// რეგისტრაციის აკრძალვა
					break;
				case '2':
					// შეწყვეტა
					break;
				case '3':
					// გაუქმება
					break;
				default:
					break;
			}
			return false;
		}
		else
		{
			return $this->_doTable();
		}

	}

	public function getAppState( $worker, $appType, $startDate = null, $endDate = null )
	{
		$sDate = PDate::Get( $startDate );
		$eDate = PDate::Get( $endDate );

		$where = array();
		$where[] = ' t.regapp = ' . $appType;
		$where[] = ' t.active = 1 ';
		$where[] = ' a.id IS NOT NULL ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$query = 'SELECT '
						. 't.actionapp, '
						. 't.type '
						. 'FROM '
						. 'rel_applications t '
						. 'LEFT JOIN ( '
						. 'SELECT '
						. '* '
						. 'FROM '
						. 'hrs_applications b '
						. 'WHERE '
						. 'b.worker = ' . $worker . ' '
						. 'AND (to_date(' . DB::Quote( $sDate->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\') BETWEEN b.start_date AND b.end_date '
						. 'OR to_date(' . DB::Quote( $eDate->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\') BETWEEN b.start_date AND b.end_date) '
						. ') a ON a.type = t.actionapp '
						. $whereQ;

		$actionAppState = DB::LoadObject( $query );
		return $actionAppState;

	}

	public function doTable()
	{

		// ცხრილის შევსება ჩასაწერად
		if ( !$this->_table->bind( $this->_data ) )
		{
			return false;
		}

		// ცხრილის შემოწმება (ვალიდაცია)
		if ( !$this->_table->check() )
		{
			return false;
		}

		// შევსებული ცხრილის ჩაწერა (Insert)
		if ( !$this->_table->store() )
		{
			return false;
		}
		return $this->_table->insertid();

	}

	public function GetTimeData( $TimeID )
	{
		$query = 'SELECT a.* FROM lib_graph_times a WHERE id= ' . $TimeID;
		return DB::LoadObject( $query );

	}

	public function CheckTime( $Worker, $DateStr, $TimeID )
	{
		$Date = PDate::Get( $DateStr );
		$Week = $Date->toFormat( '%W' );
		$Year = $Date->toFormat( '%Y' );
		$Dates = $this->WeekStartEnd( $Week, $Year );

		$this->GetWorkerGraph( $Worker, C::_( 'start', $Dates ), C::_( 'end', $Dates ) );

		echo '<pre><pre>';
		print_r( $Date );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
		die;

	}

	public function WeekStartEnd( $Week, $Year )
	{
		$dto = new DateTime();
		$dto->setISODate( $Year, $Week );
		$ret['start'] = pdate::Get( $dto->format( 'Y-m-d' ) );
		$dto->modify( '+6 days' );
		$ret['end'] = PDate::Get( $dto->format( 'Y-m-d' ) );
		return $ret;

	}

	public function GetTimeDatsssa( $TimeID )
	{
		$query = 'SELECT a.* FROM lib_graph_times a WHERE id= ' . $TimeID;
		return DB::LoadObject( $query );

	}


	public function CheckWeekTimeLimit( $Worker, $DateIn, $TimeID )
	{
		$Date = new PDate( $DateIn );
		$Dates = XGraph::GetWeekStartEnd( $Date->toFormat() );
		$CurrentDaySum = 0;
		$EndDate = PDate::Get( C::_( '1', $Dates ) );
		if ( $TimeID )
		{
			$CurrentDaySum = self::GetTimeSum( $TimeID );
		}
		else
		{
			return array(
					'Result' => true,
					'msg' => null
			);
		}
		$Hours = floatval( XGraph::GetWorkerWeekHours( $Worker, $EndDate->toFormat( '%j' ), $EndDate->toFormat( '%Y' ), $Date->toFormat( '%Y-%m-%d' ) ) ) + $CurrentDaySum;
		$WeekRate = XGraph::GetWorkerWeekRate( $Worker );

		if ( $Hours > $WeekRate )
		{
			return array(
					'Result' => false,
					'msg' => Text::_( 'ERROR 40 hour Limit' )
			);
		}
		else
		{
			return array(
					'Result' => true,
					'msg' => null
			);
		}

	}

	public static function GetTimeSum( $TimeID )
	{
		$query = 'SELECT a.working_time FROM lib_graph_times a WHERE id= ' . $TimeID;
		return (float) DB::LoadResult( $query );

	}

	public function CheckTimeIntervals( $Worker, $DateIn, $TimeID )
	{
		return array(
				'Result' => true,
				'msg' => null
		);
		$Date = new PDate( $DateIn );
		$Params = array(
				':v_worker_id' => $Worker,
				':v_day' => $Date->toFormat( '%Y-%m-%d' ),
				':v_time_id' => $TimeID
		);
		$Hours = floatval( DB::callFunction( 'hrs_chday_rest12h', $Params ) );

		if ( $Hours < $this->TimeBetween )
		{
			return array(
					'Result' => false,
					'msg' => Text::_( 'ERROR 12 hour Limit' )
			);
		}
		else
		{
			return array(
					'Result' => true,
					'msg' => null
			);
		}

	}

	public function CheckRestDay( $Worker, $DateIn, $TimeID )
	{
		return array(
				'Result' => true,
				'msg' => null
		);
		$Date = new PDate( $DateIn );
		$Params = array(
				':v_worker_id' => $Worker,
				':v_day' => $Date->toFormat( '%Y-%m-%d' ),
				':v_time_id' => $TimeID
		);
		$Hours = floatval( DB::callFunction( 'hrs_chday_rest24h', $Params ) );
		if ( $Hours < $this->RestHours )
		{
			return array(
					'Result' => false,
					'msg' => Text::_( 'ERROR Rest Day Limit' )
			);
		}
		else
		{
			return array(
					'Result' => true,
					'msg' => null
			);
		}

	}

}
