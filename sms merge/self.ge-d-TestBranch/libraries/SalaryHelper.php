<?php

/**
 * Description of SalaryHelper
 *
 * @author teimuraz.kevlishvili
 */
class SalaryHelper
{
	public static function SaveSphere( $SPHEREID, $SPHERE )
	{
		static $Spheres = array();
		if ( empty( $Spheres ) )
		{
			$Query = 'select id, sid from lib_sphere s ';
			$Spheres = DB::LoadObjectList( $Query, 'SID' );
		}
		if ( isset( $Spheres[$SPHEREID] ) )
		{
			return true;
		}
		$Table = new TableLib_sphereInterface( 'lib_sphere', 'ID', 'STRUCT_SQL.nextval' );
		$Table->ID = null;
		$Table->SID = $SPHEREID;
		$Table->LIB_TITLE = $SPHERE;
		$Table->ACTIVE = 1;
		$Table->store();
		$Spheres[$SPHEREID] = $SPHEREID;
		return true;

	}

	public static function SaveDepartment( $DEPARTMENTID, $DEPARTMENT )
	{
		static $Departments = array();
		if ( empty( $Departments ) )
		{
			$Query = 'select id, sid from lib_department s ';
			$Departments = DB::LoadObjectList( $Query, 'SID' );
		}
		if ( isset( $Departments[$DEPARTMENTID] ) )
		{
			return true;
		}
		$Table = new TableLib_departmentInterface( 'lib_department', 'ID', 'STRUCT_SQL.nextval' );
		$Table->ID = null;
		$Table->SID = $DEPARTMENTID;
		$Table->LIB_TITLE = $DEPARTMENT;
		$Table->ACTIVE = 1;
		$Table->store();
		$Departments[$DEPARTMENTID] = $DEPARTMENTID;
		return true;

	}

	public static function SaveChapter( $CHAPTERID, $CHAPTER )
	{
		static $Chapters = array();
		if ( empty( $Chapters ) )
		{
			$Query = 'select id, sid from lib_chapter s ';
			$Chapters = DB::LoadObjectList( $Query, 'SID' );
		}
		if ( isset( $Chapters[$CHAPTERID] ) )
		{
			return true;
		}
		$Table = new TableLib_chapterInterface( 'lib_chapter', 'ID', 'STRUCT_SQL.nextval' );
		$Table->ID = null;
		$Table->SID = $CHAPTERID;
		$Table->LIB_TITLE = $CHAPTER;
		$Table->ACTIVE = 1;
		$Table->store();
		$Chapters[$CHAPTERID] = $CHAPTERID;
		return true;

	}

	public static function getSphereList()
	{
		static $Spheres = null;
		if ( is_null( $Spheres ) )
		{
			$Query = 'select '
							. ' sid, '
							. ' lib_title '
							. ' from lib_sphere s '
							. ' order by s.lib_title asc';
			$Spheres = DB::LoadObjectList( $Query, 'SID' );
		}
		return $Spheres;

	}

	public static function getDepartmentList()
	{
		static $Departments = null;
		if ( is_null( $Departments ) )
		{
			$Query = 'select '
							. ' sid, '
							. ' lib_title '
							. ' from lib_department s '
							. ' order by s.lib_title asc';
			$Departments = DB::LoadObjectList( $Query, 'SID' );
		}
		return $Departments;

	}

	public static function GetChapterList()
	{
		static $Chapters = null;
		if ( is_null( $Chapters ) )
		{
			$Query = 'select '
							. ' sid, '
							. ' lib_title '
							. ' from lib_Chapter s '
							. ' where s.sid > 0 '
							. ' order by s.lib_title asc';
			$Chapters = DB::LoadObjectList( $Query, 'SID' );
		}
		return $Chapters;

	}

}
