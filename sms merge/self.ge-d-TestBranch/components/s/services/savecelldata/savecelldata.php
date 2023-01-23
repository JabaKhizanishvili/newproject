<?php

class savecelldata
{
	protected $Message = '';

	public function GetService()
	{
		$worker = (int) trim( Request::getVar( 'worker' ) );
		$day = (int) trim( Request::getVar( 'day' ) );
		$year = (int) trim( Request::getVar( 'year' ) );
		$time_id = (int) trim( Request::getVar( 'time_id' ) );
		$Confirm = (int) trim( Request::getVar( 'confirm', 1 ) );
		Request::setVar( 'format', 'json' );
		$Response = new stdClass();
		$Response->status = '';
		$Response->message = '';
		if ( empty( $worker ) )
		{
			$Response->status = 0;
			return json_encode( $Response );
		}
		if ( empty( $day ) )
		{
			$Response->status = 0;
			return json_encode( $Response );
		}

		if ( $time_id != 0 )
		{
			$Validate = new XGraphs();
			$Date = $this->DayOfYear2Date( $year, $day );
			$WeekRoutine = (int) Helper::getConfig( 'graph_show_week_times_alert' );
			if ( $WeekRoutine == 1 )
			{
				$Result = $Validate->CheckWeekTimeLimit( $worker, $Date, $time_id );
				if ( !C::_( 'Result', $Result ) && $Confirm < 1 )
				{
					$Response->status = -1;
					$Response->message = C::_( 'msg', $Result ) . ' ' . Text::_( 'Confirm And Continue?' );
					$Response->confirm = 1;
					return json_encode( $Response );
				}

				$Result2 = $Validate->CheckTimeIntervals( $worker, $Date, $time_id );
				if ( !C::_( 'Result', $Result2 ) && $Confirm < 2 )
				{
					$Response->status = -1;
					$Response->message = C::_( 'msg', $Result2 );
					$Response->confirm = 2;
					return json_encode( $Response );
				}

				$Result3 = $Validate->CheckRestDay( $worker, $Date, $time_id );
				if ( !C::_( 'Result', $Result3 ) && $Confirm < 3 )
				{
					$Response->status = -1;
					$Response->message = C::_( 'msg', $Result3 );
					$Response->confirm = 3;
					return json_encode( $Response );
				}
			}
			elseif ( $WeekRoutine == 2 )
			{
				$Result = $Validate->CheckWeekTimeLimit( $worker, $Date, $time_id );
				if ( !C::_( 'Result', $Result ) )
				{
					$Response->status = 0;
					$Response->message = C::_( 'msg', $Result );
					return json_encode( $Response );
				}

				$Result2 = $Validate->CheckTimeIntervals( $worker, $Date, $time_id );
				if ( !C::_( 'Result', $Result2 ) )
				{
					$Response->status = 0;
					$Response->message = C::_( 'msg', $Result2 );
					return json_encode( $Response );
				}

				$Result3 = $Validate->CheckRestDay( $worker, $Date, $time_id );
				if ( !C::_( 'Result', $Result3 ) )
				{
					$Response->status = 0;
					$Response->message = C::_( 'msg', $Result3 );
					return json_encode( $Response );
				}
			}
		}
		$Response->status = (int) $this->SaveData( $worker, $day, $year, $time_id );
		$Response->message = $this->Message;
		XRedis::CleanDBCache( 'HRS_GRAPH' );
		return json_encode( $Response );

	}

	public function SaveData( $worker, $day, $year, $time_id )
	{
		$OldTimeData = $this->_CheckData( $worker, $day, $year );
		$OldTimeID = C::_( 'TIME_ID', $OldTimeData, false );
		$Now = PDate::Get();
		$Days = [];
		switch ( $OldTimeData )
		{
			case false:
				{
					if ( $this->validateDates( $year, $day, $time_id ) )
					{
						$Res = $this->_insertData( $worker, $day, $year, $time_id );
						$NewDates = $this->_getTimeDataFromID( $time_id, $this->DayOfYear2Date( $year, $day ) );
						if ( $time_id && $NewDates->START_TIME->toUnix() < $Now->toUnix() )
						{
							$Days = array_flip( array_flip( [
									$NewDates->START_TIME->toFormat( '%Y-%m-%d' ),
									$NewDates->END_TIME->toFormat( '%Y-%m-%d' )
											] ) );
						}
					}
				}
			default:
				{
					if ( $OldTimeID == $time_id )
					{
						return 1;
					}
					if ( $this->validateDates( $year, $day, $time_id ) && $this->validateDates( $year, $day, $OldTimeID ) )
					{
						$OldDates = $this->_getTimeDataFromID( $OldTimeID, $this->DayOfYear2Date( $year, $day ) );
						if ( $OldDates->START_TIME->toUnix() < $Now->toUnix() )
						{
							$queryTransported = 'DELETE FROM hrs_transported_data WHERE '
											. ' user_id = ' . $worker
											. ' AND rec_date between TO_DATE(\'' . $OldDates->START_TIME->toFormat() . '\',\'yyyy-mm-dd HH24:MI:SS\') AND TO_DATE(\'' . $OldDates->END_TIME->toFormat() . '\',\'yyyy-mm-dd HH24:MI:SS\')'
											. ' AND door_type >1000 '
							;
							$queryEvents = 'DELETE FROM hrs_staff_events WHERE '
											. ' staff_id = ' . $worker
											. ' AND event_date between TO_DATE(\'' . $OldDates->START_TIME->toFormat() . '\',\'yyyy-mm-dd HH24:MI:SS\') AND TO_DATE(\'' . $OldDates->END_TIME->toFormat() . '\',\'yyyy-mm-dd HH24:MI:SS\')'
											. ' AND real_type_id >1000 '
							;
							$DeleteRestApp = 'DELETE FROM hrs_applications '
											. ' WHERE '
											. ' worker = ' . $worker
											. ' AND start_Date BETWEEN TO_DATE(\'' . $OldDates->START_TIME->toFormat() . '\',\'yyyy-mm-dd HH24:MI:SS\') AND TO_DATE(\'' . $OldDates->END_TIME->toFormat() . '\',\'yyyy-mm-dd HH24:MI:SS\')'
											. ' AND type= 10 '
							;
							DB::Delete( $queryTransported );
							DB::Delete( $queryEvents );
							DB::Delete( $DeleteRestApp );
							$NewDates = $this->_getTimeDataFromID( $time_id, $this->DayOfYear2Date( $year, $day ) );
							$Days = array_flip( array_flip( [
									$OldDates->START_TIME->toFormat( '%Y-%m-%d' ),
									$OldDates->END_TIME->toFormat( '%Y-%m-%d' ),
									$NewDates->START_TIME->toFormat( '%Y-%m-%d' ),
									$NewDates->END_TIME->toFormat( '%Y-%m-%d' )
											] ) );
						}
						$Res = $this->_updateData( $worker, $day, $year, $time_id );
					}
					else
					{
//						$queryEventsHist = 'INSERT INTO hrs_staff_events_hist values ( hrs_staff_events_hist1.NEXTVAL, '
//										. $year . ' , '
//										. $day . ' , '
//										. $time_id . ' , '
//										. $OldTimeID . ' , '
//										. Users::GetUserID() . ' , '
//										. 'sysdate'
//										. ')'
//						;
//						echo '<pre><pre>';
//						print_r( $queryEventsHist );
//						echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
//						die;
//
//						DB::Insert( $queryEventsHist );
//						$this->Message = Text::_( 'Graph Data Save ERROR!' );
						return false;
					}

					foreach ( $Days as $Day )
					{
						XGraph::RecalculateOldEvents( $worker, $Day, $Day, true );
					}
					return $Res;
				}
		}
		return false;

	}

	protected function _CheckData( $worker, $day, $year )
	{
		$Query = 'select g.worker, g.gt_day, g.gt_year, g.time_id, g.real_date from ' . DB_SCHEMA . '.hrs_graph g '
						. ' where '
						. ' g.worker = ' . $worker
						. ' and g.gt_day = ' . $day
						. ' and g.gt_year = ' . $year
		;
		return DB::LoadObject( $Query );

	}

	protected function _getStartTimeFromTimeId( $timeId )
	{
		$query = 'SELECT a.start_time FROM lib_graph_times a WHERE id= ' . $timeId;
		return DB::LoadResult( $query );

	}

	protected function _getEndTimeFromTimeId( $timeId )
	{
		$query = 'SELECT a.end_time FROM lib_graph_times a WHERE id= ' . $timeId;
		return DB::LoadResult( $query );

	}

	protected function _getTimeDataFromID( $timeId, $Date )
	{
		static $Times = [];
		$Key = md5( json_encode( func_get_args() ) );
		if ( !isset( $Times[$Key] ) )
		{
			$Response = new stdClass();
			if ( $timeId )
			{
				$query = 'SELECT a.* FROM lib_graph_times a WHERE id= ' . $timeId;
				$TimeData = DB::LoadObject( $query );
				$Response->START_TIME = PDate::Get( $Date . ' ' . C::_( 'START_TIME', $TimeData ) . ':00' );
				$Response->END_TIME = PDate::Get( $Date . ' ' . C::_( 'END_TIME', $TimeData ) . ':00' );
				$START_BREAK = C::_( 'START_BREAK', $TimeData, false );
				if ( !empty( $START_BREAK ) )
				{
					$Response->START_BREAK = PDate::Get( $Date . ' ' . $START_BREAK . ':00' );
					if ( $Response->START_BREAK->toUnix() < $Response->START_TIME->toUnix() )
					{
						$Response->START_BREAK = PDate::Get( $Response->START_BREAK . ' + 1 day' );
					}
				}
				$END_BREAK = C::_( 'END_BREAK', $TimeData );
				if ( !empty( $END_BREAK ) )
				{
					$Response->END_BREAK = PDate::Get( $Date . ' ' . $END_BREAK . ':00' );
					if ( $Response->END_BREAK->toUnix() < $Response->START_BREAK->toUnix() )
					{
						$Response->END_BREAK = PDate::Get( $Response->END_BREAK . ' + 1 day' );
					}
				}
				if ( $Response->END_TIME->toUnix() < $Response->START_TIME->toUnix() )
				{
					$Response->END_TIME = PDate::Get( $Response->END_TIME . ' + 1 day' );
				}
			}
			else
			{
				$Response->START_TIME = PDate::Get( $Date . ' ' . '00:00:00' );
				$Response->END_TIME = PDate::Get( $Date . ' ' . '00:00:00' );
			}
			$Times[$Key] = $Response;
		}
		return $Times[$Key];

	}

	protected function _insertData( $worker, $day, $year, $time_id )
	{
		$slf_worker = Xhelp::getWorker_sch( $worker );
		$Res = (bool) GraphJob::insert_graph_data( $slf_worker, $this->DayOfYear2Date( $year, $day ), $time_id, 0 );
		$NewDates = $this->_getTimeDataFromID( $time_id, $this->DayOfYear2Date( $year, $day ) );
		if ( $Res && $time_id && $NewDates->START_TIME->toUnix() < PDate::Get()->toUnix() )
		{
			XGraph::InsertOldDayEvent( $worker, $time_id, $NewDates->START_TIME->toFormat(), 2000 );
			XGraph::InsertOldDayEvent( $worker, $time_id, $NewDates->END_TIME->toFormat(), 3500 );
			$BStart = C::_( 'START_BREAK', $NewDates );
			if ( $BStart )
			{
				XGraph::InsertOldDayEvent( $worker, $time_id, $BStart->toFormat(), 2500 );
			}
			$BEnd = C::_( 'END_BREAK', $NewDates );
			if ( $BEnd )
			{
				XGraph::InsertOldDayEvent( $worker, $time_id, $BEnd->toFormat(), 3000 );
			}
		}

	}

	protected function _updateData( $worker, $day, $year, $time_id )
	{
		$slf_worker = Xhelp::getWorker_sch( $worker );
		$Res = (bool) GraphJob::update_graph_data( $slf_worker, $this->DayOfYear2Date( $year, $day ), $time_id, 0 );
		$NewDates = $this->_getTimeDataFromID( $time_id, $this->DayOfYear2Date( $year, $day ) );
		if ( $Res && $time_id && $NewDates->START_TIME->toUnix() < PDate::Get()->toUnix() )
		{
			XGraph::InsertOldDayEvent( $worker, $time_id, $NewDates->START_TIME->toFormat(), 2000 );
			XGraph::InsertOldDayEvent( $worker, $time_id, $NewDates->END_TIME->toFormat(), 3500 );
			$BStart = C::_( 'START_BREAK', $NewDates );
			if ( $BStart )
			{
				XGraph::InsertOldDayEvent( $worker, $time_id, $BStart->toFormat(), 2500 );
			}
			$BEnd = C::_( 'END_BREAK', $NewDates );
			if ( $BEnd )
			{
				XGraph::InsertOldDayEvent( $worker, $time_id, $BEnd->toFormat(), 3000 );
			}
		}
		return $Res;

//		$Query = 'update ' . DB_SCHEMA . '.hrs_graph g set '
//						. ' g.time_id = ' . $time_id
//						. ' ,g.change_woker = ' . Users::GetUserID()
//						. ' ,g.change_date = SYSDATE '
//						. ' where '
//						. ' g.worker = ' . $worker
//						. ' and g.gt_day = ' . $day
//						. ' and g.gt_year = ' . $year
//		;
//		return DB::Update( $Query );

	}

	protected function DayOfYear2Date( $year, $DayInYear )
	{
		$d = new DateTime( $year . '-01-01' );
		date_modify( $d, '+' . ($DayInYear - 1) . ' days' );
		return $d->format( 'Y-m-d' );

	}

	protected function validateDates( $year, $day, $time_id )
	{
		if ( $time_id == 0 )
		{
			return true;
		}
		$StartTime = $this->_getStartTimeFromTimeId( $time_id );
		$TimeLimit = Helper::getConfig( 'graph_change_limit' );
		if ( $TimeLimit == '' )
		{
			$TimeLimit = 300;
		}
		if ( GRAPH_FREE_EDIT == 1 )
		{
			return true;
		}

		$Date = PDate::Get( PDate::Get( PDate::Get( $this->DayOfYear2Date( $year, $day ) . ' ' . $StartTime )->toUnix() + ($TimeLimit * 60) )->toFormat() );
		if ( PDate::Get()->toUnix() <= $Date->toUnix() )
		{
			return true;
		}
		else
		{
			$this->Message = Text::_( 'Graph Data Save ERROR!' ) . ' ' . Text::_( 'GRAPH_CHANGE_LIMIT' . $TimeLimit ) . '!';
			return false;
		}

	}

}
