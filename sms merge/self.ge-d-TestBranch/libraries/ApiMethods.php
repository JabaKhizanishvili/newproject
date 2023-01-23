<?php

class XApiMethods
{
	public static function OrgUnitTypes()
	{
		$Q = ' select '
						. ' t.id,'
						. ' t.lib_title, '
						. ' t.lib_desc '
						. '  from lib_unittypes t '
						. ' where '
						. ' t.active = 1 '
						. ' order by t.ordering, t.lib_title '
		;
		return DB::LoadObjectList( $Q );

	}

	public static function Orgs()
	{
		$Q = ' select '
						. ' t.id,'
						. ' t.lib_title, '
						. ' t.lib_desc, '
						. ' t.lib_tin '
						. '  from lib_unitorgs  t '
						. ' where '
						. ' t.active = 1 '
						. ' order by t.ordering, t.lib_title '
		;
		return DB::LoadObjectList( $Q );

	}

	public static function Positions()
	{
		$Q = ' select '
						. ' t.id,'
						. ' t.lib_title, '
						. ' t.lib_desc '
						. '  from lib_positions   t '
						. ' where '
						. ' t.active = 1 '
						. ' order by t.ordering, t.lib_title '
		;
		return DB::LoadObjectList( $Q );

	}

	public static function Persons()
	{
		$Q = ' select '
						. ' t.ID,'
						. ' t.FIRSTNAME,'
						. ' t.LASTNAME,'
						. ' t.FATHER_NAME,'
						. ' t.PRIVATE_NUMBER,'
						. ' t.BIRTHDATE,'
						. ' t.GENDER,'
						. ' t.NATIONALITY,'
						. ' t.MOBILE_PHONE_NUMBER,'
						. ' t.EMAIL,'
						. ' t.LEGAL_ADDRESS,'
						. ' t.ACTUAL_ADDRESS,'
						. ' t.U_COMMENT,'
						. ' t.PERMIT_ID,'
						. ' t.ACCESSS_TYPE,'
						. ' t.LDAP_USERNAME,'
						. ' t.USER_ROLE,'
						. ' t.COUNTING_TYPE,'
						. ' t.TIMECONTROL,'
						. ' t.LIVELIST,'
						. ' t.SMS_REMINDER,'
						. ' t.SMS_WORKER_LATENESS,'
						. ' t.ACCOUNTING_OFFICES,'
						. ' t.IBAN,'
						. ' t.COUNTRY_CODE '
						. '  from slf_persons   t '
						. ' where '
						. ' t.active = 1 '
						. ' order by t.firstname '
		;
		return DB::LoadObjectList( $Q );

	}

	public static function StaffSchedules( $OrgID = 0 )
	{
		$Q = ' select '
						. ' t.id,'
						. ' t.lib_title, '
						. ' t.position, '
						. ' t.jd, '
						. ' t.quantity, '
						. ' t.salary, '
						. ' t.org, '
						. ' t.org_place, '
						. ' t.chief_schedule, '
						. ' t.replace_schedule, '
//						. ' t.iban, '
//						. ' t.auto_overtime, '
						. ' t.working_rate, '
						. ' t.schedule_code, '
						. ' t.lib_desc '
						. '  from lib_staff_schedules    t '
						. ' where '
						. ' t.org = ' . (int) $OrgID
						. ' and t.active = 1 '
						. ' order by t.ordering, t.lib_title '
		;
		return DB::LoadObjectList( $Q );

	}

	public static function Workers( $OrgID = 0 )
	{
		$Q = ' select '
						. ' t.id,'
						. ' t.ORG,'
						. ' t.PERSON,'
						. ' t.STAFF_SCHEDULE,'
						. ' t.SALARY,'
						. ' t.CHIEFS,'
						. ' t.TABLENUM,'
						. ' t.CONTRACTS_DATE,'
						. ' t.CONTRACT_END_DATE,'
						. ' t.CALCULUS_REGIME,'
						. ' t.CATEGORY_ID,'
						. ' t.GRAPHTYPE,'
						. ' t.ACCOUNTING_OFFICES,'
						. ' t.O_COMMENT,'
						. ' t.WORKING_RATE,'
//						. ' t.COUNTRY_CODE,'
						. ' t.SALARY_PAYMENT_TYPE,'
						. ' t.CONTRACT_TYPE,'
						. ' t.CALCULUS_TYPE,'
						. ' t.CHANGE_SUB_TYPE,'
						. ' t.IBAN,'
						. ' t.AUTO_OVERTIME,'
						. ' t.P_CODE,'
						. ' t.SALARYTYPE '
						. '  from slf_worker   t '
						. ' where '
						. ' t.org =  ' . (int) $OrgID
						. ' and t.active = 1 '
						. ' order by t.id '
		;
		return DB::LoadObjectList( $Q );

	}

	public static function OrgUnits( $OrgID = 0 )
	{
		$Q = ' select t.id, t.org,  t.parent_id, t.type UnitType, t.ulevel "LEVEL", t.lib_title, t.lib_desc from LIB_UNITS t where '
						. ' t.org= ' . (int) $OrgID
						. ' and t.active = 1 '
						. 'order by t.lft ';
		$Items = DB::LoadObjectList( $Q );
		foreach ( $Items as $Key => $Item )
		{
			$Level = C::_( 'LEVEL', $Item );
			$Prefix = '';
			if ( $Level )
			{
				$Prefix .= '|';
				$Prefix .= str_repeat( ' _', $Level );
			}
			$Item->PREFIX = $Prefix;
			$Items[$Key] = $Item;
		}
		return $Items;

	}

	public static function GetWorkersGraps( $Date, $UserID )
	{
		$GDate = PDate::Get( $Date );
		$Add = '';
		if ( $UserID )
		{
			$Add = ' and g.worker = ' . (int) $UserID;
		}
		$Q = 'select '
						. ' g.WORKER, '
						. ' g.GT_DAY, '
						. ' g.GT_YEAR, '
						. ' g.REAL_DATE, '
						. ' gt.LIB_TITLE, '
						. ' to_char( to_date(to_char(g.real_date, \'yyyy-mm-dd\') || \' \' || gt.start_time, \'yyyy-mm-dd hh24:mi\'), \'yyyy-mm-dd hh24:mi:ss\') START_TIME, '
						. ' to_char( CASE WHEN replace(gt.end_time, \':\', \'\') <= replace(gt.start_time, \':\', \'\') then to_date(to_char(g.real_date + 1, \'yyyy-mm-dd\') || \' \' || gt.end_time, \'yyyy-mm-dd hh24:mi\') else to_date(to_char(g.real_date, \'yyyy-mm-dd\') || \' \' || gt.end_time, \'yyyy-mm-dd hh24:mi\') end , \'yyyy-mm-dd hh24:mi:ss\') END_TIME, '
						. ' to_char(CASE WHEN gt.start_break is not null and replace(gt.start_break, \':\', \'\') <= replace(gt.start_time, \':\', \'\') then to_date(to_char(g.real_date + 1, \'yyyy-mm-dd\') || \' \' || gt.start_break, \'yyyy-mm-dd hh24:mi\') WHEN gt.start_break is not null and replace(gt.start_break, \':\', \'\') > replace(gt.start_time, \':\', \'\') then to_date(to_char(g.real_date, \'yyyy-mm-dd\') || \' \' || gt.start_break, \'yyyy-mm-dd hh24:mi\') else null end , \'yyyy-mm-dd hh24:mi:ss\') START_BREAK, '
						. ' to_char(CASE WHEN gt.end_break is not null and replace(gt.end_break, \':\', \'\') <= replace(gt.start_time, \':\', \'\') then to_date(to_char(g.real_date + 1, \'yyyy-mm-dd\') || \' \' || gt.end_break, \'yyyy-mm-dd hh24:mi\') WHEN gt.end_break is not null and replace(gt.end_break, \':\', \'\') > replace(gt.start_time, \':\', \'\') then to_date(to_char(g.real_date, \'yyyy-mm-dd\') || \' \' || gt.end_break, \'yyyy-mm-dd hh24:mi\') else null end , \'yyyy-mm-dd hh24:mi:ss\') END_BREAK, '
						. ' gt.COLOR, '
						. ' gt.REST_TYPE, '
						. ' gt.REST_MINUTES, '
						. ' gt.WORKING_TIME, '
						. ' gt.REST_TIME, '
						. ' gt.NIGHT_TIME '
						. ' from hrs_graph g '
						. ' left join lib_graph_times gt on gt.id = g.time_id '
						. ' where '
						. ' g.real_date = to_date( ' . DB::Quote( $GDate->toFormat( '%Y- %m-%d' ) ) . ', \'yyyy-mm-dd\') '
						. ' ' . $Add
		;
		$Items = DB::LoadObjectList( $Q );
		return $Items;

	}

}
