<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class Working_rateModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new Working_rateTable( );
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
		$data['WORK_DURATION'] = number_format( $data['WORK_DURATION'], 1 );
		return parent::SaveData( $data );

	}

}
