<?php
require_once PATH_BASE . DS . 'libraries/DB.php';

class Session
{
	public static function Get( $name, $db = false, $update_sess = true )
	{
		if ( $db )
		{
			$Data = self::_LoadSession();
		}
		else
		{
			$Data = self::GetNameSpace();
		}
		//Check Data Exists
		if ( isset( $Data[$name] ) )
		{
			return $Data[$name];
		}

	}

	public static function Set( $name, $Value = '', $db = false )
	{
		$Data = self::GetNameSpace();
		$Data[$name] = $Value;
		self::SetNameSpace( $Data );

	}

	public static function Destroy()
	{
		if ( isset( $_SESSION[SESSION_SPASE] ) )
		{
			unset( $_SESSION[SESSION_SPASE] );
		}
		$session_id = self::getSessionID();

//		$sql = 'delete from ' . DB_SCHEMA . '.system_sessions s '
//						. ' where s.session_id = ' . DB::Quote( $session_id );
//		DB::Update( $sql );
		$Key = XRedis::GenSessionKey( $session_id );
		XRedis::Delete( $Key );
		self::UnsetCookie();

	}

	public static function getSessionID()
	{
		static $session_id = null;
		if ( empty( $session_id ) )
		{
			$SessionName = self::getSessionName();
			$session_id = Collection::getVar( $SessionName, 'cookie', null );
			if ( empty( $session_id ) )
			{
				$session_id = md5( session_id() . '|' . time() . '|BIGUOBKJHYUIGOKHLJHG' );
			}
		}
		return $session_id;

	}

	private static function GetNameSpace()
	{
		if ( isset( $_SESSION[SESSION_SPASE] ) )
		{
			return $_SESSION[SESSION_SPASE];
		}
		return array();

	}

	private static function SetNameSpace( $Data )
	{
		$_SESSION[SESSION_SPASE] = $Data;

	}

	public static function SaveDBSession( $UserData, $remember = 0, $username = '', $update_sess = true, $SessionToken = null )
	{
		$StandardDuration = Helper::getConfig( 'standard_session_duration' );
		if ( empty( $StandardDuration ) )
		{
			$StandardDuration = 30;
		}
		$Duration = Helper::getConfig( 'extended_session_duration' );
		if ( empty( $Duration ) )
		{
			$Duration = 3000;
		}
		$session_id = self::getSessionID();
//		$session = self::_getSession( $session_id );
		$SDuration = 0;
		if ( $remember )
		{
			$endDate = new PDate( time() + $Duration * 60 );
			$SDuration = $Duration * 60;
		}
		else
		{
			$endDate = new PDate( time() + $StandardDuration * 60 );
			$SDuration = $StandardDuration * 60;
		}
		if ( $update_sess )
		{
			$UserData->SESSION_ID = $session_id;
			$UserData->SESSION_TYPE = $remember;
			$Key = XRedis::GenSessionKey( $session_id );
			XRedis::SC( $UserData, $Key, $SDuration );
			self::Set( '_user', $UserData );
			self::SetCookie( $session_id, $remember );
		}
		return $session_id;

	}

	private static function _getSession( $id )
	{
		static $Session = null;
		if ( empty( $Session ) )
		{
			$Key = XRedis::GenSessionKey( $id );
			$Data = XRedis::GC( $Key );
			if ( $Data )
			{
				$Session = $Data;
			}
		}

		return $Session;

	}

	private static function _purge()
	{
		$Rand = random_int( 1, 10 );
		if ( $Rand != 1 )
		{
			return;
		}
		$query = 'delete from ' . DB_SCHEMA . '.system_sessions t '
						. ' where t.end_date < sysdate '
		;
		$data = DB::Update( $query );
		return $data;

	}

	private static function _LoadSession( $update = false, $update_sess = true )
	{
		static $Data = null;
		if ( empty( $Data ) || $update )
		{
			$DBData = self::_getSession( self::getSessionID() );
			if ( !empty( $DBData ) )
			{
				$remember = Collection::getVar( 'SESSION_TYPE', $DBData, 0 );
				$username = Collection::getVar( 'USERNAME', $DBData, '' );
				self::Set( 'SessionTableID', Collection::getVar( 'ID', $DBData, '' ) );
				self::SetCookie( Collection::getVar( 'SESSION_ID', $DBData ), $remember );
				self::SaveDBSession( $DBData, $remember, $username, $update_sess );
				$Data['_user'] = (array) $DBData;
			}
		}
		return $Data;

	}

	public static function SetCookie( $session_id, $remember = 0 )
	{
		$SessionName = self::getSessionName();
		if ( $remember )
		{
			$Valid = time() + EXTENDED_SESSION_PERIOD * 60;
		}
		else
		{
			$Valid = time() + SESSION_PERIOD * 60;
		}
		setcookie( $SessionName, $session_id, $Valid, COOKIE_PATH );

	}

	public static function getSessionName()
	{
		static $SessionName = null;
		if ( empty( $SessionName ) )
		{
			$SessionName = md5( Request::getVar( 'REMOTE_ADDR', '127.0.0.1', 'server' )
							. '|'
							. Request::getVar( 'HTTP_USER_AGENT', 'Unknown', 'server' )
							. '|'
							. 'BIGUOBKJHYUIGOKHLJHG'
			);
		}
		return $SessionName;

	}

	public static function UnsetCookie()
	{
		$SessionName = self::getSessionName();
		$Valid = time() - 3600;
		setcookie( $SessionName, ' ', $Valid, COOKIE_PATH );

	}

}
