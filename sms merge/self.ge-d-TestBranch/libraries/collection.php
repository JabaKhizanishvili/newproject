<?php

/**
 * @version		$Id: array.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Request
 * @copyright	Copyright (C) 2009 - 2010 Self.Ge LTD. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
abstract class Collection
{
	/**
	 * @param $key
	 * @param $Collection
	 * @param string $default
	 * @return string
	 */
	public static function getVar( $key, $Collection, $default = '' )
	{
		if ( is_array( $Collection ) )
		{
			return self::getValue( $key, $Collection, $default );
		}
		if ( is_object( $Collection ) )
		{
			return self::getValue( $key, (array) $Collection, $default );
		}
		if ( is_string( $Collection ) )
		{
			switch ( mb_strtolower( $Collection ) )
			{
				case 'get':
					return self::getValue( $key, $_GET, $default );
					break;
				case 'post':
					return self::getValue( $key, $_POST, $default );
					break;
				case 'request':
					return self::getValue( $key, $_REQUEST, $default );
					break;
				case 'server':
					return self::getValue( $key, $_SERVER, $default );
					break;
				case 'cookie':
					return self::getValue( $key, $_COOKIE, $default );
					break;
				default:
					return self::getValue( $key, $_GET, $default );
					break;
			}
		}
		return $default;

	}

	public static function getVarIf( $key, &$Collection, $default = '', $ifVar = null )
	{
		$value = self::getVar( $key, $Collection, NULL );
		if ( empty( $value ) )
		{
			$value = self::getVar( $ifVar, $Collection, $default );
		}
		return $value;

	}

	protected static function getValue( $key, $Collection, $default = '' )
	{
		$keys = explode( '.', $key );
		$Data = $Collection;
		foreach ( $keys as $k )
		{
			if ( is_object( $Data ) )
			{
				$Data = (array) $Data;
			}
			if ( !isset( $Data[$k] ) )
			{
				return $default;
			}
			$Data = $Data[$k];
		}
		return $Data;

	}

	public static function toString( $Collection, $newLine = PHP_EOL )
	{
		if ( is_array( $Collection ) )
		{
			return self::_toString( $Collection, $newLine );
		}
		else if ( is_object( $Collection ) )
		{
			return self::_toString( $Collection, $newLine );
		}
		else
		{
			return $Collection . $newLine;
		}

	}

	private static function _toString( $Collection, $newLine, $level = 0 )
	{
		$Return = '';
		if ( empty( $Collection ) )
		{
			return $newLine;
		}
		$Indent = str_repeat( ' - -  ', $level );
		foreach ( $Collection as $key => $value )
		{
			if ( is_array( $value ) )
			{
				$Return .= $Indent . $key . ' = ' . $newLine . self::_toString( $value, $newLine, $level + 1 ) . $newLine;
			}
			if ( is_object( $value ) )
			{
				$Return .= $Indent . $key . ' = ' . $newLine . self::_toString( $value, $newLine, $level + 1 ) . $newLine;
			}
			else
			{
				$Return .= $Indent . $key . ' = ' . $value . $newLine;
			}
		}
		return $Return;

	}

	public static function _( $key, $Collection, $default = '' )
	{
		return self::getVar( $key, $Collection, $default );

	}

	public static function get( $key, $Collection, $default = '' )
	{
		return self::getVar( $key, $Collection, $default );

	}

}

abstract class C extends Collection
{
	
}
