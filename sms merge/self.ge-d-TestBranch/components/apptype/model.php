<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class AppTypeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new AppTypesTable( );
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
}