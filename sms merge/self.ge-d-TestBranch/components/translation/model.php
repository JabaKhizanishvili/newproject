<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class TranslationModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new TranslationTable();
		parent::__construct( $params );

	}

	public function getTerm( $Hash = '', $Lng = '' )
	{
		$hash = empty( $Hash ) ? Request::getVar( 'hash' ) : $Hash;
		$lng = empty( $Lng ) ? Request::getVar( 'lng' ) : $Lng;

		$File = X_PATH_BUFFER . DS . 'Translates' . DS . $lng . DS . $hash;
		if ( !is_file( $File ) )
		{
			return [];
		}

		$Result = json_decode( file_get_contents( $File ) );

		$data = [];
		$data['FROM_TEXT'] = C::_( 'Input', $Result );
		$data['TO_TEXT'] = C::_( 'Output', $Result );
		$data['LIB_FROM'] = C::_( 'From', $Result );
		$data['LIB_TO'] = C::_( 'To', $Result );
		$data['FROM_TEXT_HASH'] = C::_( 'Hash', $Result );

		return $data;

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

	public function SaveData( $data, $edited = [] )
	{
		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}
		elseif ( !empty( $edited ) )
		{
			$this->Table->load( C::_( 'FROM_TEXT_HASH', $data ), 'FROM_TEXT_HASH' );
			$data['EDIT_TEXT'] = C::_( 'EDIT_TEXT', $edited );
		}

		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}

		$this->Table->ACTIVE = 1;
		if ( !$this->Table->check() )
		{
			return false;
		}

		if ( !$this->Table->store() )
		{
			return false;
		}

		$this->Buffer( $this->Table );
		return true;

	}

	public function Buffer( $table, $mode = 'save' )
	{
		if ( empty( $table ) )
		{
			return false;
		}

		$data['From'] = C::_( 'LIB_FROM', $table );
		$data['To'] = C::_( 'LIB_TO', $table );
		$data['Input'] = C::_( 'FROM_TEXT', $table );
		$data['Output'] = C::_( 'EDIT_TEXT', $table );
		$data['Hash'] = C::_( 'FROM_TEXT_HASH', $table );

		$UserTranslates = PATH_BASE . DS . 'buffer' . DS . 'UserTranslates';
		if ( !is_dir( $UserTranslates ) )
		{
			Folder::create( $UserTranslates );
		}

		$domain = C::_( 0, (array) explode( '.', $_SERVER['SERVER_NAME'] ) );
		$Folder_domain = $UserTranslates . DS . $domain;
		if ( !is_dir( $Folder_domain ) )
		{
			Folder::create( $Folder_domain );
		}

		$lng = C::_( 'LIB_TO', $table );
		$Folder_lng = $Folder_domain . DS . $lng;
		if ( !is_dir( $Folder_lng ) )
		{
			Folder::create( $Folder_lng );
		}

		$content = json_encode( $data, JSON_UNESCAPED_UNICODE );
		$hash = $lng = C::_( 'FROM_TEXT_HASH', $table );
		$File_Hash = $Folder_lng . DS . $hash;

		if ( $mode == 'delete' )
		{
			unlink( $File_Hash );
		}
		else
		{
			file_put_contents( $File_Hash, $content );
		}

		return true;

	}

	public function D_elete( $data )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$this->Table->load( $id );
				$this->Buffer( $this->Table, 'delete' );
				$this->Table->ACTIVE = -2;
				$this->Table->store();
			}
		}
		return true;

	}

}
