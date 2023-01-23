<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class Access_managerModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new Access_managerTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
			$this->Table->LIB_REL_ACCESS = implode( ',', $this->getGroupWorkers() );
		}

		return $this->Table;

	}

	public function SaveData( $data )
	{
		$LIB_REL_ACCESS = C::_( 'LIB_REL_ACCESS', $data );
		$id = parent::SaveData( $data );
		if ( !$this->SaveRel( $data, $LIB_REL_ACCESS, $id ) )
		{
			return false;
		}
		return $id;

	}

	public function SaveRel( $data, $LIB_REL_ACCESS, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_access_manager cp '
						. ' where '
						. ' cp.access_id = ' . $id;
		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $LIB_REL_ACCESS as $DD )
		{
			$query .= ' into rel_access_manager '
							. ' (access_id, controller) '
							. 'values '
							. '('
							. DB::Quote( (int) $id )
							. ','
							. DB::Quote( (int) $DD )
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function getGroupWorkers()
	{
		$ID = $this->Table->ID;
		if ( empty( $ID ) )
		{
			return '';
		}
		$query = 'select controller from rel_access_manager where access_id = ' . DB::Quote( $ID );
		return DB::LoadList( $query );

	}

}
