<?php

/**
 * Description of Units
 *
 * @author teimuraz.kevlishvili
 */
class Units
{
	public static function getUnitList( $ORG = 0 )
	{
		static $List = null;
		$Query = 'Select '
						. ' u.ID,'
						. ' u.lib_title title,'
						. ' u.ulevel '
						. ' from lib_units u'
						. ' where u.active > -1'
						. ($ORG ? ' and u.org = ' . $ORG : '')
						. ' order by lft asc ';

		if ( is_null( $List ) )
		{
			$List = XRedis::getDBCache( 'lib_units', $Query, 'LoadObjectList', 'ID' );
//			$List = DB::LoadObjectList( $Query, 'ID' );
		}
		return $List;

	}

	public static function getMyUnitList( $ORG = null, $Person = null )
	{
		if ( !isset( $ORG ) || !isset( $Person ) )
		{
			return (object) [];
		}
		$Query = 'select '
						. ' t.id,'
						. ' t.lib_title title,'
						. ' t.ulevel '
						. ' from lib_units t '
						. ' left join lib_units u on u.lft <= t.lft '
						. ' and u.rgt >= t.rgt '
						. ' and u.org = t.org '
						. ' and u.id in (select	hr.org_place from	hrs_workers_sch hr '
						. ' where'
						. ' hr.org = u.org '
						. ' and hr.parent_id = ' . DB::Quote( $Person ) . ') '
						. ' where'
						. ' t.active = 1 '
						. ' and u.org = ' . DB::Quote( $ORG )
						. ' and u.id is not null '
						. ' order by t.lft asc ';
		return XRedis::getDBCache( 'lib_units', $Query, 'LoadObjectList', 'ID' );
//		return DB::LoadObjectList( $Query );

	}

	public static function InsertRoot( $Data )
	{
		$Table = new TableLib_unitsInterface( 'Lib_units', 'ID', 'library.nextval' );
		$Table->TYPE = 0;
		$Table->PARENT_ID = 0;
		$Table->ULEVEL = 0;
		$Table->PARENT_ID = 0;
		$Table->LFT = 1;
		$Table->RGT = 2;
		$Table->LIB_TITLE = C::_( 'LIB_TITLE', $Data );
		$Table->LIB_DESC = C::_( 'LIB_DESC', $Data );
		$Table->ACTIVE = 1;
		$Table->ORG = C::_( 'ID', $Data, 0 );
		$Table->CLIENT_ID = 0;
		$Table->ORDERING = 1;
		$Table->store();
		return $Table->insertid();

	}

	public static function UpdateRoot( $Data )
	{
		$Table = new TableLib_unitsInterface( 'Lib_units', 'ID', 'library.nextval' );
		$Table->loads( array(
				'ORG' => C::_( 'ID', $Data ),
				'PARENT_ID' => 0
		) );
		$Table->TYPE = 0;
		$Table->PARENT_ID = 0;
		$Table->ULEVEL = 0;
		$Table->PARENT_ID = 0;
		$Table->LFT = 1;
		$Table->ORDERING = 1;
		$Table->RGT = 2;
		$Table->LIB_TITLE = C::_( 'LIB_TITLE', $Data );
		$Table->LIB_DESC = C::_( 'LIB_DESC', $Data );
		$Table->ACTIVE = 1;
		$Table->CLIENT_ID = 0;
		$Table->ORG = C::_( 'ID', $Data );
		$Table->store();
		return $Table->insertid();

	}

	public static function getCurentLevelByParent( $ParentID )
	{
		$Query = 'select '
						. 'u.ulevel + 1'
						. ' from lib_units u'
						. ' where '
						. ' u.id = ' . DB::Quote( $ParentID );
		return DB::LoadResult( $Query );

	}

	public static function getCurentOrgByParent( $ParentID )
	{
		$Query = 'select '
						. 'u.org'
						. ' from lib_units u'
						. ' where '
						. ' u.id = ' . DB::Quote( $ParentID );
		return DB::LoadResult( $Query );

	}

	public static function Rebuild( $Org = 0 )
	{
		// get taxonomy tree data in the array format that suit for loop/nest loop verify level.
		$Data = self::RebuildGetTreeWithChildren( $Org );
		$n = 0; // need a variable to hold the running n tally
		$level = 0; // need a variable to hold the running level tally
		// verify the level data. this method will be alter the $data value. 
		// so, it doesn't need to use $data = $this->rebuildGenerateTreeData();
		self::rebuildGenerateTreeData( $Data, 0, $level, $n );
		self::SaveData( $Data, 0 );
		return true;

	}

	public static function RebuildGetTreeWithChildren( $Org )
	{
		$sql = 'SELECT '
						. ' * '
						. ' from lib_units u '
						. ' where u.active = 1 '
						. ' and u.org = ' . $Org
						. ' order by u.ordering asc, u.ulevel asc '
		;
		$Items = DB::LoadObjectList( $sql );
		// populate the array and create an empty CHILDREN array
		$Return = array();
		foreach ( $Items as $Item )
		{
			$ParentID = C::_( 'PARENT_ID', $Item );
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

//	public static function getRoot()
//	{
//		$Query = ' select '
//						. ' *'
//						. ' from lib_units u '
//						. ' where '
//						. ' u.parent_id = 0 '
//		;
//		return DB::LoadObject( $Query );
//
//	}

	public static function SaveData( &$Data, $Parent )
	{
		$Items = C::_( $Parent, $Data );
		foreach ( $Items as $Item )
		{
			$Table = new UnitsTable();
			$Table->bind( $Item );
			$Table->store();
			unset( $Table );
			$Children = C::_( $Item->ID, $Data );
			if ( !empty( $Children ) )
			{
				self::SaveData( $Data, $Item->ID );
			}
		}

	}

	public static function getOrgList()
	{
		static $ORGList = null;
		if ( is_null( $ORGList ) )
		{
			$Query = 'select '
							. ' id, '
							. ' t.lib_title title '
							. ' from lib_unitorgs t '
							. ' where t.active=1 '
							. ' order by t.ordering asc';
			$ORGList = (array) XRedis::getDBCache( 'lib_unitorgs', $Query, 'LoadObjectList', 'ID' );
//			$ORGList = DB::LoadObjectList( $query, 'ID' );
		}
		return $ORGList;

	}

	public static function getWorkerOrgList( $Worker = 0 )
	{
		if ( !$Worker )
		{
			$Worker = Users::GetUserID();
		}
		static $ORGList = array();
		if ( !isset( $ORGList[$Worker] ) )
		{
			$Query = 'select '
							. ' t.id, '
							. ' t.lib_title title '
							. ' from lib_unitorgs t '
							. ' left join slf_worker o on o.org = t.id '
							. ' where t.active=1 '
							. '  and o.person = ' . $Worker
							. '  and o.active = 1 '
							. ' order by t.ordering asc';
			$ORGList[$Worker] = XRedis::getDBCache( 'lib_unitorgs', $Query, 'LoadObjectList', 'ID' );
//			$ORGList[$Worker] = DB::LoadObjectList( $query, 'ID' );
		}
		return $ORGList[$Worker];

	}

	public static function GetMainUnits( $Org = 0 )
	{
		$Query = ' select '
						. ' u.id, '
						. ' tt.lib_title title '
						. ' from lib_units tt '
						. ' left join lib_unittypes ut on ut.id = tt.type '
						. ' left join lib_units u on u.lft >= tt.lft and u.rgt <= tt.rgt'
						. '  where '
						. ' tt.active > 0 '
						. ' and u.id is not null '
						. ' and ut.def = 1'
						. ($Org ? ' and tt.org = ' . DB::Quote( $Org ) : '')
						. ' GROUP BY	u.id,	tt.lib_title'
		;
		return XRedis::getDBCache( 'lib_units', $Query, 'LoadObjectList', 'ID' );

	}

}
