<?php

/**
 * Description of holidaywork
 *
 * @author teimuraz.kevlishvili
 */
class HolidayWork
{
	/**
	 * 
	 * @param int $Chapter
	 * @param DateString $Start
	 * @param DateString $End
	 * @return Array
	 */
	public function GetHolidayWorks( $Chapter, $Start, $End, $Type = 0 )
	{
		$StartDate = new PDate( $Start );
		$EndDate = new PDate( $End );
		$Holidays = $this->GetHolidays( $StartDate, $EndDate );
		$Return = array();
		foreach ( $Holidays as $Holiday )
		{
			$Data = $this->GetHolidayWork( $Chapter, $Holiday, $Type );
			$Return = array_merge( $Return, $Data );
		}
		return $Return;

	}

	public function GetHolidays( $StartDate, $EndDate )
	{
		$Query = 'select '
						. ' to_char(k.holiday, \'yyyy-mm-dd\') holiday '
						. ' from ('
						. ' select '
						. ' to_date(to_char(sysdate, \'yyyy\') || \'-\' || t.lib_month || \'-\' || t.lib_day, \'yyyy-mm-dd\') holiday '
						. ' from LIB_HOLIDAYS t'
						. ' ) k '
						. ' where '
						. ' k.holiday between to_date(' . DB::Quote( $StartDate->toFormat( '%d-%m%Y' ) ) . ', \'dd-mm-yyyy\') and to_date(' . DB::Quote( $EndDate->toFormat( '%d-%m%Y' ) ) . ', \'dd-mm-yyyy\')'
		;
		return DB::LoadList( $Query, 'HOLIDAY' );

	}

	public static function getInstance()
	{
		static $instance = null;
		if ( !$instance )
		{
			$instance = new self();
		}
		return $instance;

	}

	public function GetHolidayWork( $Chapter, $Day, $GraphType = 0 )
	{
		$Date = new PDate( $Day );
		$StrDate = $Date->toFormat( '%Y-%m-%d' );
		if ( $GraphType )
		{
			$Graph = ' and w.graphtype > 0';
		}
		else
		{
			$Graph = ' and w.graphtype = 0';
		}

		$Query = 'select 
				m.id,
				m.time_id,
				m.firstname,
       m.lastname,
       m.salary_employee_id,
       m.private_number,
       trunc(ROUND(((m.v_end_time - m.v_start_time) * 24), 1), 2) TIME,
	 to_char( m.v_start_time, \'yyyy-mm-dd hh24:mi:ss\') v_start_time,
	 to_char( m.v_end_time, \'yyyy-mm-dd hh24:mi:ss\') v_end_time
  from (select 
		    k.id,
		    k.firstname,
               k.lastname,
               k.salary_employee_id,
               k.private_number,
               k.start_time,
               k.TIME_ID,
               k.end_time,
               CASE
                 WHEN k.start_time < to_date(\'' . $StrDate . '\', \'yyyy-mm-dd\') then
                  to_date(\'' . $StrDate . '\', \'yyyy-mm-dd\')
                 else
                  k.start_time
               end v_start_time,
               CASE
                 WHEN k.END_TIME >
                      to_date(\'' . $StrDate . ' 23:59:59\', \'yyyy-mm-dd hh24:mi:ss\') then
                  to_date(\'' . $StrDate . ' 23:59:59\', \'yyyy-mm-dd hh24:mi:ss\')
                 else
                  k.END_TIME
               end v_end_time
          from (select 
                       w.id,
					w.firstname,
                       w.lastname,
                       w.salary_employee_id,
                       w.private_number,
                       gd."REAL_DATE",
                       
                       gd."TIME_ID",
                       to_date(to_char(gd.real_date, \'yyyy-mm-dd\') || \' \' ||
                               gt.start_time,
                               \'yyyy-mm-dd hh24:mi\') start_time,
                       
                       CASE
                         WHEN replace(gt.end_time, \':\', \'\') <=
                              replace(gt.start_time, \':\', \'\') then
                          to_date(to_char(gd.real_date + 1, \'yyyy-mm-dd\') || \' \' ||
                                  gt.end_time,
                                  \'yyyy-mm-dd hh24:mi\')
                         else
                          to_date(to_char(gd.real_date, \'yyyy-mm-dd\') || \' \' ||
                                  gt.end_time,
                                  \'yyyy-mm-dd hh24:mi\')
                       end end_time,
                       decode(nvl(gt.start_break, null),
                              null,
                              to_date(\'' . $StrDate . '\', \'yyyy-mm-dd\'),
                              to_date(\'' . $StrDate . '\' || \' \' || gt.start_break,
                                      \'yyyy-mm-dd hh24:mi\')) start_break_time,
                       CASE
                         WHEN replace(gt.end_break, \':\', \'\') <=
                              replace(gt.start_break, \':\', \'\') then
                          to_date(to_char(gd.real_date + 1, \'yyyy-mm-dd\') || \' \' ||
                                  gt.end_break,
                                  \'yyyy-mm-dd hh24:mi\')
                         else
                          to_date(to_char(gd.real_date, \'yyyy-mm-dd\') || \' \' ||
                                  gt.end_break,
                                  \'yyyy-mm-dd hh24:mi\')
                       end end_break_time
                
                  from (select w.id worker, g.real_date, g.time_id
                          from slf_persons w
                          left join hrs_graph g
                            on g.worker = w.id
                         where g.real_date between
                               trunc(to_date(\'' . $StrDate . '\', \'yyyy-mm-dd\')) - 1 and
                               to_date(\'' . $StrDate . '\', \'yyyy-mm-dd\')
					' . $Graph . ' ) gd
                  left join lib_graph_times gt
                    on gt.id = gd.time_id
                  left join slf_persons w
                    on w.id = gd."WORKER"
							where w.chapter =' . DB::Quote( (int) $Chapter )
						. ' and gd."TIME_ID" > 0) k
         where k.END_TIME >= to_date(\'' . $StrDate . '\', \'yyyy-mm-dd\')
           and (k.END_TIME <> to_date(\'' . $StrDate . '\', \'yyyy-mm-dd\'))) m'
						. ' order by lastname asc'
		;

		$Items = DB::LoadObjectList( $Query );
		$WDays = Helper::CountMonthWorkingDays( $Date->toFormat( '%Y' ), $Date->toFormat( '%m' ) );
		return $this->Calculate( $Items, $WDays, $StrDate );

	}

	public function Calculate( $Items, $WDays, $Date )
	{
		$Return = array();
		foreach ( $Items as $Item )
		{
			$IDX = C::_( 'ID', $Item );
			$New = new stdClass();
			$New->WORKER = C::_( 'LASTNAME', $Item ) . ' ' . C::_( 'FIRSTNAME', $Item );
			$New->SALARY_EMPLOYEE_ID = C::_( 'SALARY_EMPLOYEE_ID', $Item );
			$New->PRIVATE_NUMBER = C::_( 'PRIVATE_NUMBER', $Item );
			$New->HOLIDAY = $Date;
			$New->TIME = Helper::FormatBalance( 0, 2, '.' );
			$New->COEFFICIENT = Helper::FormatBalance( 0, 4, '.' );
			$App = $this->GetUserApp( $Item->ID, $Item->V_START_TIME, $Item->V_END_TIME );
			if ( $App )
			{
//				$Return[] = $New;
				continue;
			}
			$TimeID = C::_( 'TIME_ID', $Item );
			$DecretTime = $this->GetUserDecretMinutes( $Item->ID, $Item->V_START_TIME ) / 60;
			$Time = $this->GetUserEventsMinutes( $Item->ID, $Item->V_START_TIME, $Item->V_END_TIME ) / 60;
			$RestTime = $this->GetUserRestTime( $Item->ID, $Item->V_START_TIME, $Item->V_END_TIME, $TimeID ) / 60;
			$New->TIME = $Item->TIME - $Time - $DecretTime;

			if ( $New->TIME < 0 )
			{
				$New->TIME = 0;
			}
			else if ( $New->TIME >= 5 )
			{
				$New->TIME = $New->TIME - $RestTime;
			}
			$New->TIME = Helper::FormatBalance( round( $New->TIME, 1 ), 2, '.' );
			$New->COEFFICIENT = Helper::FormatBalance( round( (($New->TIME / ($WDays * 8)) * 1.5 ), 4 ), 4, '.' );
			if ( $New->TIME > 0 )
			{
				$Return[] = $New;
			}
		}
		return $Return;

	}

	public function GetUserEvents( $ID, $StartDate, $EndDate )
	{
		$Query = 'select '
						. ' e.*, '
						. ' to_char(e.event_date, \'yyyy-mm-dd hh24:mi:ss\') event_date '
						. ' from hrs_staff_events e '
						. ' where '
						. ' e.staff_id = ' . $ID
						. ' and e.event_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
						. ' and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
		;
		return DB::LoadObjectList( $Query, 'REAL_TYPE_ID' );

	}

	public function GetUserEventsMinutes( $ID, $StartDate, $EndDate )
	{
		$Query = 'select '
						. ' sum(e.TIME_MIN) '
						. ' from hrs_staff_events e '
						. ' where '
						. ' e.staff_id = ' . $ID
						. ' and e.event_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
						. ' and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
		;
		return DB::LoadResult( $Query );

	}

	public function GetUserApp( $ID, $StartDate )
	{
		$Date = new PDate( $StartDate );
		$Query = 'select '
						. ' * '
						. ' from HRS_APPLICATIONS t '
						. ' where '
						. ' t.worker = ' . DB::Quote( $ID )
						. ' and t.type in (0, 1, 3, 4, 5, 7, 8) '
						. ' and ('
						. 'to_date(' . DB::Quote( $Date->toFormat( '%Y-%m-%d' ) ) . ' , \'yyyy-mm-dd\')+0.5 between t.start_date and t.end_date '
//						. ' or to_date(' . DB::Quote( $Date->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') - 0.5 between t.start_date and t.end_date'
						. ') '
		;
		$Items = DB::LoadObject( $Query );
		if ( !empty( $Items ) )
		{
			return 1;
		}
		return 0;

	}

	public function GetUserRestTime( $ID, $StartDate, $EndDate, $TimeID )
	{
		$Query = 'select '
						. ' t.* '
						. ' from lib_graph_times t '
						. ' WHERE t.active =1 '
						. ' AND t.owner = 1'
						. ' and t.id =' . DB::Quote( $TimeID )
						. ' order by lib_title asc';
		$TimeData = DB::LoadObject( $Query );
		switch ( C::_( 'REST_TYPE', $TimeData ) )
		{
			default:
			case 0:
				return 0;
			case 1:
				$Query = 'select '
								. ' e.*, '
								. ' to_char(e.event_date, \'yyyy-mm-dd hh24:mi:ss\') event_date '
								. ' from hrs_staff_events e '
								. ' where '
								. ' e.staff_id = ' . $ID
								. ' and e.REAL_TYPE_ID in (2500, 3000)'
								. ' and e.event_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
								. ' and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
				;
				$Data = DB::LoadObjectList( $Query, 'REAL_TYPE_ID' );
				$BreakStartStr = trim( C::_( '2500.EVENT_DATE', $Data ) );
				$BreakEndStr = trim( C::_( '3000.EVENT_DATE', $Data ) );
				if ( empty( $BreakStartStr ) )
				{
					return 0;
				}
				if ( empty( $BreakEndStr ) )
				{
					return 0;
				}
				$BS = new PDate( $BreakStartStr );
				$BE = new PDate( $BreakEndStr );
				return ($BE->toUnix() - $BS->toUnix() ) / 60;
			case 2:
				return C::_( 'REST_MINUTES', $TimeData );
		}

	}

	public function GetUserDecretMinutes( $ID, $StartDate )
	{
		$Date = new PDate( $StartDate );
		$Query = 'select '
						. ' * '
						. ' from HRS_APPLICATIONS t '
						. ' where '
						. ' t.worker = ' . DB::Quote( $ID )
						. ' and t.type = 9 '
						. ' and ('
						. ' t.start_date between to_date(' . DB::Quote( $Date->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') and to_date(' . DB::Quote( $Date->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') + 1 '
//						. ' or to_date(' . DB::Quote( $Date->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') - 0.5 between t.start_date and t.end_date'
						. ') '
		;
		$Items = DB::LoadObject( $Query );
		if ( !empty( $Items ) )
		{
			return 60;
		}
		return 0;

	}

}
