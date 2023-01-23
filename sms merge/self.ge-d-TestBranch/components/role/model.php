<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class RoleModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new RolesTable( );
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
		$LIB_REL_MENUS = C::_( 'LIB_REL_MENUS', $data, [] );
		$data['LIB_REL_MENUS'] = implode( ',', $LIB_REL_MENUS );
		$id = parent::SaveData( $data );
		if ( !$this->SaveRel( $data, $LIB_REL_MENUS, $id ) )
		{
			return false;
		}
		XRedis::CleanDBCache( 'lib_menus' );
		XRedis::CleanDBCache( 'rel_roles_menus' );
		return $id;

	}

	public function SaveRel( $data, $LIB_REL_MENUS, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_roles_menus cp '
						. ' where '
						. ' cp.role = ' . $id;
		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $LIB_REL_MENUS as $DD )
		{
			$query .= ' into rel_roles_menus '
							. ' (role, menu,params) '
							. 'values '
							. '('
							. (int) $id
							. ','
							. (int) $DD
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
