<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class UnitModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new UnitsTable( );
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
        $ID = C::_( 'ID', $data );
        if ( $ID )
        {
            $this->Table->load( $ID );
            if ( C::_( 'ACTIVE', $data ) == 0 )
            {
                $exist = Xhelp::CheckWorkersInOrg( $ID, 'ORG_PLACE', 'lss' );
                if ( count( $exist ) )
                {
                    XError::setError( 'workers detected in this work place!' );
                    return false;
                }
            }
        }

        $Result = parent::SaveData( $data );
		if ( empty( $Result ) )
		{
			return false;
		}
		$Org = C::_( 'ORG', $this->Table );
		return Units::Rebuild( $Org );

	}

}
