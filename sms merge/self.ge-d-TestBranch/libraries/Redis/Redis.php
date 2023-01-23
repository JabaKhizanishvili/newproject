<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Redis
 *
 * @author teimuraz
 */
class XRedis extends Credis_Client
{
	public static $EmptyValue = '--empty--';
	public static $Query = [];
	public static $Times = [];

	/**
	 * 
	 * @staticvar type $Instance
	 * @return Credis_Client
	 */
	public static function GetInstance()
	{
		static $Instance = null;
		if ( is_null( $Instance ) )
		{
			$Start = microtime( true );
			$Index = hexdec( substr( md5( X_DOMAIN ), 0, 1 ) );
			self::$Query[] = 'Connect To Redis: Index - ' . $Index;
			$Instance = new self( X_REDIS_HOST );
			$Instance->auth( X_REDIS_PASSWORD );
			$Instance->select( $Index );
			$Instance->setOption( Redis::OPT_SCAN, Redis::SCAN_RETRY );
			self::$Times[] = microtime( true ) - $Start;
		}
		return $Instance;

	}

	public static function SC( $Collection, $Key = null, $Expired = 0 )
	{
		$Start = microtime( true );
		$Redis = self::GetInstance();
		self::$Query[] = 'Set Key: ' . $Key; //. ' - ' . json_encode( $Collection, JSON_UNESCAPED_UNICODE );
		if ( $Expired )
		{
			$Result = $Redis->setEx( $Key, $Expired, json_encode( $Collection, JSON_UNESCAPED_UNICODE ) );
		}
		else
		{
			$Result = $Redis->set( $Key, json_encode( $Collection, JSON_UNESCAPED_UNICODE ) );
		}
		self::$Times[] = microtime( true ) - $Start;
		return $Result;

	}

	public static function GC( $Key )
	{
		$Start = microtime( true );
		$Redis = self::GetInstance();
		self::$Query[] = 'Get Key: ' . $Key;
		$Result = json_decode( $Redis->get( $Key ) );
		self::$Times[] = microtime( true ) - $Start;
		return $Result;

	}

	public static function Delete( $Key )
	{
		$Start = microtime( true );
		$Redis = self::GetInstance();
		self::$Query[] = 'Delete Key: ' . $Key;
		$Result = $Redis->del( $Key );
		self::$Times[] = microtime( true ) - $Start;
		return $Result;

	}

	public static function GK( $Key )
	{
		$Start = microtime( true );
		$Redis = self::GetInstance();
		self::$Query[] = 'Search Keys: ' . $Key;
		$Result = $Redis->Keys( $Key );
		self::$Times[] = microtime( true ) - $Start;
		return $Result;

	}

	public static function GenKey()
	{
		$Args = implode( '|', func_get_args() );
		return strtoupper( md5( X_DOMAIN . '|' . $Args ) );

	}

	public static function GenSessionKey()
	{
		$Args = implode( '|', func_get_args() );
		return strtoupper( self::GetSessionPatern() . md5( X_DOMAIN . '|' . $Args ) );

	}

	public static function GenScopeKey()
	{
		$Args = implode( '-', Helper::CleanArray( func_get_args(), 'Str' ) );
		return strtoupper( DB_USER . '-' . $Args );

	}

	public static function GetSessionPatern()
	{
		return strtoupper( DB_USER . '-SESSION-' );

	}

	public static function getDBCache( $Scope, $Query, $QueryType = 'loadObjectList', $ObjectKey = null, $Expired = 86400, $Name = null )
	{
		static $Cache = [];
		if ( empty( $QueryType ) )
		{
			$QueryType = 'loadObjectList';
		}
		if ( empty( $Query ) )
		{
			return false;
		}
		if ( $Name )
		{
			$Key = self::GenScopeKey( $Scope, $QueryType, $ObjectKey, $Name, md5( $Query ) );
		}
		else
		{
			$Key = self::GenScopeKey( $Scope, $QueryType, $ObjectKey, md5( $Query ) );
		}

		if ( isset( $Cache[$Key] ) )
		{
			return $Cache[$Key];
		}
		$Data = XRedis::GC( $Key );
		if ( empty( $Data ) )
		{
			$Data = DB::{$QueryType}( $Query, $ObjectKey );
			if ( $Data )
			{
				XRedis::SC( $Data, $Key, $Expired );
			}
			else
			{
				XRedis::SC( self::$EmptyValue, $Key, $Expired );
			}
		}
		if ( is_string( $Data ) && $Data == self::$EmptyValue )
		{
			$Data = null;
		}
		if ( $QueryType == 'loadObjectList' )
		{
			$Cache[$Key] = (array) $Data;
		}
		else
		{
			$Cache[$Key] = $Data;
		}
		return $Data;

	}

	public static function CleanDBCache( $Scope )
	{
		static $Keys = [];
		if ( isset( $Keys[$Scope] ) )
		{
			return true;
		}
		$Keys[$Scope] = $Scope;
		$Key = XRedis::GenScopeKey( $Scope );
		$Data = XRedis::GK( $Key . '*' );
		if ( $Data )
		{
			foreach ( $Data as $K )
			{
				XRedis::Delete( $K );
			}
		}
		return true;

	}

	public static function Purge()
	{
		$Key = XRedis::GenScopeKey();
		$Data = XRedis::GK( $Key . '*' );
		if ( $Data )
		{
			foreach ( $Data as $K )
			{
				XRedis::Delete( $K );
			}
		}
		return true;

	}

}
