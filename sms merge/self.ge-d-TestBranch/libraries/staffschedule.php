<?php

class XStaffSchedule
{
	public static function printData( $id = null )
	{
		if ( $id )
		{
			$query = 'SELECT '
							. ' p.lib_title position, '
							. ' sc.schedule_code, '
							. ' sc.salary, '
							. ' u.lib_title unit, '
							. ' st.lib_title chief_schedule, '
							. ' sf.lib_title replace_schedule, '
							. ' rt.lib_title working_rate,  '
							. ' j.lib_title jd '
							. ' FROM lib_staff_schedules sc '
							. ' LEFT JOIN lib_positions p on p.id = sc.position '
							. ' LEFT JOIN lib_job_descriptions j on j.id = sc.jd  '
							. ' LEFT JOIN lib_units u on u.id = sc.org_place  '
							. ' LEFT JOIN lib_working_rates rt on rt.id = sc.working_rate '
							. ' LEFT JOIN lib_staff_schedules st on st.id = sc.chief_schedule '
							. ' LEFT JOIN lib_staff_schedules sf on sf.id = sc.replace_schedule  '
							. ' where '
							. ' sc.active = 1 '
							. ' AND sc.id = ' . DB::Quote( $id )
			;
			$Schedule = DB::LoadObject( $query );
			return $Schedule;
		}

	}

	public static function getData( $id = null, $limited = null )
	{
		if ( $id )
		{
			$query = 'select st.* from lib_staff_schedules st where st.active = 1 and st.id = ' . DB::Quote( $id );
			$result = DB::LoadObject( $query );
			if ( $limited )
			{
				unset( $result->ID );
				unset( $result->LIB_TITLE );
				unset( $result->LIB_DESC );
				unset( $result->ORG );
				unset( $result->ACTIVE );
				unset( $result->ORDERING );
				unset( $result->CLIENT_ID );
			}
			return $result;
		}
		return [];

	}

	public static function Rebuild( $Org = 0, $Table = null )
	{
		// get taxonomy tree data in the array format that suit for loop/nest loop verify level.
		$Data = self::RebuildGetTreeWithChildren( $Org );
		$n = 0; // need a variable to hold the running n tally
		$level = 0; // need a variable to hold the running level tally
		// verify the level data. this method will be alter the $data value. 
		// so, it doesn't need to use $data = $this->rebuildGenerateTreeData();
		self::rebuildGenerateTreeData( $Data, 0, $level, $n );
		self::SaveTreeData( $Data, 0, $Table );
		return true;

	}

	public static function RebuildGetTreeWithChildren( $Org )
	{
		$sql = 'SELECT '
						. ' * '
						. ' from lib_staff_schedules u '
						. ' where u.active = 1 '
						. ' and u.org = ' . $Org
						. ' order by u.ordering asc, u.chief_schedule desc '
		;
		$Items = DB::LoadObjectList( $sql );
		// populate the array and create an empty CHILDREN array
		$Return = array();
		foreach ( $Items as $Item )
		{
			$ParentID = (int) C::_( 'CHIEF_SCHEDULE', $Item );
			$Return[$ParentID] = C::_( $ParentID, $Return, array() );
			$Return[$ParentID][] = $Item;
		}

		return $Return;

	}

	public static function rebuildGenerateTreeData( &$Data, $ID, $Level, &$N )
	{
		// loop over the node's children and process their data
		// before assigning the right value
		$Items = C::_( $ID, $Data );
		foreach ( $Items as $Key => $Item )
		{
			++$N;
			$Item->ULEVEL = $Level;
			$Item->LFT = $N;
			$Children = C::_( $Item->ID, $Data );
			if ( !empty( $Children ) )
			{
				self::rebuildGenerateTreeData( $Data, $Item->ID, $Item->ULEVEL + 1, $N );
			}
			++$N;
			$Item->RGT = $N;
			$Items[$Key] = $Item;
		}
		$Data[$ID] = $Items;

	}

	public static function SaveTreeData( &$Data, $Parent, $Table )
	{
		$Items = C::_( $Parent, $Data );
		foreach ( $Items as $Item )
		{
			$Table->resetAll();
			$Table->bind( $Item );
			$Table->store();
			$Children = C::_( $Item->ID, $Data );
			if ( !empty( $Children ) )
			{
				self::SaveTreeData( $Data, $Item->ID, $Table );
			}
		}

	}

	public static function GetChiefSubordinationsTree( $Type = 0, $ID = null )
	{
		if ( empty( $ID ) )
		{
			$ID = Users::GetUserID();
		}
		$Hash = $Type . '-' . $ID;
		static $Data = [];
		if ( !isset( $Data[$Hash] ) )
		{
			$Q = 'select '
							. ' sw2.person '
							. ' from '
							. ' lib_staff_schedules t '
							. ' left join ('
							. ' select '
							. ' lss.lft, '
							. ' lss.rgt, '
							. ' lss.org '
							. ' from rel_worker_chief wc '
							. ' left join slf_worker sw on sw.id = wc.worker '
							. ' left join lib_staff_schedules lss on lss.id = sw.staff_schedule '
							. ' where '
							. ' wc.chief_pid = ' . $ID
							. ' and wc.clevel = ' . $Type
							. ' ) u on u.lft < t.lft and u.rgt > t.rgt and u.org = t.org '
							. ' left join slf_worker sw2 on sw2.staff_schedule = t.id '
							. ' where '
							. ' t.active = 1 '
							. ' and u.org is not null '
			;
			$D = (array) XRedis::getDBCache( 'rel_worker_chief', $Q, 'LoadList' );
//			$D = DB::LoadList( $Q );
			if ( empty( $D ) )
			{
				$D = [ 0 ];
			}
			$Data[$Hash] = implode( ', ', $D );
		}
		return $Data[$Hash];

	}

}
