<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class LinkModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new LinkTable( );
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
		return parent::SaveData( $data );

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$this->Table->Delete( $id );
			}
		}
		return true;

	}

}
