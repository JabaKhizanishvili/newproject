<?php

class XTableAutoload
{
	public static function TableAutoLoad( $Class )
	{
		if ( preg_match( '/^Table/i', $Class ) )
		{
			$Instance = XTableAutoload::getInstance();
			return $Instance->LoadClass( $Class );
		}

	}

	protected $Folder = '';
	protected $Files = array();

	public static function getInstance()
	{
		static $Instance = null;
		if ( is_null( $Instance ) )
		{
			$Instance = new self();
		}
		return $Instance;

	}

	public function __construct()
	{
		$this->Folder = PATH_BASE . DS . 'buffer' . DS . 'interfaces';
		$Files = Folder::files( $this->Folder, '.php' );
		foreach ( $Files as $File )
		{
			$Class = mb_strtolower( File::stripExt( $File ) );
			$this->Files[$Class] = $File;
		}

	}

	public function LoadClass( $Table )
	{
		$ClassName = mb_strtolower( $Table );
		$File = C::_( $ClassName, $this->Files, false );
		if ( !empty( $File ) )
		{
			return require_once $this->Folder . DS . $File;
		}
		return false;

	}

}

spl_autoload_register( array( 'XTableAutoload', 'TableAutoload' ) );
