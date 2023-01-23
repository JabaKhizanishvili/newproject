<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class DoorModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new DoorTable( );
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
		$id = C::_( 'ID', $data, '' );
		$Q = 'select count(id) from lib_doors where '
						. ' active != -2 AND CODE = ' . DB::Quote( trim( C::_( 'CODE', $data ) ) )
						. ($id ? ' and ID != ' . $id : '')
		;
		if ( DB::LoadResult( $Q ) > 0 )
		{
			return false;
		}

		return parent::SaveData( $data );

	}

}
