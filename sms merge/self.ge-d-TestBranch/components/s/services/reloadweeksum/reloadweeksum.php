<?php

class reloadweeksum
{
	protected $Message = '';

	public function GetService()
	{
		$Enabled = Helper::getConfig( 'graph_show_week_times' );
		$Response = new stdClass();
		$Response->status = '';
		$Response->timesum = '';
		$Response->class = '';
		$Response->day = '0';
		if ( !$Enabled )
		{
			return json_encode( $Response );
		}
		$worker = (int) trim( Request::getVar( 'worker' ) );
		$day = (int) trim( Request::getVar( 'day' ) );
		$year = (int) trim( Request::getVar( 'year' ) );
		Request::setVar( 'format', 'json' );
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
		if ( empty( $year ) )
		{
			$Response->status = 0;
			return json_encode( $Response );
		}
		$Date = PDate::Get( XGraph::DayOfYear2Date( $year, $day ) );
		$WeekEnd = C::_( '1', XGraph::GetWeekStartEnd( $Date ) );
		$Response->status = 1;
		$Response->day = $WeekEnd->toFormat( '%j' );
		$RoutineTime = XGraph::GetWorkerWeekRate( $worker );
		$WeekTimeSum = XGraph::GetWorkerWeekHours( $worker, $Response->day, $year );
		if ( $RoutineTime >= $WeekTimeSum )
		{
			$Response->class = 'normal';
		}
		else
		{
			$Response->class = 'danger';
		}
		$Response->timesum = $RoutineTime . ' / ' . $WeekTimeSum . ' ' . Text::_( 'Hour' );
		return json_encode( $Response );

	}

}
