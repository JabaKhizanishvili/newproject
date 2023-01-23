<?php

class TKCalendar
{
	static $countItems = 1;
	static $step = 5;

	public static function getHeader( $startDate, $EndDate, $suffix = '' )
	{
		$Now = PDate::Get( PDate::Get()->toFormat( '%Y-%m-%d' ) );
		$Holidays = Helper::GetAllHoldays();
		ob_start();
		?>
		<div class="tk_header">
			<?php
			$startTime = 0;
			$Start = $startDate->toUnix();
			$End = $EndDate->toUnix();
			$Count = ($End - $Start) / 86400;
			$TimeSum = (int) Helper::getConfig( 'graph_show_week_times' );
			$WeekEnd = self::GetWeekEnd();
			while ( $Start < $End )
			{
				$Icon = '';
				$EndClass = '';
				$date = new PDate( $startDate->toUnix() + $startTime );
				$Date = $date->toFormat( '%Y-%m-%d' );
				$day = $date->toFormat( '%j' );
				if ( $Count == 0 )
				{
					$EndClass = ' lastColumn';
				}
				if ( $Now->toUnix() > $date->toUnix() && GRAPH_FREE_EDIT == 0 )
				{
					$suffix = '_a';
				}
				else
				{
					$suffix = '';
				}
				$Day = $date->toFormat( '%w' );
				if ( isset( $Holidays[$Date] ) )
				{
					$Icon = '<i class="calendar-icon bi bi-calendar-event-fill"></i>';
				}
				?>
				<div class="tk_head h<?php echo $day . $EndClass; ?> bulletin_report_item_head week-day-<?php echo $Day; ?>">
					<div class="tk_head_in<?php echo $suffix; ?>" id="head_<?php echo $day . $suffix; ?>">
						<?php echo $date->toFormat( '%A' ); ?>
						<br />
						<?php echo $date->toFormat( '%d %B' ); ?>
						<?php echo $Icon; ?>
					</div>
				</div>
				<?php
				if ( $TimeSum > 0 && $Day == $WeekEnd && empty( $suffix ) )
				{
					?>
					<div class="tk_head h<?php echo $day . $EndClass; ?>">
						<div class="tk_head_time_sum">
							<?php echo Text::_( 'Time Sum' ); ?>
							<br />
						</div>
					</div>
					<?php
				}
				$Count--;
				$startTime = 86400;
				$startDate = $date;
				$Start = $date->toUnix();
			}
			?>
			<div class="cls"></div>
			<div class="tk_i_no_scroll"></div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public static function getItems( $startDate, $EndDate, $worker, $suffix = '', $GroupID = 0, $Archive = 1, &$TimesSummary = array() )
	{
		$Start = $startDate->toUnix();
		$End = $EndDate->toUnix();
		$WorkerData = self::getWorkerGraph( $startDate, $EndDate, $worker->ID );
		$WorkerHolidays = self::getWorkerHolidays( $startDate, $EndDate, $worker->ID );
		$WorkerRestHours = []; //self::getWorkerRestHours( $startDate, $EndDate, $worker->ID );
		$WorkerDecrets = self::getWorkerDecrets( $startDate, $EndDate, $worker->ID );
		$GraphTimes = self::GetGraphTimesData( $GroupID, $Archive );
		$TimeSum = (int) Helper::getConfig( 'graph_show_week_times' );
		$WeekEnd = self::GetWeekEnd();
		ob_start();
		self::$countItems++;
		if ( self::$countItems > self::$step )
		{
			self::$countItems = 1;
		}
		?>
		<div class="tk_i_block">
			<?php
			$startTime = 0;
			$Now = PDate::Get( PDate::Get()->toFormat( '%Y-%m-%d' ) );
			while ( $Start < $End )
			{
				$date = new PDate( $startDate->toUnix() + $startTime );
				$day = $date->toFormat( '%j' );
				$year = (int) $date->toFormat( '%Y' );
				$GT = C::_( $year . '.' . (int) $day . '.TIME_ID', $WorkerData, false );
				$RT = $year . '.' . (int) $day;
				$HD = C::_( $year . '.' . (int) $day, $WorkerHolidays, false );
				$DD = C::_( $year . '.' . (int) $day, $WorkerDecrets, false );
				$colorStyle = '';
				$html = '';
				$htmlH = '';
				if ( $Now->toUnix() > $date->toUnix() && GRAPH_FREE_EDIT == 0 )
				{
					$suffix = '_a';
				}
				else
				{
					$suffix = '';
				}
				if ( !C::_( $GT, $GraphTimes ) )
				{
					$GraphTimes[$GT] = self::GetGraphTimeData( $GT );
				}
				if ( $GT )
				{
					$html = XTranslate::_( C::_( $GT . '.LIB_TITLE', $GraphTimes ) );
					$colorStyle = ' style="background-color: ' . C::_( $GT . '.COLOR', $GraphTimes ) . '; "';
				}
				elseif ( $GT !== false )
				{
					$html = Text::_( 'Holiday' );
					$colorStyle = ' style="background-color: #ff5947; "';
				}
				$TimesSummary[$day] = (int) C::_( $day, $TimesSummary, 0 ) + (int) C::_( 'TIME_COUNT', $GT, 0 );
				if ( $HD )
				{
					$Type = C::_( 'TYPE', $HD );
					switch ( $Type )
					{
						case 1:
						case 0:
						default:
							$htmlH .= '<img src="' . X_TEMPLATE . '/images/holiday.png" alt="" class="tk_hd_img" />';
							break;
						case 5:
							$htmlH .= '<img src="' . X_TEMPLATE . '/images/ambulance.png" alt="" class="tk_hd_img" />';
							break;
					}
				}
				$suffixD = '';
				$Today = ($day == $Now->toFormat( '%j' ));
				$RestType = (int) C::_( $GT . '.REST_TYPE', $GraphTimes );
				$HaveRest = true;
				if ( count( C::_( $RT, $WorkerRestHours, array() ) ) )
				{
					$HaveRest = false;
				}

				if ( $RestType == 2 && $Today && !$HD && empty( $suffix ) && $HaveRest )
				{
					$URL = '?option=workersrest&tmpl=modal&worker=' . $worker->ID . '&iframe=true&height=95%25&width=95%25';
					$htmlH .= '<a href="' . $URL . '" class="modal-frame">'
									. '<img src="' . X_TEMPLATE . '/images/sofa_plus.png" alt="" class="tk_rest_img" />'
									. '</a>'
					;
				}
				if ( $DD )
				{
					$suffixD = '_d';
					$html = Text::_( 'Decret' );
					$colorStyle = ' style="background-color: #ff5947;color:#CCCCCC; "';
				}
				$Day = $date->toFormat( '%w' );
				?>
				<div class="tk_i">
					<div class="tk_i_in<?php echo $suffix . $suffixD; ?> day<?php echo $day; ?> worker<?php echo $worker->ID; ?> year<?php echo $year; ?>" <?php echo $colorStyle; ?>>
						<input type="hidden"  value="" />
						<?php
						if ( $GT )
						{
							echo Helper::MakeDoubleToolTip( $html . $htmlH, self::MakeTimeTip( $GraphTimes[$GT], C::_( $RT, $WorkerRestHours, array() ) ) );
						}
						else
						{
							?>
							<span><?php echo $html . $htmlH; ?></span>
							<?php
						}
						?>
					</div>
					<?php
					if ( 1 == 2 && !empty( $html ) )
					{
						$uri = URI::getInstance( '?option=graphhistory&worker=' . $worker->ID . '&day=' . $day . '&year=' . $year );
						$uri->setVar( 'tmpl', 'modal' );
						$uri->setVar( 'iframe', 'true' );
						?>
						<span class="info-btn">
							<a class="graph-info-icon" href="<?php echo $uri->toString(); ?>" data-lity>
								<i class="bi bi-clock-history"></i>
							</a>
						</span>
						<?php
					}
					?>
				</div>
				<?php
				if ( $TimeSum > 0 && $Day == $WeekEnd && empty( $suffix ) )
				{
					$RoutineTime = XGraph::GetWorkerWeekRate( $worker->ID );
					$WeekTimeSum = XGraph::GetWorkerWeekHours( $worker->ID, $day, $year );
					if ( $RoutineTime >= $WeekTimeSum )
					{
						$CLS = 'normal';
					}
					else
					{
						$CLS = 'danger';
					}
					?>
					<div class="tk_i">
						<div class="tk_i_in_time_sum tk_time_sum_<?php echo $worker->ID; ?>_<?php echo $year; ?>_<?php echo $day; ?> time_sum_<?php echo $CLS; ?>">
							<?php echo $RoutineTime; ?> / 
							<?php echo $WeekTimeSum; ?> <?php echo Text::_( 'Hour' ); ?>
						</div>
					</div>
					<?php
				}
				$startTime = 86400;
				$startDate = $date;
				$Start = $date->toUnix();
			}
			?>
			<div class="cls"></div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public static function GetGraphTimes( $Group = 0 )
	{
		static $content = null;
		if ( is_null( $content ) )
		{
			$data = self::GetGraphTimesData( $Group );
			ob_start();
			?>
			<div class="tk_graph_times_overlay"></div>
			<div class="tk_graph_times">
				<a href="javascript:hideTimes();void(0);" class="tk_graph_times_close"></a>
				<div class="tk_graph_times_items">
					<div class="tk_graph_time tk_graph_holiday" id="g_0" style="background-color: #ff5947">
						<?php echo Text::_( 'Holiday' ); ?>
					</div>
					<?php
					foreach ( $data as $item )
					{
						if ( $item->ACTIVE != 1 )
						{
							continue;
						}
						?>
						<div class = "tk_graph_time" id="g_<?php echo $item->ID; ?>" style="background-color: <?php echo $item->COLOR; ?>">
							<?php echo $item->LIB_TITLE; ?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
			$content = ob_get_clean();
		}
		return $content;

	}

	public static function getWorkerGraph( $startDate, $EndDate, $Worker )
	{
		static $Data = [];
		if ( !isset( $Data[$Worker] ) )
		{
			$Query = 'select t.WORKER, '
							. ' t.GT_DAY, '
							. ' t.GT_YEAR, '
							. ' t.TIME_ID '
							. ' from HRS_GRAPH t '
							. ' where '
//						. ' t.worker = ' . (int) $worker
//						. ' and'
							. ' t.real_date between to_date(\'' . $startDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') '
							. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\')'
			;
			$UData = (array) XRedis::getDBCache( 'HRS_GRAPH', $Query );
			foreach ( $UData as $d )
			{
				$Data[$d->WORKER][$d->GT_YEAR][$d->GT_DAY] = $d;
			}
		}
		return C::_( $Worker, $Data, [] );

	}

	public static function getTimes( $startDate, $EndDate, $TimeID )
	{
		$Query = 'Select '
						. ' Count(1) - Sum(Is_Holiday) time_count,'
						. ' real_date, '
						. ' Max(gt_day) gt_day, '
						. ' Max(gt_year) gt_year '
						. ' From '
						. ' (Select '
						. ' Decode(A.Status, Null, 0, 1) Is_Holiday, '
						. ' t.* '
						. ' From Hrs_Graph t, '
						. ' hrs_applications a '
						. ' Where t.Time_Id = ' . (int) $TimeID
						. ' And t.Real_Date Between To_Date(\'' . $startDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') '
						. ' And To_Date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') '
						. ' And a.Worker(+) = t.Worker '
						. ' And a.Status(+) = 1 '
						. ' And a.type(+) in(' . HolidayLimitsTable::GetHolidayIDx() . ', 5) ' //თვლის გამოკლებაში მონაწილე აპლიკაციის ტიპები
						. ' And t.Real_Date Between a.Start_Date(+) And a.End_Date(+)'
						. ' ) '
						. ' Group By Real_Date ';
		$data = DB::LoadObjectList( $Query );
		$Return = array();
		foreach ( $data as $d )
		{
			$Return[$d->GT_YEAR][$d->GT_DAY] = $d;
		}
		return $Return;
		/*
		  $Query = 'select count(1) time_count, '
		  . ' t.real_date, '
		  . ' max(t.gt_day) gt_day, '
		  . ' max(t.gt_year) gt_year '
		  . ' from hrs_graph t '
		  . ' where '
		  . ' t.time_id = ' . (int) $TimeID
		  . ' and t.real_date between to_date(\'' . $startDate->toFormat('%Y-%m-%d') . '\', \'yyyy-mm-dd\') and to_date(\'' . $EndDate->toFormat('%Y-%m-%d') . '\', \'yyyy-mm-dd\') '
		  . ' group by t.real_date ';
		  $data = DB::LoadObjectList($Query);
		  $Return = array();
		  foreach($data as $d)
		  {
		  $Return[$d->GT_YEAR][$d->GT_DAY] = $d;
		  }
		  return $Return; */

	}

	public static function GetGraphTimesData( $GroupID = null, $Active = 1 )
	{
		if ( empty( $GroupID ) )
		{
			return array();
		}
		static $data = array();
		$Key = $GroupID . '-' . $Active;
		if ( !isset( $data[$Key] ) )
		{
			$Query = 'select '
							. ' t.id, '
							. ' t.lib_title, '
							. ' t.color, '
							. ' t.rest_type, '
							. ' t.active, '
							. ' t.start_time, '
							. ' t.end_time, '
							. ' t.start_break, '
							. 't.end_break '
							. ' from lib_graph_times t '
							. ' WHERE '
//							. ($Active == 1 ? ' t.active = ' . (int) $Active . ' and ' : '')
							. '  t.id in(select distinct tg.time_id from REL_TIME_GROUP tg where tg.group_id =' . $GroupID . ')'
							. ' AND t.owner =1 '
							. ' order by lib_title asc';
			$data[$Key] = (array) XRedis::getDBCache( 'lib_graph_times', $Query, 'LoadObjectList', 'ID' );
//			$data[$Key] = DB::LoadObjectList( $Query, 'ID' );
		}
		return $data[$Key];

	}

	public static function getWorkerHolidays( $startDate, $EndDate, $Worker )
	{
		static $Data = [];
		if ( !isset( $Data[$Worker] ) )
		{
			$Query = 'select '
							. ' g.WORKER, '
							. ' g.GT_DAY, '
							. ' g.GT_YEAR, '
							. ' g.TIME_ID,'
							. ' t.type '
							. ' from '
							. ' hrs_applications t, '
							. ' hrs_graph g, '
							. ' slf_worker w '
//						. ' where g.worker = ' . (int) $Worker
//						. ' and t.status = 1 '
//						. ' and'
							. ' where '
							. ' t.status in (1, 2, 3) '
							. ' and t.type in (' . HolidayLimitsTable::GetHolidayIDx() . ', 5 ) '
							. ' and to_date(\'' . $startDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') <= t.end_date '
							. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') >= t.start_date '
							. ' and w.id = g.worker '
							. ' and w.orgpid = t.worker'
							. ' and g.real_date between t.start_date and t.end_date '
							. ' and g.real_date between to_date(\'' . $startDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') and '
							. ' to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\')   and g.time_id > 0 '
			;
			$Apps = (array) XRedis::getDBCache( 'hrs_applications', $Query, 'LoadObjectList' );
			foreach ( $Apps as $d )
			{
				$Data[$d->WORKER][$d->GT_YEAR][$d->GT_DAY] = $d;
			}
		}
		return C::_( $Worker, $Data, [] );

	}

	public static function getTimeItems( $startDate, $EndDate, $Time, &$TimesSummary = array() )
	{
		$Start = $startDate->toUnix();
		$End = $EndDate->toUnix();
		$TimeData = self::getTimes( $startDate, $EndDate, $Time->ID );
		ob_start();
		if ( self::$countItems == 1 )
		{
			?>
			<div class="tk_i_scroll"></div>
			<?php
		}
		self::$countItems++;
		if ( self::$countItems > self::$step )
		{
			self::$countItems = 1;
		}
		?>
		<div class="tk_i_block" data-rel="<?php echo $Time->ID; ?>">
			<?php
			$startTime = 0;
			while ( $Start < $End )
			{
				$date = new PDate( $startDate->toUnix() + $startTime );
				$day = $date->toFormat( '%j' );
				$year = (int) $date->toFormat( '%Y' );
				$GT = C::_( $year . '.' . (int) $day, $TimeData, false );
				$html = '0';
				if ( $GT )
				{
					$html = C::_( 'TIME_COUNT', $GT );
				}
				$colorStyle = ' style="background-color: ' . C::_( 'COLOR', $Time ) . '; "';
				$TimesSummary[$day] = C::_( $day, $TimesSummary, 0 ) + C::_( 'TIME_COUNT', $GT );
				?>
				<div class="tk_i">
					<div class="tk_i_in_t day<?php echo $day; ?> time<?php echo $Time->ID; ?> year<?php echo $year; ?>" <?php echo $colorStyle; ?> data-export-rel="<?php echo $Time->ID; ?>">
						<?php
						if ( $html <> '0' )
						{
							?>
							<a rel="iframe-<?php echo $Time->ID . '' . $day; ?>" class="modal-frame" href="?bgroup=-1&group=-1&graphtime=<?php echo $Time->ID; ?>&start_date=<?php echo $date->toFormat( '%d-%m-%Y' ); ?>&end_date=<?php echo $date->toFormat( '%d-%m-%Y' ); ?>&option=r_graph_workers&tmpl=modal&iframe=true&height=97%&width=97%">
								<span>
									<?php echo $html; ?>
								</span>
							</a>
							<?php
						}
						else
						{
							?>
							<span>
								<?php echo $html; ?>
							</span>
							<?php
						}
						?>
						</span>
					</div>
				</div>
				<?php
				$startTime = 86400;
				$startDate = $date;
				$Start = $date->toUnix();
			}
			?>
			<div class="cls"></div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public static function getTimeItemsSummary( $startDate, $EndDate, $TimesSummary, $GroupID = 0 )
	{
		$Start = $startDate->toUnix();
		$End = $EndDate->toUnix();
		ob_start();
		?>
		<div class="tk_i_block">
			<?php
			$startTime = 0;
			while ( $Start < $End )
			{
				$date = new PDate( $startDate->toUnix() + $startTime );
				$day = $date->toFormat( '%j' );
				$year = (int) $date->toFormat( '%Y' );
				$html = (int) C::_( $day, $TimesSummary );
				?>
				<div class="tk_i">
					<div class="tk_i_in_t day<?php echo $day; ?> time_all year<?php echo $year; ?>"  >
						<input type="hidden"  value="" />
						<span>
							<?php
							if ( $html <> '0' )
							{
								?>
								<a rel="iframe-<?php echo $day; ?>" class="modal-frame" href="?bgroup=<?php echo $GroupID; ?>&group=-1&graphtime=-1&start_date=<?php echo $date->toFormat( '%d-%m-%Y' ); ?>&end_date=<?php echo $date->toFormat( '%d-%m-%Y' ); ?>&option=r_graph_workers&tmpl=modal&iframe=true&height=97%&width=97%">
									<?php echo $html; ?>
								</a>
								<?php
							}
							else
							{
								echo $html;
							}
							?>
						</span>
					</div>
				</div>
				<?php
				$startTime = 86400;
				$startDate = $date;
				$Start = $date->toUnix();
			}
			?>
			<div class="cls"></div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public static function getWorkerDecrets( $startDate, $EndDate, $Worker )
	{
		static $Data = [];
		if ( !isset( $Data[$Worker] ) )
		{
			$Query = 'select sw.ID, '
							. ' to_char(t.start_date, \'yyyy-mm-dd hh24:mi:ss\') start_date, '
							. ' to_char(t.end_date, \'yyyy-mm-dd hh24:mi:ss\') end_date '
							. ' from '
							. ' hrs_applications t '
							. ' left join slf_worker sw on sw.orgpid = t.worker '
							. ' where '
							. ' t.type in (3, 4) '
							. ' and t.status > 0 '
							. ' and to_date(\'' . $startDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') <= t.end_date '
							. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') >= t.start_date '
			;
			$Apps = XRedis::getDBCache( 'hrs_applications', $Query, 'LoadObjectList' );
			if ( $Apps )
			{
				$daySec = 86400;
				foreach ( $Apps as $d )
				{
					$Start = new PDate( $d->START_DATE );
					$StartUnix = $Start->toUnix();
					$End = new PDate( $d->END_DATE );
					$EndUnix = $End->toUnix();
					$Day = new PDate();
					$Day->setTimestamp( $StartUnix );
					while ( $StartUnix <= $EndUnix )
					{
						$Data[$d->ID][$Day->toFormat( '%Y' )][(int) $Day->toFormat( '%j' )] = 1;
						$StartUnix += $daySec;
						$Day->setTimestamp( $StartUnix );
					}
				}
			}
		}
		return C::_( $Worker, $Data, [] );

	}

	public static function GetGraphTimeData( $GT )
	{
		static $data = null;
		if ( empty( $GT ) )
		{
			return false;
		}
		if ( !isset( $data[$GT] ) )
		{
			$query = 'select '
							. ' t.* '
							. ' from lib_graph_times t '
							. ' WHERE t.active =1 '
							. ' AND t.ID = ' . $GT
			;
			$data[$GT] = DB::LoadObject( $query, 'ID' );
		}
		return $data[$GT];

	}

	public static function MakeTimeTip( $GraphTime, $WorkerRestHours )
	{
		$LIB_TITLE = C::_( 'LIB_TITLE', $GraphTime );
		$START_TIME = C::_( 'START_TIME', $GraphTime );
		$END_TIME = C::_( 'END_TIME', $GraphTime );
		$START_BREAK = C::_( 'START_BREAK', $GraphTime, false );
		$END_BREAK = C::_( 'END_BREAK', $GraphTime, false );
		if ( empty( $START_BREAK ) )
		{
			$START_BREAKDate = C::_( 'START_DATE', $WorkerRestHours, false );
			if ( $START_BREAKDate )
			{
				$StartDate = new PDate( $START_BREAKDate );
				$START_BREAK = $StartDate->toFormat( '%H:%M' );
			}
		}
		if ( empty( $END_BREAK ) )
		{

			$END_BREAKDate = C::_( 'END_DATE', $WorkerRestHours, false );
			if ( $END_BREAKDate )
			{
				$EndDate = new PDate( $END_BREAKDate );
				$END_BREAK = $EndDate->toFormat( '%H:%M' );
			}
		}
		ob_start();
		?>
		<span class="tip_title"><?php echo $LIB_TITLE; ?></span>
		<span class="tip_item"><?php echo Text::_( 'START_TIME' ); ?>: <?php echo $START_TIME; ?></span>
		<span class="tip_item"><?php echo Text::_( 'END_TIME' ); ?>: <?php echo $END_TIME; ?></span>
		<?php
		if ( $START_BREAK )
		{
			?>
			<span class="tip_item"><?php echo Text::_( 'START_BREAK' ); ?>: <?php echo $START_BREAK; ?></span>
			<?php
		}
		if ( $END_BREAK )
		{
			?>
			<span class="tip_item"><?php echo Text::_( 'END_BREAK' ); ?>: <?php echo $END_BREAK; ?></span>
			<?php
		}
		$content = ob_get_clean();
		return $content;

	}

	public static function getWorkerRestHours( $StartDate, $EndDate, $Worker )
	{
		$Query = 'select '
						. ' g.* ,'
						. ' t.type,'
						. ' to_char(t.start_date, \'yyyy-mm-dd hh24:mi:ss\') start_date, '
						. ' to_char(t.end_date, \'yyyy-mm-dd hh24:mi:ss\') end_date '
						. ' from '
						. ' hrs_applications t, '
						. ' hrs_graph g '
						. ' where t.worker = ' . (int) $Worker
						. ' and t.status = 1 '
						. ' and t.type in (10) '
						. ' and to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') <= trunc(t.end_date) '
						. ' and to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') >= trunc(t.start_date) '
						. ' and g.worker = t.worker '
						. ' and g.real_date between trunc(t.start_date) and trunc(t.end_date) '
						. ' and g.real_date between to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') and '
						. ' to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\')   and g.time_id > 0 '
		;
		$data = DB::LoadObjectList( $Query );
		$Return = array();
		foreach ( $data as $d )
		{
			$Return[$d->GT_YEAR][$d->GT_DAY] = $d;
		}
		return $Return;

	}

	public static function GetWeekEnd()
	{
		$WeekStart = (int) trim( Helper::getConfig( 'graph_autoovertime_week_start_day' ) );
		$Day = $WeekStart - 1;

		if ( $Day < 0 )
		{
			$Day = 6;
		}
		return $Day;

	}

}
