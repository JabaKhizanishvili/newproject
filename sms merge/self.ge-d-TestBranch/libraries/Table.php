<?php

/**
 * Description of Table
 *
 * @author teimuraz
 */
class XHRSTable
{
	public function GetWorkerCalculusTypes()
	{
		$Query = 'select '
						. ' c.*, '
						. ' to_char(c.start_date, \'yyyy-mm-dd\') start_date,'
						. ' to_char(c.end_date, \'yyyy-mm-dd\') end_date,'
						. ' to_char(c.record_date, \'yyyy-mm-dd hh24:mi:ss\') record_date '
						. ' from REL_WORKER_CALCULUS_TYPE c '
						. ' left join hrs_workers w on w.id = c.worker '
						. ' where '
						. ' w.Active = 1 '
						. ' and c.calculus_type in(1) '
						. ' ORDER BY c.worker asc, c.record_date asc '
		;

		return DB::LoadObjectList( $Query );

	}

	public function GetWorkersData( $start_date, $end_date, $Workers = array(), $ORG = 0 )
	{
		$Return = new stdClass();
		$Return->items = array();
		$Return->Workers = array();
		$where = array();
		$SubWhere = array();
		if ( !empty( $start_date ) )
		{
			$Start_date = new PDate( $start_date );
			$where[] = ' e.event_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
			$SubWhere[] = ' m.event_date > to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		}
		if ( !empty( $end_date ) )
		{
			$End_Date = new PDate( $end_date );
			$where[] = ' e.event_date < to_date(\'' . $End_Date->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
			$SubWhere[] = ' m.event_date < to_date(\'' . $End_Date->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		}
		$SubWhere[] = ' m.c_resolution != 1 ';
		if ( count( $where ) > 1 )
		{
			if ( count( $Workers ) )
			{
				$where[] = ' w.id in(' . implode( ',', $Workers ) . ') ';
			}
			else
			{
				$where[] = ' w.calculus_type =1 ';
			}

//			$where[] = ' w.id in( 480, 42176) ';
//			$where[] = ' w.id in(37705) ';
//			$where[] = ' w.active =1';
//			$where[] = ' w.calculus_type =1 ';
//			$where[] = ' w.calculus_regime =1 ';
			$where[] = ' w.org =  ' . (int) $ORG;
			$where[] = ' e.time_id > 0 ';
			$where[] = ' e.real_type_id = 2000 ';
//			$where[] = ' trunc(e.event_date) not in (select to_date(to_char(sysdate, \'yyyy\') || \'-\' || t.lib_month || \'-\' || t.lib_day, \'yyyy-mm-dd\') holiday from lib_holidays t where t.active = 1) ';
//			$SubWhere[] = ' m.c_resolution <>1 ';
			$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
//			$SubwhereQ = count( $SubWhere ) ? ' WHERE (' . implode( ') AND (', $SubWhere ) . ')' : '';

			$Query = 'select m.*,'
							. ' to_char(m.EndTime, \'yyyy-mm-dd hh24:mi:ss\') endtime, '
							. ' to_char(m.StartTime, \'yyyy-mm-dd hh24:mi:ss\') starttime '
							. ' from ( select w.id,'
							. ' w.orgpid, '
							. ' w.calculus_type, '
							. ' w.graphtype, '
							. ' w.calculus_regime, '
							. ' gt.rest_time as rest, '
							. ' gt.working_time workinghours, '
							. ' e.event_date starttime, '
							. ' getenddate(e.staff_id, e.event_date) endtime, '
							. ' e.staff_id, '
							. ' e.time_id, '
							. ' gt.owner, '
							. ' nvl(gt.rest_minutes, 0) rest_minutes, '
							. ' gt.start_break, '
							. ' gt.end_break, '
							. ' to_char(e.event_date, \'yyyy-mm-dd\') event_date, '
							. ' getlatenes(e.staff_id, e.event_date) lateness '
							. ' from hrs_staff_events e '
							. ' left join lib_graph_times gt on gt. id = e.time_id '
							. ' left join slf_worker w on w.id =  e.staff_id '
							. $whereQ
							. ' ) m '
							. 'order by m.id asc'
			;
			$Data = DB::LoadObjectList( $Query );
			$Return->days = $this->LoadDays( $start_date, $end_date );
			$Return->overtimes = self::GetOverTimes( $Start_date, $End_Date );
			$Return->missings = $this->GetMissings( $Start_date, $End_Date );
			foreach ( $Data as $Item )
			{
				$ID = C::_( 'STAFF_ID', $Item );
				$TDate = C::_( 'EVENT_DATE', $Item );
				$Return->items[$ID] = C::_( $ID, $Return->items, array() );
				$Return->items[$ID][$TDate] = $Item;
				$Return->Workers[$ID]['ID'] = $ID;
				$Return->Workers[$ID]['ORGPID'] = C::_( 'ORGPID', $Item );
				$Return->Workers[$ID]['FIRSTNAME'] = C::_( 'FIRSTNAME', $Item );
				$Return->Workers[$ID]['LASTNAME'] = C::_( 'LASTNAME', $Item );
				$Return->Workers[$ID]['PRIVATE_NUMBER'] = C::_( 'PRIVATE_NUMBER', $Item );
				$Return->Workers[$ID]['POSITION'] = C::_( 'POSITION', $Item );
				$Return->Workers[$ID]['EMAIL'] = C::_( 'EMAIL', $Item );
				$Return->Workers[$ID]['MOBILE_PHONE_NUMBER'] = C::_( 'MOBILE_PHONE_NUMBER', $Item );
				$Return->Workers[$ID]['CALCULUS_REGIME'] = C::_( 'CALCULUS_REGIME', $Item );
				$Return->Workers[$ID]['GRAPHTYPE'] = C::_( 'GRAPHTYPE', $Item );
//				$Return->Workers[$ID]['POSITION'] = C::_( 'POSITION', $Item );
			}
		}
		else
		{
			$Return->Workers = array();
			$Return->items = array();
		}
		return $Return;

	}

	public static function GetOverTimes( $Start_date, $End_Date )
	{
		$OWhere[] = ' o.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		$OWhere[] = ' o.start_date <= to_date(\'' . $End_Date->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		$OWhere[] = ' o.type in (11, 13, 14) ';
		$OWhere[] = '  o.status = 1 ';
		$OWhereQ = count( $OWhere ) ? ' WHERE (' . implode( ') AND (', $OWhere ) . ')' : '';
		$Q = ' select '
						. ' case '
						. ' when o.worker_id > 0 then o.worker_id else o.worker '
						. ' end worker_id, '
						. ' to_char(o.start_date, \'yyyy-mm-dd\') odate, '
						. ' sum(o.day_count) osum '
						. ' from HRS_APPLICATIONS o '
						. $OWhereQ
						. ' group by o.worker_id, o.worker, to_char(o.start_date, \'yyyy-mm-dd\') '
		;

		$Data = DB::LoadObjectList( $Q );
		$Return = array();
		foreach ( $Data as $Item )
		{
			$ID = C::_( 'WORKER_ID', $Item );
			$TDate = C::_( 'ODATE', $Item );
			$Return[$ID] = C::_( $ID, $Return, array() );
			$Return[$ID][$TDate] = C::_( 'OSUM', $Item );
		}
		return $Return;

	}

	public function GetMissings( $Start_date, $End_Date )
	{
		$OWhere[] = ' o.start_date >= to_date(\'' . $Start_date->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\')';
		$OWhere[] = ' o.start_date <= to_date(\'' . $End_Date->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\')';
		$OWhere[] = ' o.type in (17) ';
		$OWhere[] = '  o.status = 1 ';
		$OWhereQ = count( $OWhere ) ? ' WHERE (' . implode( ') AND (', $OWhere ) . ')' : '';
		$Q = ' select '
						. ' case '
						. ' when o.worker_id > 0 then o.worker_id else o.worker '
						. ' end worker_id, '
						. ' to_char(o.start_date, \'yyyy-mm-dd\') odate, '
						. ' sum(o.day_count) osum '
						. ' from HRS_APPLICATIONS o '
						. $OWhereQ
						. ' group by o.worker_id, o.worker, to_char(o.start_date, \'yyyy-mm-dd\') '
		;

		$Data = DB::LoadObjectList( $Q );
		$Return = array();
		foreach ( $Data as $Item )
		{
			$ID = C::_( 'WORKER_ID', $Item );
			$TDate = C::_( 'ODATE', $Item );
			$Return[$ID] = C::_( $ID, $Return, array() );
			$Return[$ID][$TDate] = C::_( 'OSUM', $Item );
		}
		return $Return;

	}

	public function getWorkersOuts( $start_date, $end_date, $ORG = 0 )
	{
		$startDate = new PDate( $start_date );
		$EndDate = new PDate( $end_date );
		$Query = 'select '
						. ' t.worker, '
						. ' to_char(t.start_date, \'yyyy-mm-dd\') StartDate, '
						. ' to_char(t.end_date, \'yyyy-mm-dd\') EndDate, '
						. ' t.type'
						. ' from '
						. ' hrs_applications t '
						. ' where '
						. ' t.status > 0 '
//						. ($ORG > 0 ? ' and t.org = ' . (int) $ORG : '')
						. ' and t.type in (' . HolidayLimitsTable::GetHolidayIDx() . ',3,4,5)'
						. ' and ('
						. ' ( t.start_date >= to_date(\'' . $startDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') '
						. ' and t.start_date <= to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\'))'
						. ' or  to_date(\'' . $startDate->toFormat( '%d-%m-%Y' ) . '\', \'dd-mm-yyyy\') between t.start_date and t.end_date '
						. ' or to_date(\'' . $EndDate->toFormat( '%d-%m-%Y 23:59:59' ) . '\', \'dd-mm-yyyy hh24:mi:ss\') between  t.start_date and t.end_date) '
		;
		$data = DB::LoadObjectList( $Query );
		$Return = array();
		foreach ( $data as $d )
		{
			$Start = C::_( 'STARTDATE', $d );
			$End = C::_( 'ENDDATE', $d );
			$Type = C::_( 'TYPE', $d );
			$Days = Helper::GetDays( $Start, $End );
			foreach ( $Days as $Day )
			{
				$Return[$d->WORKER][$Day] = $Type;
			}
		}
		return $Return;

	}

	public function getTable()
	{
		$Table = new TableHrs_tableInterface( 'Hrs_table', 'ID', 'library.nextval' );
		$Table->setDATE_FIELDS( 'CHANGE_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$Table->setDATE_FIELDS( 'APPROVE_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		return $Table;

	}

	public function getHoursForSingleDay( $StartHour, $StartMin, $EndHour, $EndMIn, $NightStartHour, $NightStartMin, $NightEndHour, $NightEndMin )
	{
		$NightStartMinutes = $NightStartHour * 60 + $NightStartMin;
		$NightEndMinutes = $NightEndHour * 60 + $NightEndMin;

		$StartMinutes = $StartHour * 60 + $StartMin;
		$EndMInutes = $EndHour * 60 + $EndMIn;

		if ( $StartMinutes >= $NightEndMinutes && $EndMInutes <= $NightStartMinutes )
		{
			return 0;
		}


		if ( $StartMinutes >= $NightStartMinutes && $EndMInutes >= $NightEndMinutes )
		{

			$Diff = ($NightEndMinutes - $StartMinutes) / 60;
			return $this->ReturnNightTime( $Diff );
		}
		if ( $StartMinutes >= $NightStartMinutes && $EndMInutes <= $NightEndMinutes )
		{

			$Diff = ($EndMInutes - $StartMinutes) / 60;
			return $this->ReturnNightTime( $Diff );
		}

		if ( $EndMInutes <= $NightStartMinutes )
		{
			return 0;
		}
		if ( $EndMInutes <= $NightEndMinutes && $StartMinutes >= $NightStartMinutes )
		{

			$Diff = ($EndMInutes - $StartMinutes) / 60;
			return $this->ReturnNightTime( $Diff );
		}
		if ( $EndMInutes <= $NightEndMinutes && $StartMinutes < $NightStartMinutes )
		{
			$Diff = ($EndMInutes - $NightStartMinutes) / 60;
			return $this->ReturnNightTime( $Diff );
		}
		if ( $EndMInutes >= $NightStartMinutes && $StartMinutes > $NightEndMinutes )
		{
			$Diff = ($EndMInutes - $NightStartMinutes) / 60;
			return $this->ReturnNightTime( $Diff );
		}

		return 0;

	}

	public function ReturnNightTime( $Time )
	{
		if ( $Time < 0 && $Time > -100 )
		{
			$Time = 0;
		}
		return $Time;

	}

	/**
	 * 
	 * @param type $StartDate
	 * @param type $EndDate
	 * @param type $NightStartHour
	 * @param type $NightStartMin
	 * @param type $NightEndHour
	 * @param type $NightEndMin
	 * @return type
	 * @assert ('2022-05-10 00:00:00', '2022-05-11 00:00:00') == 8
	 * @assert ('2022-05-10 21:00:00', '2022-05-11 21:00:00') == 8
	 * @assert ('2022-05-10 23:00:00', '2022-05-11 23:00:00') == 8
	 * @assert ('2022-05-10 01:00:00', '2022-05-11 01:00:00') == 8
	 * @assert ('2022-05-10 21:00:00', '2022-05-11 03:00:00') == 5
	 * @assert ('2022-05-10 21:00:00', '2022-05-11 00:00:00') == 2
	 * @assert ('2022-05-10 21:00:00', '2022-05-10 23:00:00') == 1
	 * @assert ('2022-05-10 21:00:00', '2022-05-10 23:30:00') == 1.5
	 * @assert ('2022-05-11 00:00:00', '2022-05-11 21:00:00') == 6
	 * @assert ('2022-05-10 03:00:00', '2022-05-10 05:00:00') == 2
	 * @assert ('2022-05-10 00:00:00', '2022-05-10 05:00:00') == 5
	 * @assert ('2022-05-10 03:15:00', '2022-05-10 05:00:00') == 1.75
	 * @assert ('2022-05-10 04:00:00', '2022-05-10 09:00:00') == 2
	 * @assert ('2022-05-10 05:30:00', '2022-05-10 06:00:00') == 0.5
	 * @assert ('2022-05-10 03:30:00', '2022-05-10 23:00:00') == 3.5
	 * @assert ('2022-05-10 20:30:00', '2022-05-10 20:55:00') == 0
	 */
	public function NightHourCalculator( $StartDate, $EndDate, $NightStartHour = null, $NightStartMin = null, $NightEndHour = null, $NightEndMin = null, $DaySplitHour = 12, $DaySplitMin = 00 )
	{
		if ( is_null( $NightStartHour ) && is_null( $NightStartMin ) )
		{
			$night_start_time = Helper::getConfig( 'night_start_time', '22:00' );
			$split = explode( ':', $night_start_time );
			$NightStartHour = (int) C::_( 0, $split, 22 );
			$NightStartMin = (int) C::_( 1, $split, 0 );
		}

		if ( is_null( $NightEndHour ) && is_null( $NightEndMin ) )
		{
			$night_end_time = Helper::getConfig( 'night_end_time', '06:00' );
			$split = explode( ':', $night_end_time );
			$NightEndHour = (int) C::_( 0, $split, 6 );
			$NightEndMin = (int) C::_( 1, $split, 0 );
		}

		$Start = PDate::Get( $StartDate );
		$End = PDate::Get( $EndDate );
		$StartHour = intval( $Start->toFormat( '%H' ) );
		$StartMin = intval( $Start->toFormat( '%M' ) );

		$EndHour = intval( $End->toFormat( '%H' ) );
		$EndMin = intval( $End->toFormat( '%M' ) );

		if ( $StartHour < $NightEndHour && $EndHour > $NightStartHour )
		{
			$Sum = $this->NightHourCalculator( $StartHour, $StartMin, $DaySplitHour, $DaySplitMin, $NightStartHour, $NightStartMin );
			$Sum += $this->NightHourCalculator( $DaySplitHour, $DaySplitMin, $EndHour, $EndMin );
			return $Sum;
		}
		if ( $Start->toFormat( '%Y-%m-%d' ) === $End->toFormat( '%Y-%m-%d' ) )
		{
			if ( $EndHour < $NightStartHour )
			{
				return $this->getHoursForSingleDay( $StartHour, $StartMin, $EndHour, $EndMin, 00, 00, $NightEndHour, $NightEndMin );
			}
			else
			{
				return $this->getHoursForSingleDay( $StartHour, $StartMin, $EndHour, $EndMin, $NightStartHour, $NightStartMin, 24, 00 );
			}
		}



		$numHours = $this->getHoursForSingleDay( $StartHour, $StartMin, 24, 00, $NightStartHour, $NightStartMin, 24, 00 );
		$numHours += $this->getHoursForSingleDay( 0, 0, $EndHour, $EndMin, 00, 00, $NightEndHour, $NightEndMin );
		return $numHours;

	}

	public static function GetMarginDatesFromBillID( $BILLID )
	{
		$Year = '20' . substr( $BILLID, 0, 2 );
		$Month = substr( $BILLID, 2, 2 );
		$StartDay = '01';
		$StartDate = PDate::Get( $Year . '-' . $Month . '-' . $StartDay );
		$EndDate = date( 'Y-m-t', $StartDate->toUnix() + 86400 * 15 );
		$Return = array(
				'START' => $StartDate->toFormat( '%Y-%m-%d' ),
				'END' => $EndDate
		);
		return $Return;

	}

	public function Generate( $data )
	{
		$BILLID = C::_( 'BILL_ID', $data, 0 );
		if ( empty( $BILLID ) )
		{
			return false;
		}
		$ORG = C::_( 'ORG', $data, 0 );
		if ( empty( $ORG ) )
		{
			return false;
		}
		$WorkersIDx = Helper::CleanArray( explode( ',', C::_( 'IDX', $data, 0 ) ) );
		$Dates = Helper::GetMarginDatesFromBillID( $BILLID );
		$StartDate = C::_( 'START', $Dates );
		$EndDate = C::_( 'END', $Dates );

		$CalcLateness = C::_( 'CALC_LATENES', $data, 0 );
		$XTable = new XHRSTable();
		$CommonHolidays = Helper::GetAllHoldays();
		$APPS = $XTable->getWorkersOuts( $StartDate, $EndDate, $ORG );
		$Workers = $XTable->GetWorkersData( $StartDate, $EndDate, $WorkersIDx, $ORG );
		$TableObj = $XTable->getTable();
		$TableObj->setDATE_FIELDS( 'CHANGE_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$TableObj->setDATE_FIELDS( 'APPROVE_DATE', 'yyyy-mm-dd hh24:mi:ss' );
		$HWItems = HolidayLimitsTable::GetHolidayIDx( 0, 'a' );
		$HWLItems = HolidayLimitsTable::GetHolidayIDx( 1, 'a' );
		foreach ( $Workers->Workers as $Worker )
		{
			$ID = C::_( 'ID', $Worker );
			$FWorkeds = 0;
			$Workeds = 0;
			$WHolidays = 0;
			$WLHolidays = 0;
			$Bullettins = 0;
			$Missions = 0;
			$SUM1 = 0;
			$SUM2 = 0;
			$Holidays = 0;
			$HolidayMissions = 0;
			$NightHours = 0;
			$WorkingDay = 0;
			$HoliDayHours = 0;

			$Table = clone $TableObj;
			$Table->loads( array(
					'WORKER' => $ID,
					'BILL_ID' => $BILLID
			) );
			if ( $Table->STATUS > 1 )
			{
				continue;
			}
			$Table->WORKER = $ID;
			$Table->BILL_ID = $BILLID;
			$Table->ORG = $ORG;
			$CalculusRegime = C::_( 'CALCULUS_REGIME', $Worker, 1 );
			$GraphType = C::_( 'GRAPHTYPE', $Worker );
			$SumOvertime = 0;
			$HoliD1 = 0;
			$HoliD2 = 0;
			$ORGPID = C::_( 'ORGPID', $Worker, 0 );
			$Missings = array_keys( (array) C::_( $ID, $Workers->missings, [] ) );
			foreach ( $Workers->days as $Day )
			{
				$DayNumber = PDate::Get( $Day )->toFormat( '%d' );
				/** @var TableHrs_tableInterface $Table */
				$Row = C::_( $ID . '.' . $Day . '', $Workers->items, 0 );
				$Worked = round( C::_( 'WORKINGHOURS', $Row, 0 ), 2 );
				if ( $CalcLateness )
				{
					$Latenes = round( C::_( 'LATENESS', $Row, 0 ), 1 );
				}
				else
				{
					$Latenes = 0;
				}

				$RW = floatval( $Worked - $Latenes );
				$TimeID = C::_( 'TIME_ID', $Row, false );
				$StartTime = C::_( 'STARTTIME', $Row );
				$EndTime = C::_( 'ENDTIME', $Row );
				$TimeType = C::_( 'OWNER', $Row );
				$FWorkeds += $Worked;
				$Workeds += $RW;
				$H = C::_( $Day, $CommonHolidays, false );
				$Out = C::_( $ORGPID . '.' . $Day, $APPS, false );
				if ( $H && $GraphType == 0 && $Out === false )
				{
					$HD = Helper::FormatBalance( $RW, 2 );
					$HoliDayHours += $HD;
					$Holidays++;
					if ( $DayNumber <= 15 )
					{
						$HoliD1 += $HD;
					}
					else
					{
						$HoliD2 += $HD;
					}
					$Table->{'DAY' . $DayNumber} = $HD;
				}
				elseif ( $H && $GraphType > 0 && ($Out != 5 or $Out != 3) )
				{
					$Table->{'DAY' . $DayNumber} = 0;
					$Holidays++;
				}
				elseif ( $H && !$TimeID && ($Out == 5 or $Out == 3) )
				{
					$Table->{'DAY' . $DayNumber} = 0 - 100 - $Out;
					if ( $Out == 5 )
					{
						$Bullettins++;
					}
				}
				elseif ( $TimeType == 0 && $H && ($Out == 5 or $Out == 3) )
				{
					$Table->{'DAY' . $DayNumber} = 0 - 100 - $Out;
					if ( $Out == 5 )
					{
						$Bullettins++;
					}
				}
				elseif ( $TimeType == 0 && $H && $Out == 5 )
				{
					$Table->{'DAY' . $DayNumber} = -105;
					$Bullettins++;
				}
				elseif ( $TimeType == 0 && $H )
				{
					$Table->{'DAY' . $DayNumber} = 0;
				}
				else
				{
					$Table->{'DAY' . $DayNumber} = Helper::FormatBalance( $RW, 2 );
					if ( $Out === false )
					{
						if ( $TimeID )
						{
							$WorkingDay++;
						}
						$Table->{'DAY' . $DayNumber} = Helper::FormatBalance( $RW, 2 );
						if ( $DayNumber <= 15 )
						{
							$sum1 = Helper::FormatBalance( $RW, 2 ) + Helper::FormatBalance( C::_( 'overtimes.' . $ID . '.' . $Day, $Workers, 0 ), 2 );
							$SUM1 += $sum1 > 0 ? $sum1 : 0;
						}
						else
						{
							$sum2 = Helper::FormatBalance( $RW, 2 ) + Helper::FormatBalance( C::_( 'overtimes.' . $ID . '.' . $Day, $Workers, 0 ), 2 );
							$SUM2 += $sum2 > 0 ? $sum2 : 0;
						}
						$NIGHT = 0;
						if ( $TimeID )
						{
							$StartDate = PDate::Get( $StartTime );
							$EndDate = PDate::Get( $EndTime );
							if ( $StartDate->toUnix() > $EndDate->toUnix() )
							{
								$EndDate = PDate::Get( $EndTime . ' + 1day' );
							}
							$NIGHT = $this->NightHourCalculator( $StartDate->toFormat(), $EndDate->toFormat() );
							if ( $NIGHT > 0 )
							{
								if ( $CalcLateness )
								{
									$Latenes = $this->NightHourLatenesCalculator( $ID, $StartDate->toFormat(), $EndDate->toFormat() );
								}
								else
								{
									$Latenes = 0;
								}
								$Rest = $XTable->NightHourRestCalculator( $ID, $TimeID, $StartDate->toFormat(), $EndDate->toFormat() );
								$NIGHT = Helper::FormatBalance( $this->ReturnNightTime( $NIGHT - $Latenes - $Rest ), 2 );
							}
						}
						$NightHours += $NIGHT;
					}
					elseif ( $TimeID == 0 && ($Out == 5 or $Out == 3) )
					{
						$Table->{'DAY' . $DayNumber} = 0 - 100 - $Out;
						if ( $Out == 5 )
						{
							$Bullettins++;
						}
					}
					elseif ( $TimeID == 0 )
					{
						$Table->{'DAY' . $DayNumber} = 0;
//						$Holidays++;
					}
					else
					{
						$HolidayMissions = $Table->{'DAY' . $DayNumber} = 0 - 100 - $Out;
						if ( isset( $HWItems[$Out] ) )
						{
							$Out = 0;
						}
						elseif ( isset( $HWLItems[$Out] ) )
						{
							$Out = 1;
						}
						switch ( $Out )
						{
							case 0:
								$WHolidays++; //= $RW;
								break;
							case 1:
								$WLHolidays++;
								break;
							case 5:
								$Bullettins++;
								break;
							case 7:
								$Missions++;
								break;
							default:
								break;
						}
					}
				}
				if ( $Table->{'DAY' . $DayNumber} > 0 )
				{
					$Table->{'DAY' . $DayNumber} = $Table->{'DAY' . $DayNumber} + Helper::FormatBalance( C::_( 'overtimes.' . $ID . '.' . $Day, $Workers, 0 ), 2 );
				}
				elseif ( $Table->{'DAY' . $DayNumber} == 0 )
				{
					$Table->{'DAY' . $DayNumber} = Helper::FormatBalance( C::_( 'overtimes.' . $ID . '.' . $Day, $Workers, 0 ), 2 );
				}
				$SumOvertime += Helper::FormatBalance( C::_( 'overtimes.' . $ID . '.' . $Day, $Workers, 0 ), 2 );
				$Table->{'DAY' . $DayNumber} = $this->ReturnNightTime( $Table->{'DAY' . $DayNumber} );

				if ( in_array( $Day, $Missings ) )
				{
					$Table->{'DAY' . $DayNumber} = -80;
				}
			}
			$Working = 0;
			$HDay = 0;

			foreach ( $Workers->days as $Day )
			{
				$A = PDate::Get( $Day )->toFormat( '%d' );
				if ( $Table->{'DAY' . $A} > 0 )
				{
					$Working++;
				}
				if ( $Table->{'DAY' . $A} == 0 )
				{
					$HDay++;
				}
			}

			$SUM1 += $HoliD1;
			$SUM2 += $HoliD2;

			$Table->DAYSUM01 = $SUM1;
			$Table->DAYSUM02 = $SUM2;
			$Table->DAYSUM = $Working;
			$Table->SUMHOUR = $SUM1 + $SUM2;
			$Table->OVERTIMEHOUR = $HoliDayHours + $SumOvertime;
			//@TODO NIGHTHOUR
			$Table->NIGHTHOUR = Helper::FormatBalance( $NightHours, 2 );
			$Table->HOLIDAYHOUR = $HoliDayHours;
			$Table->HOLIDAY = $WHolidays;
			$Table->NHOLIDAY = $WLHolidays;
			$Table->BULLETINS = $Bullettins;

			if ( $CalculusRegime == 2 )
			{
				$Table->OTHERHOUR = $Table->SUMHOUR;
			}
			else
			{
				$Table->OTHERHOUR = 0;
			}
			$Table->OTHER = 0;
			$Table->HOLIDAYS = $HDay;
			$Table->store();
		}
		return true;

	}

	public function SendAlert( $Worker, $DATA )
	{
		$Dates = Helper::GetMarginDatesFromBillID( $DATA->BILL_ID );
		$StartDate = C::_( 'START', $Dates );
		$EndDate = C::_( 'END', $Dates );
		$WorkerData = XGraph::getWorkerDataSch( $Worker, 1 );
		$SCH = C::_( 'SCHEDULE_NAME', $WorkerData );
		$sms = new oneWaySMS();
		$URL = URI::getInstance();

		$Firstname = C::_( 'FIRSTNAME', $WorkerData );
		$Lastname = C::_( 'LASTNAME', $WorkerData );
		$message = 'გამარჯობა!' . PHP_EOL . PHP_EOL
						. 'წარმოგიდგენთ მონაცემებს ნამუშევარი დროის აღრიცხვის შესახებ: ' . PHP_EOL . PHP_EOL
						. (!empty( $SCH ) ? 'საშტატო ერთეული: ' . $SCH . PHP_EOL . PHP_EOL : '')
						. 'აღრიცხვის პერიოდი: ' . $StartDate . ' - ' . $EndDate . PHP_EOL . PHP_EOL
						. 'საათების ჯამური რაოდენობა: ' . $DATA->SUMHOUR . PHP_EOL . PHP_EOL
						. 'დეტალურ ინფორმაციას, '
						. 'გთხოვთ, გაეცნოთ ელექტრონულ სისტემაში: ' . $URL->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) ) . PHP_EOL . PHP_EOL
		;

		$ID = C::_( 'ID', $WorkerData );
		$Email = C::_( 'EMAIL', $WorkerData );
		$Mobile = C::_( 'MOBILE_PHONE_NUMBER', $WorkerData );
		if ( $Email && filter_var( $Email, FILTER_VALIDATE_EMAIL ) )
		{
			$Subject = 'ნამუშევარი საათების გაცნობა';
			$Result = Cmail( $Email, $Subject, $message );
			if ( $Result )
			{
				file_put_contents( PATH_LOGS . DS . 'e-hours-send.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
			}
			else
			{
				file_put_contents( PATH_LOGS . DS . 'e-hours-failed.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
			}
		}
		if ( $Mobile )
		{
			$Result = $sms->Send( $Mobile, trim( $sms->TranslitToLat( $message ) ) );
			$Code = (int) substr( $Result, 0, 4 );
			if ( $Code == 0 )
			{
				file_put_contents( PATH_LOGS . DS . 'sms-alert-send.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
			}
			else
			{
				file_put_contents( PATH_LOGS . DS . 'sms-alert-failed.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
			}
		}
		$DATA->STATUS = 3;
		$DATA->store();
		return true;

	}

	public function CalculateHoursDiff( $StartIn, $EndIn )
	{
		$Start = explode( ':', $StartIn );
		$End = explode( ':', $EndIn );
		$HS = (int) C::_( '0', $Start );
		$HE = (int) C::_( '0', $End );
		$MS = (int) C::_( '1', $Start );
		$ME = (int) C::_( '1', $End );
		$HSA = $HS + ($MS / 100);
		$HEA = $HE + ($ME / 100);
		if ( ($HSA > $HEA) || ($MS == $ME && $HS == $HE) )
		{
			$HE += 24;
		}
		$ST = $HS + $MS / 60;
		$ET = $HE + $ME / 60;
		$DiffH = $ET - $ST;
		return round( $DiffH, 2 );

	}

	public function NightHourLatenesCalculator( $ID, $StartTime, $EndTime )
	{
		$Start = PDate::Get( $StartTime );
		$End = PDate::Get( $EndTime );
		$Q = 'select '
						. '  t.event_date - 1 / 24 / 60 * t.time_min TSTART, '
						. ' t.event_date TEND'
						. '  from HRS_STAFF_EVENTS t '
						. ' where '
						. ' staff_id = ' . $ID
						. ' and t.event_date between to_date(' . DB::Quote( $Start->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\')  and to_date(' . DB::Quote( $End->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\')   '
						. ' and nvl(t.time_min, 0) > 0'
						. ' order by t.event_date desc ';
		$Items = DB::LoadObjectList( $Q );
		$SUM = 0;
		foreach ( $Items as $K )
		{
			$M = $this->NightHourCalculator( C::_( 'TSTART', $K ), C::_( 'TEND', $K ) );

			$SUM = $SUM + $M;
		}
		return $SUM;

	}

	public function NightHourRestCalculator( $ID, $TimeID, $StartTime, $EndTime )
	{
		/** @var XGraphs $XGraph */
		$XGraph = XGraphs::GetInstance();
		$TimeData = $XGraph->GetTimeData( $TimeID );
		$RestType = C::_( 'REST_TYPE', $TimeData, 0 );
		$RestTime = 0;
		$Start = PDate::Get( $StartTime );
		$End = PDate::Get( $EndTime );
		switch ( $RestType )
		{
			case 4:
				$Q = ' select '
								. ' estart, '
								. ' estart + 1 / 24 / 60 * k.true_time_min eEnd '
								. ' from ( '
								. ' select '
								. ' GREATEST(getprevdate(t.staff_id, t.event_date, 2), getprevdate(t.staff_id, t.event_date, 2500)) eStart, '
								. ' t.true_time_min '
								. '  from HRS_STAFF_EVENTS t '
								. ' where '
								. ' t.staff_id = ' . $ID
								. ' and t.event_date between to_date(' . DB::Quote( $Start->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\')  and to_date(' . DB::Quote( $End->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\')   '
								. ' and t.event_type = 3'
								. ' ) k '
				;
				$Items = DB::LoadObjectList( $Q );
				foreach ( $Items as $K )
				{
					$M = $this->NightHourCalculator( C::_( 'ESTART', $K ), C::_( 'EEND', $K ) );
					$RestTime = $RestTime + $M;
				}
				break;
			case 1:
				$BStart = C::_( 'START_BREAK', $TimeData );
				$BEnd = C::_( 'END_BREAK', $TimeData );
				$StartDate = PDate::Get( $Start->toFormat( '%Y-%m-%d' ) . ' ' . $BStart );
				$EndDate = PDate::Get( $Start->toFormat( '%Y-%m-%d' ) . ' ' . $BEnd );
				if ( $StartDate->toUnix() > $EndDate->toUnix() )
				{
					$EndDate = PDate::Get( $Start->toFormat( '%Y-%m-%d' ) . ' ' . $BEnd . ' + 1day' );
				}
				$RestTime = $this->NightHourCalculator( $StartDate->toFormat(), $EndDate->toFormat() );
				break;
			case 0:
			default:
				break;
		}
		return $RestTime;

	}

	public function LoadDays( $start_date, $end_date )
	{
		$StartDate = PDate::Get( $start_date )->toFormat( '%Y-%m-%d 00:00:00' );
		$EndDate = PDate::Get( $end_date )->toFormat( '%Y-%m-%d 23:59:59' );
		$DayQuery = 'select to_char(trunc(to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\')) + level - 1, \'yyyy-mm-dd\') pdate from dual connect by level <= trunc(to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd hh24:mi:ss\')) - trunc(to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') )+1';
		return DB::LoadList( $DayQuery, 'PDATE' );

	}

	public function LoadGraph( $Worker, $start_date, $end_date )
	{
		$G = new XGraph();
		$User = $G->GetOrgUser( $Worker );
		$GraphType = C::_( 'GRAPHTYPE', $User );
		$StartDate = PDate::Get( $start_date )->toFormat( '%Y-%m-%d 00:00:00' );
		$EndDate = PDate::Get( $end_date )->toFormat( '%Y-%m-%d 23:59:59' );
		$Days = array();
		if ( $GraphType == 0 )
		{
			$Query = 'select '
							. ' t.time_id, '
							. ' to_char(t.real_date, \'yyyy-mm-dd\') real_date '
							. ' from HRS_GRAPH t '
							. ' where '
							. ' t.worker = ' . (int) $Worker
							. ' and t.real_date between trunc(to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\')) '
							. ' and trunc(to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') ) '
							. ' order by t.real_date asc '
			;
			$Days = DB::LoadObjectList( $Query, 'REAL_DATE' );
			foreach ( $Days as $Key => $Value )
			{
				$Days[$Key] = C::_( 'TIME_ID', $Value );
			}
		}
		else
		{
			$CommonHolidays = Helper::GetAllHoldays();
			$Days = $this->LoadDays( $start_date, $end_date );
			$DayData = $G->GetGraphDays( $GraphType );
			foreach ( $Days as $Key => $Value )
			{
				$H = C::_( $Value, $CommonHolidays, false );
				if ( $H )
				{
					$Days[$Key] = 0;
				}
				else
				{
					$VDate = strtoupper( PDate::Get( $Value )->toFormat( '%A', true, false ) );
					$Days[$Key] = C::_( $VDate, $DayData );
				}
			}
		}

		return $Days;

	}

}
