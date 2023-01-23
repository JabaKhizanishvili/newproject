<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class Msg_templateModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new msg_templateTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->EMAIL = '';
			$this->Table->load( $id[0] );
			if ( !empty( $this->Table->EMAIL0 ) )
			{
				for ( $K = 0; $K <= 9; $K++ )
				{
					$this->Table->EMAIL .= $this->Table->{'EMAIL' . $K};
				}
			}
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		if ( !$this->Table->bind($data) )
		{
			return false;
		}
		
		$Functions = $this->Table->SplitText( C::_( 'EMAIL', $data ), 'EMAIL' );
		$this->Table->bind( $Functions );
		
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
		return $this->Table->insertid();

	}

}
