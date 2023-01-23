<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class unitorgModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new unitorgTable( );
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
				$exist = Xhelp::CheckWorkersInOrg( $ID, 'ORG' );
				if ( count( $exist ) )
				{
					XError::setError( 'workers detected in this org!' );
					return false;
				}
			}
		}


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
		$IDx = $this->Table->insertid();
		if ( $ID )
		{
			Units::UpdateRoot( $data );
		}
		else
		{
			Units::InsertRoot( $this->Table );
		}
		return $IDx;

	}

}
