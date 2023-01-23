<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class controllerModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new controllerTable( );
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

	public function DeleteUndefined( $data, $mode = 'archive' )
	{

		foreach ( $data as $Item )
		{
			$Item = strtolower( trim( $Item ) );
			if ( empty( $Item ) )
			{
				continue;
			}
			$Q = 'DELETE slf_api_controllers c WHERE lower(trim(c.CONTROLLER_CODE)) = ' . DB::Quote( $Item );
			DB::Delete( $Q );
		}
		return true;

	}

	public function SaveData( $data )
	{
		if ( !$this->check_code( C::_( 'ID', $data, 0 ), C::_( 'CONTROLLER_CODE', $data ) ) )
		{
			XError::setError( 'controller code already used!' );
			return false;
		}

		$data['CONNECTION_STATUS'] = 0;

		return parent::SaveData( $data );

	}

	public function check_code( $id = 0, $code = '' )
	{
		if ( empty( $code ) )
		{
			return false;
		}

		$query = 'select '
						. ' count(*) '
						. ' from lib_controllers c '
						. ' where '
						. ' c.active >= 0 '
						. (!empty( $id ) ? ' and c.id != ' . (int) $id : '')
						. ' and c.controller_code = ' . DB::Quote( trim( $code ) )
		;
		$result = DB::LoadResult( $query );
		if ( $result > 0 )
		{
			return false;
		}

		return true;

	}

}
