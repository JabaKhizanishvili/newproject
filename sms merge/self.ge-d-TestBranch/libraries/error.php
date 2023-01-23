<?php

/**
 * Description of error
 *
 * @author teimuraz.kevlishvili
 */
class XError
{
	protected static $Error = array();
	protected static $Info = array();
	protected static $Message = array();

	public static function getMessage()
	{
		return self::$Message;

	}

	public static function setMessage( $Message )
	{
		self::$Message[] = $Message;
		Session::Set( '_message', self::$Message );
		return true;

	}

	public static function getError()
	{
		return self::$Error;

	}

	public static function setError( $Error )
	{
		self::$Error[] = $Error;
		Session::Set( '_errors', self::$Error );
		return true;

	}

	public static function getErrors()
	{
		$Error = strip_tags( Request::getVar( 'error' ) );
		$Errors = (array) Session::get( '_errors' );
		Session::Set( '_errors', array() );
		if ( !empty( $Error ) )
		{
			$Errors[] = $Error;
		}
		return $Errors;

	}

	public static function getMessages()
	{
		$Messages = (array) Session::get( '_message' );
		Session::Set( '_message', array() );
		$Msg = strip_tags( Request::getVar( 'msg', null, 'get', 'allnum' ) );
		if ( !empty( $Msg ) )
		{
			$Messages[] = $Msg;
		}
		return $Messages;

	}

	public static function isErrors()
	{
		return count( self::$Error );

	}

	public static function setInfo( $Info )
	{
		self::$Info[] = $Info;
		Session::Set( '_info', self::$Info );
		return true;

	}

	public static function isInfo()
	{
		return count( self::$Info );

	}

	public static function getInfo()
	{
		return self::$Info;

	}

	public static function getInfos()
	{
		$Infos = (array) Session::get( '_info' );
		Session::Set( '_info', array() );
		return $Infos;

	}

}
