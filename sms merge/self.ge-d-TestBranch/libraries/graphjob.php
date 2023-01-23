<?php

class GraphJob
{
	public static $XHRSTable = null;
	public static $date_fields = [
			'START_TIME' => 'yyyy-mm-dd hh24:mi:ss',
			'END_TIME' => 'yyyy-mm-dd hh24:mi:ss',
			'START_BREAK' => 'yyyy-mm-dd hh24:mi:ss',
			'END_BREAK' => 'yyyy-mm-dd hh24:mi:ss',
			'REAL_DATE' => 'yyyy-mm-dd',
			'CHANGE_DATE' => 'yyyy-mm-dd hh24:mi:ss'
	];

	public static function fill_sub_graph( $day_back = 0 )
	{
		$Q = 'select '
						. ' g.*, '
						. ' to_char(g.real_date, \'yyyy-mm-dd\') realdate'
						. ' from '
						. ' hrs_graph g '
						. ' where '
						. ' g.real_date = trunc(sysdate) - ' . (int) $day_back
		;

		$graphs = DB::LoadObjectList( $Q, 'WORKER' );
		if ( empty( $graphs ) )
		{
			return false;
		}

		$events = self::get_events( $day_back );
		if ( empty( $events ) )
		{
			return false;
		}

		self::$XHRSTable = new XHRSTable();
		$day_workers = self::sub_graph_day_workers( $day_back );
		$CommonHolidays = Helper::GetAllHoldays();
		$DATA = [];
		foreach ( $graphs as $worker => $gdata )
		{
			$WorkerEvents = C::_( $worker, $events, [] );
			if ( empty( $WorkerEvents ) )
			{
				continue;
			}

			$time_min = 0;
			$real_date = C::_( 'REALDATE', $gdata );
			$night_time = C::_( 'NIGHT_TIME', $gdata );
			$rest_time = C::_( 'REST_TIME', $gdata );
			$working_time = C::_( 'WORKING_TIME', $gdata );

			$start_time = C::_( 'START_TIME', $gdata );
			$START = PDate::Get( $start_time );
			$S_day = PDate::Get( $START->toFormat( '%Y-%m-%d' ) );

			$b_start_time = C::_( 'START_BREAK', $gdata );
			$B_START = PDate::Get( $b_start_time );

			$b_end_time = C::_( 'END_BREAK', $gdata );
			$B_END = PDate::Get( $b_end_time );

			$end_time = C::_( 'END_TIME', $gdata );
			$END = PDate::Get( $end_time );
			$E_day = PDate::Get( $END->toFormat( '%Y-%m-%d' ) );

			$time_min_1 = 0;
			$time_min_2 = 0;
			$ddate = PDate::Get( $START->toFormat( '%Y-%m-%d 23:59:59' ) );

			foreach ( $WorkerEvents as $data )
			{
				$event_date = PDate::Get( $data->EVENT_DATE );
				if ( $event_date->toUnix() <= $ddate->toUnix() )
				{
					$time_min_1 += (int) $data->TIME_MIN;
				}

				if ( $S_day->toUnix() < $E_day->toUnix() && $event_date->toUnix() > $ddate->toUnix() )
				{
					$time_min_2 += (int) $data->TIME_MIN;
				}
			}

			$Lateness = ($time_min_1 + $time_min_2) / 60;
			$Worked = $working_time - $Lateness;
			$holiday_hours = 0;
			if ( in_array( $real_date, $CommonHolidays ) )
			{
				$holiday_hours += $Worked;
			}

			$add_data = [
					'WORKER' => $worker,
					'REAL_DATE' => $real_date
			];

			if ( $S_day->toUnix() < $E_day->toUnix() )
			{
				$add_data['LATENESS'] = Helper::FormatBalance( $time_min_1 / 60 );
				$DATA[] = self::day_part( 1, $START, $B_START, $B_END, $END, $add_data, $CommonHolidays );

				$add_data['LATENESS'] = Helper::FormatBalance( $time_min_2 / 60 );
				$DATA[] = self::day_part( 2, $START, $B_START, $B_END, $END, $add_data, $CommonHolidays );
			}
			else
			{
				$DATA[] = [
						'WORKER' => $worker,
						'REAL_DATE' => $real_date,
						'REST_TIME' => Helper::FormatBalance( $rest_time, 2 ),
						'WORK_TIME' => Helper::FormatBalance( $Worked, 2 ),
						'LATENESS' => Helper::FormatBalance( $Lateness, 2 ),
						'NIGHT_HOURS' => Helper::FormatBalance( $night_time, 2 ),
						'HOLIDAY_HOURS' => Helper::FormatBalance( $holiday_hours, 2 )
				];
			}
		}

		foreach ( $DATA as $data )
		{
			if ( in_array( $worker, $day_workers ) )
			{
				continue;
			}

			self::SaveData( $data, self::$date_fields, 'hrs_sub_graph' );
		}

		return true;

	}

	public function day_part( $part = 0, $START = null, $B_START = null, $B_END = null, $END = null, $add_data = [], $CommonHolidays = [] )
	{
		$day = $START->toFormat( '%Y-%m-%d' );
		$StartHour = $START->toFormat( '%H' );
		$StartMin = $START->toFormat( '%M' );

		$BreakStartHour = $B_START->toFormat( '%H' );
		$BreakStartMin = $B_START->toFormat( '%M' );
		$BreakEndHour = $B_END->toFormat( '%H' );
		$BreakEndMin = $B_END->toFormat( '%M' );

		$EndHour = $END->toFormat( '%H' );
		$EndMin = $END->toFormat( '%M' );

		$NightStartHour = 22;
		$NightStartMin = 0;
		$NightEndHour = 6;
		$NightEndMin = 0;

		$next_day = PDate::Get( $END->toFormat( '%Y-%m-%d' ) );
		if ( $part == 1 )
		{
			$EndHour = 24;
			$EndMin = 0;
			$NightEndHour = 24;
			if ( $next_day->toUnix() > $B_START->toUnix() )
			{
				$BreakEndHour = 24;
				$BreakEndMin = 0;
			}
		}

		if ( $part == 2 )
		{
			$day = $END->toFormat( '%Y-%m-%d' );
			$StartHour = 0;
			$StartMin = 0;
			$NightStartHour = 0;

			if ( $next_day->toUnix() > $B_START->toUnix() )
			{
				$BreakStartHour = 0;
				$BreakStartMin = 0;
			}
		}

		$w = self::$XHRSTable->getHoursForSingleDay( $StartHour, $StartMin, $EndHour, $EndMin, 0, 0, $NightEndHour, $NightEndMin );
		$break = self::$XHRSTable->getHoursForSingleDay( $BreakStartHour, $BreakStartMin, $BreakEndHour, $BreakEndMin, $NightStartHour, $NightStartMin, $NightEndHour, $NightEndMin );
		$night = self::$XHRSTable->getHoursForSingleDay( $StartHour, $StartMin, $EndHour, $EndMin, $NightStartHour, $NightStartMin, $NightEndHour, $NightEndMin );

		$l = C::_( 'LATENESS', $add_data, 0 );
		$lateness = $l > 0 ? $l : 0;
		$worked = $w - $break - $lateness;

		$holiday = '';
		if ( in_array( $day, $CommonHolidays ) )
		{
			$holiday = $worked;
		}

		$data = [
				'WORK_TIME' => Helper::FormatBalance( $worked, 2 ),
				'REST_TIME' => Helper::FormatBalance( $break, 2 ),
				'NIGHT_HOURS' => Helper::FormatBalance( $night, 2 ),
				'HOLIDAY_HOURS' => Helper::FormatBalance( $holiday, 2 )
		];

		$DATA = array_merge( $add_data, $data );
		return $DATA;

	}

	public function get_events( $day_back = 0 )
	{
		$Q = ' select '
						. ' e.* '
						. ' from hrs_staff_events e '
						. ' where '
						. ' trunc(e.event_date) between trunc(sysdate) - ' . (int) $day_back . ' and trunc(sysdate) - ' . (int) ($day_back - 1)
						. ' order by e.id asc'
		;
		$data = DB::LoadObjectList( $Q );
		$collect = [];
		foreach ( $data as $value )
		{
			$worker = $value->STAFF_ID;
			$collect[$worker][] = $value;
		}

		return $collect;

	}

	public function sub_graph_day_workers( $day_back = 0 )
	{
		$Q = 'select '
						. ' g.worker '
						. ' from hrs_sub_graph g '
						. ' where '
						. ' g.real_date = trunc(sysdate) - ' . (int) $day_back
						. ' group by g.worker '
		;
		return DB::LoadList( $Q );

	}

	public static function updateStandardGraphData( $config_days = 1 )
	{
		$Q = 'select '
						. ' g.worker, '
						. ' to_char(sysdate + 1, \'yyyy-mm-dd\') this_date'
						. ' from hrs_graph g '
						. ' left join lib_standard_graphs sg on sg.id = g.standard_graph_id '
						. ' where '
						. ' type = 1 '
						. ' and real_date > trunc(sysdate) '
						. ' and g.standard_graph_checksum != sg.checksum '
						. ' group by g.worker '
		;
		$result = DB::LoadObjectList( $Q );

		$workers = self::getWorkers();
		$return = false;
		foreach ( $result as $w )
		{
			$worker = C::_( 'WORKER', $w );
			$day_start = C::_( 'THIS_DATE', $w );

			$slf_worker = C::_( $worker, $workers );
			$graph = C::_( 'GRAPHTYPE', $slf_worker );
			$collect = self::generate_standard_graph_days( $graph, 0, $config_days, $day_start );
			$standard_graph_data = self::getStandardGraphs( $graph );
			foreach ( $collect as $day => $time_id )
			{
				$return = self::update_graph_data( $slf_worker, $day, $time_id, 1, $standard_graph_data );
			}
		}

		return $return;

	}

	public static function standard_to_standard_change( $config_days = 1 )
	{
		$Q = 'select '
						. ' w.id worker, '
						. ' min(g.real_date) this_date '
						. ' from hrs_graph g '
						. ' left join slf_worker w on w.id = g.worker '
						. ' left join slf_changes c on c.id = w.change_id '
						. ' where '
						. ' w.active > 0 '
						. ' and g.standard_graph_id != w.graphtype '
						. ' and g.standard_graph_id != 0 '
						. ' and w.graphtype != 0 '
						. ' and g.real_date > trunc(sysdate) '
						. ' group by w.id '
		;
		$result = DB::LoadObjectList( $Q );

		$workers = self::getWorkers();
		$return = false;
		foreach ( $result as $w )
		{
			$worker = C::_( 'WORKER', $w );
			$day_start = C::_( 'THIS_DATE', $w );

			$slf_worker = C::_( $worker, $workers );
			$graph = C::_( 'GRAPHTYPE', $slf_worker );
			$collect = self::generate_standard_graph_days( $graph, 0, $config_days, $day_start );
			$standard_graph_data = self::getStandardGraphs( $graph );

			foreach ( $collect as $day => $time_id )
			{
				$return = self::update_graph_data( $slf_worker, $day, $time_id, 1, $standard_graph_data );
			}
		}

		return $return;

	}

	public static function dinamic_to_standard_change()
	{
		$Q = 'select '
						. ' w.id worker, '
						. ' min(g.real_date) this_date '
						. ' from hrs_graph g '
						. ' left join slf_worker w on w.id = g.worker '
						. ' where '
						. ' w.active > 0 '
						. ' and g.standard_graph_id = 0 '
						. ' and w.graphtype != 0 '
						. ' and g.real_date > trunc(sysdate) '
						. ' and g.type = 0 '
						. ' group by w.id '
		;
		$result = DB::LoadObjectList( $Q );

		$return = false;
		foreach ( $result as $w )
		{
			$worker = C::_( 'WORKER', $w );
			$day_start = C::_( 'THIS_DATE', $w );
			self::deleteFutureGraphLines( $worker, $day_start, 0 );
		}

		return $return;

	}

	public static function calculusTypeRegime_CheckUpdate()
	{
		$Q = 'select '
						. ' w.id worker, '
						. ' to_char(sysdate, \'yyyy-mm-dd\') as this_date '
						. ' from hrs_graph g '
						. ' left join slf_worker w on w.id = g.worker '
						. ' where '
						. ' w.active > 0 '
						. ' and g.calculus_type != w.calculus_type or g.calculus_regime != w.calculus_regime '
						. ' and g.real_date >= trunc(sysdate) '
						. ' group by w.id '
		;
		$result = DB::LoadObjectList( $Q );

		$workers = self::getWorkers();
		$return = false;
		foreach ( $result as $w )
		{
			$id = C::_( 'WORKER', $w );
			$day_start = C::_( 'THIS_DATE', $w );

			$worker = C::_( $id, $workers );
			$calculus_type = C::_( 'CALCULUS_TYPE', $worker );
			$calculus_regime = C::_( 'CALCULUS_REGIME', $worker );

			$U = 'update hrs_graph g '
							. ' set '
							. ' g.calculus_type = ' . DB::Quote( $calculus_type ) . ', '
							. ' g.calculus_regime = ' . DB::Quote( $calculus_regime )
							. ' where '
							. ' g.worker = ' . $id
							. ' and g.real_date >= trunc(sysdate) '
			;
			$return = DB::Update( $U );
		}

		return $return;

	}

	public static function updateGraphTimeData()
	{
		$Q = 'select '
						. ' g.time_id '
						. ' from hrs_graph g '
						. ' left join lib_graph_times gt on gt.id = g.time_id '
						. ' where '
						. ' g.real_date > sysdate '
						. ' and g.checksum != gt.checksum '
						. ' group by g.time_id'
		;
		$result = DB::LoadList( $Q );

		$sets = [
				'LIB_TITLE',
				'LIB_DESC',
				'START_TIME',
				'END_TIME',
				'START_BREAK',
				'END_BREAK',
				'COLOR',
				'REST_TYPE',
				'REST_MINUTES',
				'VACATION_INDEX',
				'WORKING_TIME',
				'REST_TIME',
				'HOLIDAY_YN',
				'CHECKSUM'
		];

		$GraphTimes = self::getGraphTimes();
		foreach ( $result as $time_id )
		{
			$data = C::_( $time_id, $GraphTimes, [] );

			$Date = PDate::Get();
			$Day = $Date->toFormat( '%Y-%m-%d' );

			$s = C::_( 'START_TIME', $data, '' );
			$e = C::_( 'END_TIME', $data, '' );
			$sb = C::_( 'START_BREAK', $data, '' );
			$eb = C::_( 'END_BREAK', $data, '' );

			$ss = PDate::Get( $Day . ' ' . $s );
			$ee = PDate::Get( $Day . ' ' . $e );
			$EE = Xhelp::addDay( $ss, $ee );

			$ssb = PDate::Get( $Day . ' ' . $sb );
			$eeb = PDate::Get( $Day . ' ' . $eb );
			$SSB = Xhelp::addDay( $ss, $ssb );
			$EEB = Xhelp::addDay( $ssb, $eeb );

			$vals = [];
			foreach ( $sets as $key )
			{
				if ( !array_key_exists( $key, $data ) )
				{
					continue;
				}

				$value = C::_( $key, $data, '' );
				if ( array_key_exists( $key, self::$date_fields ) )
				{
					$rest_type = C::_( 'REST_TYPE', $data );

					$plus_day = '';
					if ( $key == 'END_TIME' && $EE )
					{
						$plus_day = ' + 1 ';
					}

					if ( $key == 'START_BREAK' && $rest_type == 0 )
					{
						continue;
					}

					if ( $key == 'START_BREAK' && $SSB )
					{
						$plus_day = ' + 1 ';
					}

					if ( $key == 'END_BREAK' && $rest_type == 0 )
					{
						continue;
					}

					if ( $key == 'END_BREAK' && $EEB )
					{
						$plus_day = ' + 1 ';
					}

					if ( $key == 'REST_MINUTES' && $rest_type != 4 )
					{
						$value = 0;
					}

					$vals[] = $key . ' = to_date( to_char(real_date, \'yyyy-mm-dd\') || \' ' . $value . '\' , \'' . self::$date_fields[$key] . '\') ' . $plus_day;
				}
				elseif ( (string) $value != '' )
				{
					$vals[] = $key . ' = ' . DB::Quote( $value );
				}
				else
				{
					$vals[] = $key . ' = ' . DB::Quote( '' );
				}
			}

			$Q = 'update hrs_graph set '
							. implode( ',', $vals )
							. ' where '
							. ' real_date  > sysdate '
							. ' and time_id  = ' . (int) $time_id
			;

			DB::Update( $Q, 'WORKER' );
		}

	}

	public static function update_standard_graph_days( $config_days )
	{
		$Q = 'select '
						. ' g.worker, '
						. ' max(g.real_date) last_day, '
						. ' count(*) days '
						. ' from hrs_graph g '
						. ' where '
						. ' g.real_date >= trunc(sysdate) '
						. ' and g.type = 1 '
						. ' group by g.worker '
		;
		$graphs = DB::LoadObjectList( $Q );

		$workers = self::getWorkers();

		$return = false;
		foreach ( $graphs as $data )
		{
			$days = C::_( 'DAYS', $data );
			$worker = C::_( 'WORKER', $data );
			$las_day = C::_( 'LAST_DAY', $data );
			$worker_data = C::_( $worker, $workers );
			if ( $days < $config_days )
			{
				$day_diff = $config_days - $days;
				$day_start = $las_day ? $las_day . ' +1 day' : '';
				$return = self::standard_graph_data( $worker_data, $day_diff, $day_start );
			}
		}

		return $return;

	}

	public static function insert_standard_graph_data( $config_days = 7 )
	{
		$Q = 'select * from slf_worker w where w.active = 1  and w.graphtype > 0';
		$workers = DB::LoadObjectList( $Q, 'ID' );

		$return = false;
		foreach ( $workers as $id => $data )
		{
			$return = self::standard_graph_data( $data, $config_days );
		}

		return $return;

	}

	public static function standard_graph_data( $slf_worker = null, $config_days = 1, $day_start = '' )
	{
		if ( empty( $slf_worker ) )
		{
			return false;
		}

		$graph = C::_( 'GRAPHTYPE', $slf_worker );
		$collect = self::generate_standard_graph_days( $graph, 0, $config_days, $day_start );
		$standard_graph_data = self::getStandardGraphs( $graph );
		$return = false;
		foreach ( $collect as $day => $time_id )
		{
			$return = self::insert_graph_data( $slf_worker, $day, $time_id, 1, $standard_graph_data, $config_days );
		}

		return $return;

	}

	public static function insert_graph_data( $slf_worker = null, $day = '', $time_id = null, $type = 0, $standard_graph_data = null, $config_days = 1 )
	{
//		if ( self::checkGraphLine( C::_( 'ID', $slf_worker ), $day, $time_id, $type, C::_( 'ID', $standard_graph_data ), $config_days ) )
//		{
//			return false;
//		}
		$table = self::collectGraphData( $slf_worker, $day, $time_id, $type, $standard_graph_data );
		return self::SaveData( $table, self::$date_fields );

	}

	public static function update_graph_data( $slf_worker = null, $day = '', $time_id = null, $type = 0, $standard_graph_data = null )
	{
		$table = self::collectGraphData( $slf_worker, $day, $time_id, $type, $standard_graph_data );
		return self::UpdateData( $table, self::$date_fields );

	}

	public static function collectGraphData( $slf_worker = null, $day = '', $time_id = null, $type = 0, $standard_graph_data = null )
	{
		if ( empty( $slf_worker ) || empty( $day ) )
		{
			return false;
		}

		$time_id = $time_id ? $time_id : 0;
		static $table_obj = null;
		if ( is_null( $table_obj ) )
		{
			$table_obj = new TableHrs_graphInterface( 'hrs_graph', 'ID', 'library.nextval' );
		}

		$table = clone $table_obj;

		static $GraphTimes = null;
		if ( is_null( $GraphTimes ) )
		{
			$GraphTimes = self::getGraphTimes();
		}

		$GraphTime = (array) C::_( $time_id, $GraphTimes, [] );
		$Date = PDate::Get( $day );
		$Day = $Date->toFormat( '%Y-%m-%d' );

		$s = C::_( 'START_TIME', $GraphTime, '' );
		$e = C::_( 'END_TIME', $GraphTime, '' );
		$sb = C::_( 'START_BREAK', $GraphTime, '' );
		$eb = C::_( 'END_BREAK', $GraphTime, '' );

		$ss = PDate::Get( $Day . ' ' . $s );
		$ee = PDate::Get( $Day . ' ' . $e );
		Xhelp::addDay( $ss, $ee );

		$ssb = PDate::Get( $Day . ' ' . $sb );
		$eeb = PDate::Get( $Day . ' ' . $eb );
		Xhelp::addDay( $ss, $ssb );
		Xhelp::addDay( $ssb, $eeb );

		$table->CHANGE_WOKER = 0;

		$table->bind( $slf_worker );
		if ( count( $GraphTime ) )
		{
			$table->bind( $GraphTime );
		}

		$table->WORKER = C::_( 'ID', $slf_worker );
		$table->GT_DAY = $Date->toFormat( '%j' );
		$table->GT_YEAR = $Date->toFormat( '%Y' );
		$table->TIME_ID = $time_id;
		$table->REAL_DATE = $Day;
		$table->START_TIME = $s ? $ss->toFormat() : '';
		$table->END_TIME = $e ? $ee->toFormat() : '';
		$table->START_BREAK = $sb ? $ssb->toFormat() : '';
		$table->END_BREAK = $eb ? $eeb->toFormat() : '';
		$table->REST_MINUTES = 0;
		$table->TYPE = $type;
		$table->STANDARD_GRAPH_CHECKSUM = C::_( 'CHECKSUM', $standard_graph_data, '' );
		$table->STANDARD_GRAPH_ID = C::_( 'ID', $standard_graph_data, 0 );

		return $table;

	}

	public static function getStandardGraphs( $id = 0 )
	{
		static $getStandardGraphs = null;
		if ( is_null( $getStandardGraphs ) )
		{
			$Q = 'select '
							. ' t.* '
							. ' from lib_standard_graphs t '
							. ' where '
							. ' t.active = 1 '
			;
			$getStandardGraphs = DB::LoadObjectList( $Q, 'ID' );
		}

		if ( $id > 0 )
		{
			return C::_( $id, $getStandardGraphs );
		}

		return $getStandardGraphs;

	}

	public static function getGraphTimes()
	{
		$Q = 'select '
						. ' t.* '
						. ' from lib_graph_times t '
						. ' where '
						. ' t.active > 0 ';
		return DB::LoadObjectList( $Q, 'ID' );

	}

	public static function getGraphTime( $id = null )
	{
		if ( !$id )
		{
			return false;
		}

		$Q = 'select '
						. ' t.* '
						. ' from lib_graph_times t '
						. ' where '
						. ' t.active > 0 '
						. ' and t.id = ' . (int) $id;
		return DB::LoadObject( $Q );

	}

	public static function generate_standard_graph_days( $graph = null, $Q_current = 0, $Q = 0, $day_start = '' )
	{
		$StandardGraph_data = self::getStandardGraphs( $graph );
		$StandardGraph = array_slice( (array) $StandardGraph_data, 4, 7 );
		$Day = PDate::Get( $day_start );
		$collect = [];
		for ( $i = $Q_current; $i < $Q; $i++ )
		{
			$thisDay = $Day->toFormat( '%A', true, false );
			$TimeId = C::_( $thisDay, $StandardGraph );
			$collect[$Day->toFormat( '%Y-%m-%d' )] = $TimeId;
			$Day = PDate::Get( $Day->toUnix() + 86400 );
		}

		return $collect;

	}

	public static function SaveData( $data = null, $date_fields = [], $table = 'hrs_graph' )
	{
		$keys = [];
		$vals = [];
		foreach ( $data as $key => $value )
		{
			if ( preg_match( '/:/i', $key ) )
			{
				continue;
			}

			$keys[] = $key;

			if ( array_key_exists( $key, self::$date_fields ) )
			{
				$vals[] = ' to_date(\'' . $value . '\', \'' . self::$date_fields[$key] . '\')';
			}
			elseif ( (string) $value != '' )
			{
				$vals[] = DB::Quote( $value );
			}
			else
			{
				$vals[] = DB::Quote( '' );
			}
		}

		$Query = 'insert into ' . DB_SCHEMA . '.' . $table . ' ( '
						. implode( ',', $keys )
						. ' ) '
						. ' values ( '
						. implode( ',', $vals )
						. ' ) '
		;

		return (bool) DB::Insert( $Query, 'WORKER' );

	}

	public static function UpdateData( $data = null, $date_fields = [] )
	{
		$vals = [];
		foreach ( $data as $key => $value )
		{
			if ( preg_match( '/:/i', $key ) )
			{
				continue;
			}

			if ( array_key_exists( $key, self::$date_fields ) )
			{
				$vals[] = $key . ' =  to_date(\'' . $value . '\', \'' . self::$date_fields[$key] . '\')';
			}
			elseif ( (string) $value != '' )
			{
				$vals[] = $key . ' = ' . DB::Quote( $value );
			}
			else
			{
				$vals[] = $key . ' = ' . DB::Quote( '' );
			}
		}

		$Query = 'update ' . DB_SCHEMA . '.hrs_graph set '
						. implode( ',', $vals )
						. ' where '
						. ' worker = ' . C::_( 'WORKER', $data )
						. ' and gt_day = ' . C::_( 'GT_DAY', $data )
						. ' and gt_year = ' . C::_( 'GT_YEAR', $data )
		;

		return (bool) DB::Update( $Query, 'WORKER' );

	}

	public static function checkGraphLine( $worker = null, $day = '', $time_id = null, $type = 0, $graph_id = 0, $config_days = 1 )
	{
		static $checkGraphLine = null;
		if ( is_null( $checkGraphLine ) )
		{
			$Q = 'select '
							. ' g.*,'
							. ' to_char(g.real_date, \'yyyy-mm-dd\') real_date '
							. ' from hrs_graph g '
							. ' where g.real_date >= trunc(sysdate) '
			;
			$checkGraphLine = DB::LoadObjectList( $Q );
		}

		$result = [];
		foreach ( $checkGraphLine as $data )
		{
			if ( $data->TYPE != (int) $type || $data->REAL_DATE != trim( $day ) || $data->WORKER != (int) $worker )
			{
				continue;
			}

			if ( $type == 0 && $data->TIME_ID != (int) $time_id )
			{
				continue;
			}

			if ( $type == 1 && $data->STANDARD_GRAPH_ID != (int) $graph_id )
			{
				continue;
			}

			$result[] = $data;
		}

		if ( count( $result ) > 0 )
		{
			return true;
		}

		return false;

	}

	public static function getWorkers()
	{
		$workers = null;
		if ( is_null( $workers ) )
		{
			$Q = 'select * from slf_worker w '
							. ' where '
							. ' w.active = 1 '
			;
			$workers = DB::LoadObjectList( $Q, 'ID' );
		}
		return $workers;

	}

	public static function deleteFutureGraphLines( $worker = null, $day = '', $type = 0 )
	{
		$Q = 'delete  from hrs_graph g '
						. ' where '
						. ' g.worker =  ' . (int) $worker
						. ' and g.real_date > to_date(' . DB::Quote( $day ) . ', \'yyyy-mm-dd hh24:mi:ss\')'
						. ' and g.type =  ' . (int) $type
		;

		return DB::Delete( $Q );

	}

}
