<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XTranslate
 *
 * @author teimuraz
 */
class XTranslate
{
	public static function ShowLangs()
	{
		if ( count( self::GetLangs() ) > 1 )
		{
			return true;
		}
		return false;

	}

	/**
	 * 
	 * @staticvar Array $Langs
	 * @return type
	 */
	public static function GetLangs()
	{
		static $Langs = null;
		if ( is_null( $Langs ) )
		{
			$Query = 'select t.lib_code, t.lib_title, t.def_lang from LIB_LANGUAGES t where t.active = 1';
			$Langs = XRedis::getDBCache( 'lib_languages', $Query, 'LoadObjectList', 'LIB_CODE' );
		}
		if ( empty( $Langs ) )
		{
			$Langs = [];
		}
		return (array) $Langs;

	}

	public static function GetDefaultLang()
	{
		$DefLang = null;
		if ( is_null( $DefLang ) )
		{
			$Langs = self::GetLangs();
			foreach ( $Langs as $Lang )
			{
				if ( C::_( 'DEF_LANG', $Lang ) == 1 )
				{
					$DefLang = $Lang;
					break;
				}
			}
		}
		return $DefLang;

	}

	public static function GetCurrentLang()
	{
		$SCLang = null;
		if ( is_null( $SCLang ) )
		{
			$SCLangD = Request::getCmd( 'Lang', null, 'cookie' );
			if ( empty( $SCLangD ) )
			{
				$DefLang = self::GetDefaultLang();
				$SCLang = C::_( 'LIB_CODE', $DefLang );
				self::SetCurrentLang( $SCLang );
			}
			else
			{
				$SCLang = $SCLangD;
			}
		}
		return $SCLang;

	}

	public static function SetLanguage()
	{
		$CurrentLang = self::GetCurrentLang();
		$SetLanguage = self::GetLangCodeFromCode( trim( Request::getCmd( 'setLanguage', null, 'get' ) ) );
		if ( !empty( $SetLanguage ) && $CurrentLang != $SetLanguage )
		{
			self::SetCurrentLang( $SetLanguage );
			Users::Redirect( '/?ref=LangSwitch' );
		}

	}

	public static function SetCurrentLang( $SCLang )
	{
		$cookie = new XCookie( 'Lang' );
		$cookie->setValue( $SCLang );
		$cookie->setExpiryTime( time() + 60 * 60 * 24 * 30 );
		$cookie->setPath( '/' );
		$cookie->setHttpOnly( true );
		$cookie->setSecureOnly( true );
		$cookie->setSameSiteRestriction( 'Strict' );
		return $cookie->saveAndSet();

	}

	public static function GetLangCodeFromCode( $Code )
	{
		$Langs = self::GetLangs();
		foreach ( $Langs as $Lang )
		{
			if ( C::_( 'LIB_CODE', $Lang ) == $Code )
			{
				return $Code;
			}
		}
		return false;

	}

	public static function _( $String, $Scope = 'content', $Default = null )
	{
		if ( empty( $Scope ) )
		{
			$Scope = 'content';
		}

		$CurrentLang = self::GetCurrentLang();
		if ( is_null( $Default ) )
		{
			$Default = C::_( 'LIB_CODE', self::GetDefaultLang() );
		}
		if ( $CurrentLang == $Default )
		{
			return $String;
		}
		return self::Translate( $String, $CurrentLang, $Scope );

	}

	public static function Translate( $String, $To, $Scope = 'content' )
	{
		$Hash = md5( $To . '|' . mb_strtolower( $String ) );
		$Translate = self::GetLocalTranslate( $Hash, $To );
		if ( !$Translate )
		{
			$Translate = self::GetAPITranslate( $String, $To, $Scope );

			if ( !$Translate )
			{
				return $String;
			}
			self::SetLocalTranslate( $Hash, $To, $Translate );
		}
		$Result = C::_( 'Output', $Translate );
		if ( empty( $Result ) )
		{
			$Result = $String;
		}
		return $Result;

	}

	public static function GetLocalTranslate( $Hash, $CurrentLang )
	{
		$Domain = basename( PATH_LOGS );
		$UserPath = X_PATH_TRANSLATE_USER . DS . $Domain . DS . $CurrentLang . DS . $Hash;
		if ( File::exists( $UserPath ) )
		{
			return json_decode( file_get_contents( $UserPath ) );
		}


		$TranslateFile = X_PATH_TRANSLATE . DS . $CurrentLang . DS . $Hash;
		if ( File::exists( $TranslateFile ) )
		{
			return json_decode( file_get_contents( $TranslateFile ) );
		}
		return false;

	}

	public static function SetLocalTranslate( $Hash, $CurrentLang, $Translate )
	{
		$Folder = X_PATH_TRANSLATE . DS . $CurrentLang;
		if ( !Folder::exists( $Folder ) )
		{
			Folder::create( $Folder, 0777 );
		}
		return file_put_contents( $Folder . DS . $Hash, json_encode( $Translate, JSON_UNESCAPED_UNICODE ) );

	}

	public static function GetAPITranslate( $String, $CurrentLang, $Scope )
	{
		$CURL = new XCurl();
		$CURL->setTimeout( 2 );
		$Post = array(
				'scope' => $Scope,
				'to' => $CurrentLang,
				'text' => $String
		);
		$CURL->post( X_TRANSLATE_API, $Post );
		$Code = $CURL->getHttpStatusCode();
		if ( $Code != 200 )
		{
			return false;
		}
		return $CURL->response;

	}

}
