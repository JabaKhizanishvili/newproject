<?php

/**
 * Description of UserGraphs
 *
 * @author teimuraz
 */
class XUserGraphs
{
	public static function RegisterEvent( $UserID, $Date, $type = null )
	{
		$DateTime = PDate::Get( $Date );
		$EventDate = clone $DateTime;
		$UserLastEvent = self::GetUserLastEvent( $UserID, $DateTime );
		if ( isset( $UserLastEvent[2000] ) )
		{
			$DateTime = PDate::Get( C::_( '2000.EVENT_DATE', $UserLastEvent ) );
		}
		$UserGraphTime = self::GetUserGraphTime( $UserID, $DateTime );
		if ( empty( $UserGraphTime ) )
		{
			return false;
		}
		if ( !isset( $UserLastEvent[2000] ) && $type = 1 )
		{
			return self::RegisterUserEvent( $UserID, $EventDate, 2000, $UserGraphTime );
		}
		else if ( !isset( $UserLastEvent[3500] ) && $type = 2 )
		{
			return self::RegisterUserEvent( $UserID, $EventDate, 3500, $UserGraphTime );
		}
		else
		{
			if ( isset( $UserLastEvent[2000] ) )
			{
				return self::RegisterUserEvent( $UserID, $EventDate, 3500, $UserGraphTime );
			}
			else
			{
				return self::RegisterUserEvent( $UserID, $EventDate, 2000, $UserGraphTime );
			}
		}
		return false;

	}

	public static function GetUserGraphTime( $UserID, PDate $DateTime )
	{
		$Query = 'select '
						. ' g.time_id '
						. ' from hrs_graph g '
						. ' left join lib_graph_times gt on g.time_id = gt.id '
						. ' left join slf_worker sw on sw.id = g.worker '
						. ' where '
						. ' sw.graphtype = 0 '
						. ' and g.worker = ' . DB::Quote( $UserID )
						. ' and gt.type = 1 '
						. ' and g.real_date = to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\') '
						. ' union all '
						. ' select gt.id from ( '
						. ' select '
						. ' case '
						. ' when trim(to_char(to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\'), \'DAY\')) = \'MONDAY\' '
						. ' then s.monday '
						. ' when trim(to_char(to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\'), \'DAY\')) = \'TUESDAY\' '
						. ' then s.tuesday '
						. ' when trim(to_char(to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\'), \'DAY\')) = \'WEDNESDAY\' '
						. ' then s.wednesday '
						. ' when trim(to_char(to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\'), \'DAY\')) = \'THURSDAY\' '
						. ' then s.thursday '
						. ' when trim(to_char(to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\'), \'DAY\')) = \'FRIDAY\' '
						. ' then s.friday '
						. ' when trim(to_char(to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\'), \'DAY\')) = \'SATURDAY\' '
						. ' then s.saturday '
						. ' when trim(to_char(to_date(' . DB::Quote( $DateTime->toFormat( '%Y-%m-%d' ) ) . ', \'yyyy-mm-dd\'), \'DAY\')) = \'SUNDAY\' '
						. ' then s.sunday end '
						. ' as time_id '
						. ' from slf_worker w '
						. ' left join lib_standard_graphs s on s.id = w.graphtype '
						. ' where '
						. ' w.id = ' . DB::Quote( $UserID )
						. ' and w.graphtype > 0 '
						. ' and w.active > 0 '
						. ' ) s '
						. ' inner join lib_graph_times gt on s.time_id = gt.id '
						. ' where '
						. ' gt.type = 1 '
		;
		$Graph = DB::LoadResult( $Query );
		return $Graph;

	}

	public static function GEtUserLastEvent( $UserID, $DateTime )
	{
		$Query = ' select '
						. ' rd.* '
						. ' from hrs_staff_events rd '
						. ' where '
						. ' rd.id in ('
						. ' select '
						. ' max(u.id) '
						. ' from hrs_staff_events u '
						. ' where '
						. ' u.staff_id = ' . DB::Quote( $UserID )
						. ' and u.event_date <= to_date(' . DB::Quote( $DateTime->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
						. ' and u.real_type_id in (2000, 3500) '
						. ' group by u.staff_id, u.real_type_id '
						. ' ) '
		;
		$Rows = DB::LoadObjectList( $Query, 'REAL_TYPE_ID' );
		$StartEvent = C::_( '2000.EVENT_DATE', $Rows, '___' );
		$EndEvent = C::_( '3500.EVENT_DATE', $Rows, '___' );
		if ( $StartEvent == '___' )
		{
			return [];
		}

		if ( $EndEvent == '___' )
		{
			return $Rows;
		}
		if ( PDate::Get( $StartEvent )->toUnix() > PDate::Get( $EndEvent )->toUnix() )
		{
			unset( $Rows[3500] );
			return $Rows;
		}

	}

	public static function RegisterUserEvent( $UserID, PDate $Date, int $Code, $UserGraphTime )
	{
		$User = XGraph::getWorkerDataSch( $UserID );
		$PARENT_ID = C::_( 'PARENT_ID', $User );
		$Table = new TableHrs_transported_dataInterface( 'hrs_transported_data', 'ID', 'sqs_transported_data.nextval' );
		$Table->REC_DATE = $Date->toFormat();
		$Table->USER_ID = $UserID;
		$Table->DOOR_TYPE = $Code;
		$Table->PARENT_ID = $PARENT_ID;
		$Table->TIME_ID = $UserGraphTime;
		if ( $Table->store() )
		{
			return $UserGraphTime;
		}
		else
		{
			return false;
		}

	}

}
