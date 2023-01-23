<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class newModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new newsTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->TEXT = '';
			$this->Table->load( $id[0] );
			if ( !empty( $this->Table->TEXT0 ) )
			{
				for ( $K = 0; $K <= 9; $K++ )
				{
					$this->Table->TEXT .= $this->Table->{'TEXT' . $K};
				}
			}
			if ( !empty( $this->Table->UNPUBLISH_DATE ) )
			{
				if ( PDate::Get( $this->Table->UNPUBLISH_DATE )->toFormat( '%Y' ) == '2050' )
				{
					$this->Table->UNPUBLISH_DATE = null;
				}
			}
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}
		$imageSource = C::_( 'IMAGE', $data );
		$data['IMAGE'] = Helper::Base64ToImage( $imageSource, md5( $imageSource ) );

		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}

		$data['TEXT'] = C::_( 'params.TEXT', Request::get( 'post', 4 ) );

		$Text = $this->Table->SplitText( C::_( 'TEXT', $data ) );
		foreach ( $Text as $Key => $V )
		{
			$this->Table->{'TEXT' . $Key} = $V;
		}
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

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				$this->Table->ACTIVE = -2;
				$this->Table->MODIFY_USER = Users::GetUserID();
				$this->Table->MODIFY_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

	}

}
