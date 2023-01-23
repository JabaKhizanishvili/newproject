<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class certificateModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new certificateTable( );
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
		$data['ACTIVE'] = C::_( 'ACTIVE', $data, 1 );
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
		$ID = $this->Table->insertid();
		if ( !$ID )
		{
			return false;
		}
		return true;

	}

}
