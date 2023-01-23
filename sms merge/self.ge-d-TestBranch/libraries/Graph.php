<?php

/**
 * Description of Graph
 *
 * @author teimuraz
 */
class XGraph
{
	public static function getWorkerGroups( $UserID = 0 )
	{
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}
		$Q = ' select '
						. ' wg.group_id ID, '
						. ' o.lib_title || \' - \' || lwg.lib_title lib_title '
						. ' from rel_workers_groups wg '
						. ' left join lib_workers_groups lwg on lwg.id = wg.group_id '
						. ' left join lib_unitorgs o on o.id = lwg.org'
						. ' left join HRS_WORKERS_SCH w on w.id = wg.worker '
						. ' where '
						. ' w.parent_id = ' . $UserID
		;
		return DB::LoadObjectList( $Q );

	}

	public static function GetMyOrgs( $UserID = 0 )
	{
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}
		$Q = ' select '
						. ' re.org id, '
						. ' u.lib_title '
						. ' from '
						. ' hrs_workers re '
						. ' left join lib_unitorgs u on u.id = re.org '
						. ' where '
						. ' re.parent_id = ' . DB::Quote( $UserID )
						. 'and u.active = 1 '
						. 'and re.active = 1 '
		;
		return (array) DB::LoadObjectList( $Q );

	}

	public static function GetMyOrgsIDx( $UserID = 0 )
	{
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}
		$Q = ' select '
						. ' w.org '
						. ' from hrs_workers w '
						. ' where '
						. ' w.parent_id = ' . $UserID
		;
		return DB::LoadList( $Q );

	}

	public static function GetWorkerORGByID( $Org )
	{
		$Q = ' select '
						. ' w.org id '
						. ' from hrs_workers w '
						. ' where '
						. ' w.id = ' . (int) $Org
		;
		return DB::LoadResult( $Q );

	}

	public static function getWorkerIDx( $UserID = 0 )
	{
		$Data = self::getMyOrgpidOrgs( $UserID );
		$Return = [];
		foreach ( $Data as $D )
		{
			$Return[] = C::_( 'ORGPID', $D );
		}
		return $Return;

	}

	public static function getWorkerSCH_IDx( $UserID = 0 )
	{
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}
		$Q = ' select '
						. ' w.id, '
						. ' w.org '
						. ' from hrs_workers_sch w '
						. ' where '
						. ' w.parent_id = ' . (int) $UserID
						. ' and w.active = 1'
		;
		return DB::LoadObjectList( $Q );

	}

	public static function getWorkerDataSch( $UserID, $Joins = 0 )
	{
		if ( empty( $UserID ) )
		{
			return [];
		}

		$Join = '';
		$JoinSelect = '';
		if ( $Joins == 1 )
		{
			$JoinSelect = ', st.lib_title schedule_name ';
			$Join = ' left join lib_staff_schedules st on st.id = w.staff_schedule ';
		}

		$Q = ' select '
						. ' w.* '
						. $JoinSelect
						. ' from hrs_workers_sch w '
						. $Join
						. ' where '
						. ' w.id = ' . (int) $UserID
		;
		return DB::LoadObject( $Q );

	}

	public static function getWorkerORGIDs( $UserID = 0 )
	{
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}
		$Q = ' 
			select '
						. ' w.id '
						. ' from hrs_workers w '
						. ' where '
						. ' w.parent_id = ' . $UserID
		;
		return DB::LoadList( $Q );

	}

	public static function getMyOrgpidOrgs( $UserID = 0 )
	{
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}
		$Q = '	select '
						. ' w.org, '
						. ' w.id orgpid, '
						. ' w.org_name '
						. ' from hrs_workers w '
						. ' where '
						. ' w.parent_id = ' . $UserID
						. ' and w.active = 1 '
		;
		return (array) XRedis::getDBCache( 'hrs_workers', $Q, 'LoadObjectList', 'ORG' );
//		return DB::LoadObjectList( $Q, 'org' );

	}

	public static function get_ORGPID_Workers( $Orgpid = 0 )
	{
		static $get_ORGPID_Workers = null;
		if ( is_null( $get_ORGPID_Workers ) )
		{
			$Q = 'select '
							. ' w.id, '
							. ' w.orgpid '
							. ' from slf_worker w '
							. ' where '
//							. ' w.orgpid = ' . $Orgpid
							. ' w.active = 1 '
			;
			$result = DB::LoadObjectList( $Q );
			$collect = [];
			foreach ( $result as $data )
			{
				$collect[$data->ORGPID][] = $data->ID;
			}
			$get_ORGPID_Workers = $collect;
		}
		return C::_( $Orgpid, $get_ORGPID_Workers, [] );

	}

	public static function getWorkerIDxByOrgs( array $Orgs, $UserID = 0 )
	{
		if ( empty( $Orgs ) )
		{
			return array();
		}
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}

		$Q = ' 
			select '
						. '  w . id, '
						. ' w.org '
						. ' from hrs_workers w '
						. ' where '
						. ' w.org in ( ' . implode( ',  ', $Orgs ) . ' ) '
						. ' and w.parent_id = ' . $UserID
		;
		return DB::LoadObjectList( $Q, 'ORG' );

	}

	public static function getWorkerIDsByOrg( $Org, $UserID = 0 )
	{
		if ( empty( $Org ) )
		{
			return 0;
		}
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}

		$Q = '
			select '
						. '  w . id '
						. ' from hrs_workers w '
						. ' where '
						. ' w.org = ' . $Org
						. ' and w.parent_id = ' . $UserID
		;
		return DB::LoadResult( $Q, 'ORG' );

	}

	public static function GetOrgUser( $Worker )
	{
		$Q = '
			select '
						. ' w . *'
						. ' from hrs_workers w '
						. ' where '
						. ' w.id = ' . DB::Quote( $Worker )
		;
		return DB::LoadObject( $Q );

	}

	public static function GetOrgUserSchedule( $Worker, $active = 0 )
	{
		$Q = 'select '
						. ' sch.ID, '
						. ' sch.GRAPHTYPE '
						. ' from hrs_workers w '
						. ' left join hrs_workers_sch sch on sch.orgpid = w.ID '
						. ' where '
						. ' w.id = ' . DB::Quote( $Worker )
						. ($active == 1 ? ' and sch.active = 1 ' : '')
		;
		return DB::LoadObjectList( $Q );

	}

	public static function GetApprove( $Org )
	{
		$Q = '
			select '
						. ' w . *'
						. ' from hrs_workers w '
						. ' where '
						. ' w.parent_id = ' . Users::GetUserID()
						. ' and w.org = ' . $Org
		;
		return DB::LoadObject( $Q );

	}

	public static function GetAdminApprove()
	{
		$Q = ' 
			select '
						. ' w . *'
						. ' from hrs_workers w '
						. ' where '
						. ' w.parent_id = ' . Users::GetUserID()
		;
		return DB::LoadObject( $Q );

	}

	public static function GetOrgData( $Org )
	{
		$Query = 'select
			* from lib_unitorgs o where o.id = ' . $Org;
		return DB::LoadObject( $Query );

	}

	public function GetGraphDays( $GraphType )
	{
		$Q = 'select
			'
						. ' * '
						. ' from LIB_STANDARD_GRAPHS t '
						. ' where '
						. ' t.id = ' . $GraphType;
		return DB::LoadObject( $Q );

	}

	public static function GetWorkerWeekHours( $Worker, $WeekEndDay, $Year, $Exclude = null )
	{
		$EndDate = PDate::Get( self::DayOfYear2Date( $Year, $WeekEndDay ) );
		$StartDate = PDate::Get( $EndDate->toFormat() . '-6 day' );
		$Hash = md5( $StartDate . '-' . $EndDate . '-' . $Exclude );
		static $Data = [];
		if ( !isset( $Data[$Hash] ) )
		{
			$Add = '';
			if ( $Exclude )
			{
				$Add = ' and trunc(t.real_date) != trunc(to_date(\'' . $Exclude . '\', \'yyyy-mm-dd\'))  ';
			}
			$Q = 'select '
							. ' t.WORKER id,'
							. ' sum(gt.working_time) s '
							. ' from HRS_GRAPH t '
							. ' left join lib_graph_times gt on gt.id = t.time_id '
							. ' where '
							. '  t.real_date between to_date(\'' . $StartDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') and  to_date(\'' . $EndDate->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd\') '
//							. ' and gt_year = ' . (int) $Year
							. $Add
							. ' GROUP BY t.WORKER '
			;
			$Data[$Hash] = (array) XRedis::getDBCache( 'HRS_GRAPH', $Q, 'loadObjectList', 'ID' );
		}
		return (float) C::_( $Hash . '.' . $Worker . '.S', $Data );

	}

	/**
	 * 
	 * @param type $Date
	 * @return type
	 * @assert ('2022-05-10', 0) == array(PDate::Get('2022-05-08'), PDate::Get('2022-05-14'))
	 * @assert ('2022-05-10', 1) == array(PDate::Get('2022-05-09'), PDate::Get('2022-05-15'))
	 * @assert ('2022-05-10', 2) == array(PDate::Get('2022-05-10'), PDate::Get('2022-05-16'))
	 * @assert ('2022-05-10', 3) == array(PDate::Get('2022-05-04'), PDate::Get('2022-05-10'))
	 * @assert ('2022-05-10', 4) == array(PDate::Get('2022-05-05'), PDate::Get('2022-05-11'))
	 * @assert ('2022-05-10', 5) == array(PDate::Get('2022-05-06'), PDate::Get('2022-05-12'))
	 * @assert ('2022-05-10', 6) == array(PDate::Get('2022-05-07'), PDate::Get('2022-05-13'))
	 */
	public static function GetWeekStartEnd( $Date, $WeekStart = -1 )
	{
		if ( $WeekStart == -1 )
		{
			$WeekStart = (int) trim( Helper::getConfig( 'graph_autoovertime_week_start_day' ) );
		}
		$Days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
		$FDate = PDate::Get( $Date );
		$D = PDate::Get( $Date )->toFormat( '%w' );
		if ( $D == $WeekStart )
		{
			$StartDate = $FDate;
		}
		else
		{
			$StartDate = PDate::Get( $Date . ' previous ' . C::_( $WeekStart, $Days ) );
		}
		$EndDate = PDate::Get( $StartDate->toFormat() . ' +6 days' );
		return array( $StartDate, $EndDate );

	}

	public static function GetWorkerWeekRate( $Worker )
	{
		static $WorkerDate = null;
		if ( is_null( $WorkerDate ) )
		{
			$Q = ' select '
							. ' w.id, '
							. ' wr.work_duration '
							. ' from slf_worker w '
							. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
							. ' left join lib_working_rates wr on wr.id = sc.working_rate '
							. ' where '
							. ' w.ACTIVE = 1 ' //. (int) $Worker
			;
			$WorkerDate = XRedis::getDBCache( 'slf_worker', $Q, 'LoadObjectList', 'ID' );
		}
		return (float) C::_( $Worker . '.WORK_DURATION', $WorkerDate, 0 );

	}

	public static function DayOfYear2Date( $year, $DayInYear )
	{
		$d = new DateTime( $year . '-01-01' );
		date_modify( $d, '+' . ($DayInYear - 1) . ' days' );
		return $d->format( 'Y-m-d' );

	}

	public static function GetOrgUserIDByOrgID( $Worker )
	{
		static $WorkerDate = array();
		if ( !isset( $WorkerDate[$Worker] ) )
		{
			$Q = ' select '
							. ' w.id '
							. ' from slf_worker w '
							. ' where '
							. ' w.orgpid = ' . (int) $Worker
			;
			$WorkerDate[$Worker] = DB::LoadList( $Q );
		}
		return $WorkerDate[$Worker];

	}

	public static function RecalculateOldEvents( $UserID, $Start, $End, $ImperativeRecalc = false )
	{
		$Status = Helper::getConfig( 'run_old_events_recalculation', 0 );
		if ( $Status || $ImperativeRecalc )
		{
			$StartDate = PDate::Get( $Start );
			$EndDate = PDate::Get( $End );
			$Now = PDate::Get();
			if ( $StartDate->toUnix() < $Now->toUnix() )
			{
				$Params = array(
						':p_date_start' => $StartDate->toFormat( '%Y-%m-%d' ),
						':p_date_end' => $EndDate->toFormat( '%Y-%m-%d' ),
						':p_worker' => $UserID
				);
				return DB::callProcedure( 'ReCalc', $Params );
			}
		}
		return true;

	}

	public static function InsertOldDayEvent( $UserID, $TimeID, $EventDate, $EventCode )
	{
		static $All = [];
		if ( empty( $EventDate ) )
		{
			return false;
		}
		$Key = md5( json_encode( func_get_args() ) );
		if ( isset( $All[$Key] ) )
		{
			return true;
		}
		$All[$Key] = true;
		$Event = PDate::Get( $EventDate );
		$Transaction = new TableHrs_transported_dataInterface( 'hrs_transported_data', 'ID', 'SQS_TRANSPORTED_DATA.Nextval' );
		$Transaction->REC_DATE = $Event->toFormat();
		$Transaction->USER_ID = $UserID;
		$Transaction->DOOR_TYPE = $EventCode;
		$Transaction->TIME_ID = $TimeID;
		$Transaction->CLIENT_ID = 0;
		$Transaction->PARENT_ID = Xhelp::getWorker_sch( $UserID );
		return $Transaction->store();

	}

}
