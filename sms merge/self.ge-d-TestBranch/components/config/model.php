<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class ConfigModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new ConfigTable( );
		parent::__construct( $params );

	}

	public function getItems( $C )
	{
		if ( !$C )
		{
			$C = $this->_option;
		}
		$query = 'select t.* from system_config t ';
		$data = DB::LoadObjectList( $query, 'KEY' );
		$return = new stdClass();
		foreach ( $data as $d )
		{
			$key = $d->KEY;
			$return->{$key } = $d->VALUE;
		}
		return $return;

	}

	public function SaveData( $data )
	{
		$C = Request::getCMD( 'c', false );

		if ( empty( $C ) )
		{
			XError::setError( 'config error!' );
			return false;
		}
		if ( !$this->checkConfig( $data, $C ) )
		{
			return false;
		}
		if ( $C == 'config' )
		{
			$imageSource = C::_( 'system_logo', $data );
			$Photo = Helper::Base64ToImage( $imageSource, md5( $imageSource ) );
			if ( empty( $Photo ) )
			{
				return false;
			}
			$data['system_logo'] = $Photo;
		}
		$this->Table->Clear( $C );

		foreach ( $data as $key => $value )
		{
			if ( is_array( $value ) )
			{
				$value = implode( '|', $value );
			}
			if ( is_array( $value ) )
			{
				$value = implode( '|', $value );
			}
			if ( !$this->Table->bind( array( 'KEY' => $key, 'VALUE' => $value, 'C_SCOPE' => $C ) ) )
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
			$this->Table->reset();
		}
		XRedis::CleanDBCache( $this->Table->getTableName() );
		return true;

	}

	public function checkConfig( $data, $scope )
	{
		$collect = [];
		foreach ( array_keys( $data ) as $value )
		{
			$collect[] = '\'' . $value . '\'';
		}
		$names = implode( ', ', $collect );

		$query = ' select key, c_scope, value from SYSTEM_CONFIG where KEY in (' . $names . ') and c_scope != ' . DB::Quote( $scope );
		$result = DB::LoadObjectList( $query );
		$E = 0;
		foreach ( $result as $value )
		{
			$key = C::_( 'KEY', $value );
			$option = C::_( 'C_SCOPE', $value );
			$val = C::_( 'VALUE', $value );
			$path = PATH_BASE . DS . 'components' . DS . $option . DS . 'configuration.xml';
			if ( file::exists( $path ) )
			{
				$content = file_get_contents( $path );
				$k = 'name="' . $key . '"';
				if ( preg_match( '/' . $k . '/i', $content ) )
				{
					$error = Text::_( 'Key:' ) . ' "' . C::_( 'KEY', $value ) . '" ' . Text::_( 'already exists in Component:' ) . ' "' . C::_( 'C_SCOPE', $value ) . '"';
					XError::setError( $error );
					$E++;
				}
				else
				{
					$query = ' update system_config set c_scope = \'' . $scope . '\' where key=\'' . $key . '\' and c_scope = \'' . $option . '\'';
					DB::Update( $query );
				}
			}
		}
		if ( $E > 0 )
		{
			return false;
		}

		return true;

	}

}
