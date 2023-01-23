<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class F_salary_typeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new F_salary_typeTable( );
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
				$exist = Xhelp::CheckWorkersInOrg( $ID, 'SALARYTYPE' );
				if ( count( $exist ) )
				{
					XError::setError( 'workers detected in this id!' );
					return false;
				}
			}
		}

		return parent::SaveData( $data );

	}

}
