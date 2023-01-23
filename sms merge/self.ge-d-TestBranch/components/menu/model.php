<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class MenuModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new MenusTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$LIB_REL_MENUS = C::_( 'ROLE_REL', $data, array() );
		$id = parent::SaveData( $data );

		$this->SaveRel( $data, $LIB_REL_MENUS, $id );
		DB::callProcedure( 'UpdateRoles' );
		XRedis::CleanDBCache( 'lib_roles' );
		XRedis::CleanDBCache( 'rel_roles_menus' );
		return $id;

	}

	public function SaveRel( $data, $LIB_REL_MENUS, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_roles_menus cp '
						. ' where '
						. ' cp.menu = ' . $id;
		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $LIB_REL_MENUS as $DD )
		{
			$query .= ' into rel_roles_menus '
							. ' (role, menu,params) '
							. 'values '
							. '('
							. (int) $DD
							. ','
							. (int) $id
							. ','
							. DB::Quote( json_encode( C::_( $DD, $data, array() ) ) )
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

}
