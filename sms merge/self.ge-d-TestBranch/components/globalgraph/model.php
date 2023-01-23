<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class GlobalGraphModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new GlobalGraphTable( );
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
		$collect = [];
		foreach ( $data as $value )
		{
			$collect[] = $value;
		}
		$collect = array_slice( $collect, -7 );
		array_unshift( $collect, $data['ID'], $data['ACTIVE'] );
		$FData = implode( '|', $collect );
		$md5 = md5( $FData );
		$data['CHECKSUM'] = $md5;

		return parent::SaveData( $data );

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$existGraphs = Xhelp::CheckStandartTimeInWorkers( $id );
				if ( count( $existGraphs ) > 0 )
				{
					XError::setError( 'global graph is bind to standard graph!' );
					continue;
				}
				parent::Delete( (array) $id, $mode );
			}
		}
		else
		{
			return false;
		}
		return true;

	}

}
