<?php
defined( 'DS' ) or die( 'Restricted Access!' );

/**
 * @version		$id: controller.php 1 2011-07-13 05:09:23Z WS Team $
 * @package	XMLInterface.Framework
 * @copyright	Copyright (C) 2009 - 2010 Self.Ge LTD. All rights reserved.
 * @license      Commercial License
 */
abstract class X
{
	/**
	 * 
	 * @param type $PClass
	 * @return boolean
	 */
	public static function XAutoload( $PClass )
	{
		static $ClassMapData = null;
		if ( is_null( $ClassMapData ) )
		{
			$ClassMapData = self::GetClassMap();
		}
		$Class = mb_strtolower( $PClass );
		$Path = self::_( $Class, $ClassMapData, false );

		if ( empty( $Path ) )
		{
			return XTableAutoload::TableAutoLoad( $PClass );
		}


		if ( $Path )
		{
			return self::XLoad( $Path );
		}

	}

	public static function XLoad( $Path )
	{
		$File = X_PATH_LIBRARIES . DS . str_replace( '.', DS, $Path ) . '.php';
		if ( is_file( $File ) )
		{
			return require_once $File;
		}

	}

	public static function GetClassMap()
	{
		$File = X_PATH_LIBRARIES . DS . 'Class.Map.php';
		if ( is_file( $File ) )
		{
			require_once $File;
		}
		else
		{
			$XMapData = array();
		}
		return $XMapData;

	}

	protected static function _( $Key, $Collection, $Default = '' )
	{
		$Keys = explode( '.', $Key );
		$Data = $Collection;
		foreach ( $Keys as $K )
		{
			if ( is_object( $Data ) )
			{
				$Data = (array) $Data;
			}
			if ( !isset( $Data[$K] ) )
			{
				return $Default;
			}
			$Data = $Data[$K];
		}
		return $Data;

	}

}

spl_autoload_register( array( 'X', 'XAutoload' ) );
