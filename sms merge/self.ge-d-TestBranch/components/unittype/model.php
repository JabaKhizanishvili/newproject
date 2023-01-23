<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class UnitTypeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new UnitTypeTable( );
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
		$ID = parent::SaveData( $data );
		$Def = C::_( 'DEF', $data, 0 );
		if ( $Def && $ID )
		{
			$Query = 'update LIB_UNITTYPES t set t.def = 0 where t.id <> ' . $ID . '; commit ';
			DB::callProcedure( $Query );
		}
		return $ID;

	}

}
