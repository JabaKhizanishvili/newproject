<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class apiModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new apiTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
			$this->Table->API_TABLES = '';
			if ( !empty( $this->Table->API_TABLES0 ) )
			{
				for ( $K = 0; $K < 10; $K++ )
				{
					$this->Table->API_TABLES .= $this->Table->{'API_TABLES' . $K};
				}
			}
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$tables = C::_( 'API_TABLES', $data, [] );
		if ( count( $tables ) )
		{
			$on = C::_( 'ON', $tables, [] );
			unset( $tables['ON'] );
			foreach ( $tables as $key => $value )
			{
				if ( !in_array( $key, $on ) )
				{
					unset( $tables[$key] );
				}
			}
			$data['API_TABLES'] = json_encode( $tables );
		}

		$Text = $this->Table->SplitText( C::_( 'API_TABLES', $data ), ':' );
		$n = 10;
		for ( $index = 0; $index < $n; $index++ )
		{
			$this->Table->{'API_TABLES' . $index} = C::_( $index, $Text, '' );
		}
		$id = C::_( 'ID', $data );
		if ( empty( $id ) )
		{
			$data['APIID'] = $this->GUID();
			$data['APIKEY'] = $this->GUID();
		}
		$data['CHANGE_USER'] = Users::GetUserID();
		$data['CHANGE_DATE'] = PDate::Get()->toFormat();
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

		if ( empty( $id ) )
		{
			XError::setMessage( Text::_( 'Data Saved!' ) . '<br><br><span class="infoMSG"><span>' . Text::_( 'apiid' ) . ': </span>' . C::_( 'APIID', $data ) . '</span><br><br><span class="infoMSG"><span>' . Text::_( 'apikey' ) . ': </span>' . C::_( 'APIKEY', $data ) ) . '</span>';
		}
		else
		{
			XError::setMessage( 'Data Saved!' );
		}
		return $this->Table->insertid();

	}

	public function GUID()
	{
		if ( function_exists( 'com_create_guid' ) === true )
		{
			return trim( com_create_guid(), '{}' );
		}

		$data = openssl_random_pseudo_bytes( 16 );
		$data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 ); // set version to 0100
		$data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 ); // set bits 6-7 to 10
		return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );

	}

}
