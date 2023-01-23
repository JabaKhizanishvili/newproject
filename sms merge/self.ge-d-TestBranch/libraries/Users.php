<?php
require_once PATH_BASE . DS . 'components' . DS . 'helper.php';

/**
 * Users Class
 * @author  Teimuraz Kevlishvili
 * @copyright Copyright (c) 2012, Teimuraz Kevlishvili
 */
class Users
{
	static $_user = null;

	public static function InitUser( $ExtendSession = true )
	{
		$userData = Session::Get( '_user', true, $ExtendSession );
		if ( $userData )
		{
			self::$_user = $userData;
			$FIRSTNAME = C::_( 'FIRSTNAME', $userData );
			$LASTNAME = C::_( 'LASTNAME', $userData );
			if ( empty( $FIRSTNAME ) || empty( $LASTNAME ) )
			{
				$option = Request::getVar( 'option', '' );
				$Options = array(
						'profile' => 1,
						'login' => 1,
						'logout' => 1
				);
				if ( !isset( $Options[$option] ) )
				{
					XError::setError( 'You Must Fill Profile Data!' );
					Users::Redirect( '?option=profile' );
				}
			}
			Sentry\configureScope( function ( Sentry\State\Scope $scope ): void
			{
				$scope->setUser( [ 'id' => C::_( 'ID', self::$_user ),
						'username' => C::_( 'FIRSTNAME', self::$_user ) . ' ' . C::_( 'LASTNAME', self::$_user ),
						'email' => C::_( 'EMAIL', self::$_user ),
						'ip_address' => Request::getVar( 'REMOTE_ADDR', '', 'server' )
				] );
			} );
		}
		$option = Request::getVar( 'option' );
		if ( $option == 'restoreloginas' )
		{
			self::RestoreLoginAs();
		}
		if ( $option == 'login' )
		{
			$task = Request::getCmd( 'task', '' );
			$AzureSSO = Helper::getConfig( 'azure_sso_on' );
			$AzureSAMLSSO = Helper::getConfig( 'azure_saml_sso_on' );
			$Return = Request::getVar( 'return' );
			if ( !empty( $task ) && $task == 'sso' && $AzureSSO )
			{
				self::AzureSSO();
			}
			if ( !empty( $task ) && $task == 'saml_sso' && $AzureSAMLSSO )
			{
				self::AzureSAMLSSO( $Return );
			}
			elseif ( !Users::login( Request::getVar( 'username', '', 'post' ), Request::getVar( 'password', '', 'post' ) ) )
			{
				Session::Destroy();
				$uri = URI::getInstance();
				$uri->setVar( 'ref', 'logginFailed' );
				$uri->delVar( 'option' );
				$uri->delVar( 'msg' );
				$redirect = $uri->toString( array( 'path', 'query', 'fragment' ) );
				$msg = Text::_( 'Login Failed!' );
				XError::setError( $msg );
				self::Redirect( $redirect );
			}
		}
		else if ( $option == 'logout' )
		{
			Users::logout();
			return true;
		}
		return true;

	}

// Login function , takes username and password ( raw password ) , returns boolean ( true/false ) , also sets $_SESSION variables
	public static function login( $usernameIN, $password )
	{
		$username = trim( $usernameIN );
		if ( empty( $username ) || empty( $password ) )
		{
			return false;
		}

		$Login = self::LocalLogin( $username, $password );
		if ( !$Login )
		{
			return false;
		}
		self::logLogin();
//		$Login = self::Ldaplogin( $username, $password );
		$return = Request::getVar( 'return' );
		$redirect = '?ref=loggedIN';
		if ( !empty( $return ) )
		{
			$uri = URI::getInstance( base64_decode( $return ) );
			$uri->setVar( 'ref', 'loggedIN' );
			$redirect = $uri->toString( array( 'path', 'query', 'fragment' ) );
		}
		self::Redirect( $redirect );
		return true;

	}

	// Login function , takes username and password ( raw password ) , returns boolean ( true/false ) , also sets $_SESSION variables
	public static function SSOlogin( $User, $SessionToken )
	{
		$AzureKey = Helper::getConfig( 'azure_sso_ident_key' );
		$UserID = C::_( $AzureKey, $User );
		if ( empty( $UserID ) )
		{
			Users::Redirect( '/?ref=sso', 'Login Failed!', 'error' );
			exit();
		}

		$Login = self::_SSOLogin( $UserID, $SessionToken );
		if ( !$Login )
		{
			Users::Redirect( '/?ref=sso', 'Login Failed!', 'error' );
		}
		self::logLogin();
		$return = Request::getVar( 'return' );
		$redirect = '/?ref=sso-success';
		if ( !empty( $return ) )
		{
			$uri = URI::getInstance( base64_decode( $return ) );
			$uri->setVar( 'ref', 'loggedIN' );
			$redirect = $uri->toString( array( 'path', 'query', 'fragment' ) );
		}
		self::Redirect( $redirect );
		return true;

	}

	// Login function , takes username and password ( raw password ) , returns boolean ( true/false ) , also sets $_SESSION variables
	public static function SAMLSSOlogin( $User )
	{
		$AzureKey = Helper::getConfig( 'azure_saml_sso_ident_key' );
		$UserID = C::_( $AzureKey, $User );
		if ( empty( $UserID ) )
		{
			Users::Redirect( '/?ref=samlsso', 'Login Failed!', 'error' );
			exit();
		}
		$Login = self::_SAMLSSOLogin( $UserID, $User );
		if ( !$Login )
		{
			Users::Redirect( '/?ref=samlsso', 'Login Failed!', 'error' );
		}
		self::logLogin();
		$return = Session::Get( 'RETURN' );
		$redirect = '/?ref=samlsso-success';
		if ( !empty( $return ) )
		{
			$uri = URI::getInstance( base64_decode( $return ) );
			$uri->setVar( 'ref', 'samlsso-success' );
			$redirect = $uri->toString( array( 'path', 'query', 'fragment' ) );
		}
		self::Redirect( $redirect, 'Login Successfully!' );
		return true;

	}

	public static function _SAMLSSOLogin( $UserID, $SAMLData )
	{
		$UserData = new Tableslf_personsInterface( 'slf_persons', 'id' );
		$User = Users::getUserByUserName( $UserID );
		if ( !$User )
		{
			$Msg = 'User Not Found!';
			self::Redirect( '/?ref=sso', $Msg, 'error' );
			return false;
		}
		$UserData->load( $User->ID, 'ID' );
		if ( empty( $UserData->ID ) )
		{
			self::Log( $UserID, 'DB SEARCH - მომხმარებელის მონაცემები ბაზაში ვერ მოიძებნა.' );
			$Msg = 'User Not Found!';
			self::Redirect( '/?ref=sso', $Msg, 'error' );
			return false;
		}
		if ( empty( $UserData->ACTIVE ) )
		{
			$Msg = 'Username Blocked!';
			self::Redirect( '/?ref=sso', $Msg, 'error' );
			die;
		}
		self::$_user = (object) $UserData->getProperties();
		self::$_user->SAMLSSO = 1;
		self::$_user->SAMLSSODATA = $SAMLData;
		self::SetSessionData( self::$_user );
		return true;

	}

	public static function _SSOLogin( $UserID, $SessionToken )
	{
		$UserData = new Tableslf_personsInterface( 'slf_persons', 'id' );
		$User = Users::getUserByUserName( $UserID );
		if ( !$User )
		{
			$Msg = 'User Not Found!';
			self::Redirect( '/?ref=sso', $Msg, 'error' );
			return false;
		}
		$UserData->load( $User->ID, 'ID' );
		if ( empty( $UserData->ID ) )
		{
			self::Log( $UserID, 'DB SEARCH - მომხმარებელის მონაცემები ბაზაში ვერ მოიძებნა.' );
			$Msg = 'User Not Found!';
			self::Redirect( '/?ref=sso', $Msg, 'error' );
			return false;
		}
		if ( empty( $UserData->ACTIVE ) )
		{
			$Msg = 'Username Blocked!';
			self::Redirect( '/?ref=sso', $Msg, 'error' );
			die;
		}
		self::$_user = (object) $UserData->getProperties();
		self::$_user->SSO = 1;
		self::SetSessionData( self::$_user, $SessionToken );
		return true;

	}

	public static function LocalLogin( $username, $password )
	{
		$UserData = new Tableslf_personsInterface( 'slf_persons', 'id' );
		$User = Users::getUserByUserName( $username );
		if ( !$User )
		{
			return false;
		}
		$UserData->load( $User->ID, 'ID' );
		$Password = md5( $password );
		if ( empty( $UserData->ID ) )
		{
			self::Log( $username, 'DB SEARCH - მომხმარებელის მონაცემები ბაზაში ვერ მოიძებნა.' );
			return false;
		}
		if ( $UserData->U_PASSWORD != $Password )
		{
			self::Log( $username, 'DB SEARCH - მომხმარებელის მონაცემები არასწორია.' );
			return false;
		}
		if ( empty( $UserData->ACTIVE ) )
		{
			$msg = 'Username Blocked!';
			self::Redirect( '?', $msg );
			die;
		}
		self::$_user = (object) $UserData->getProperties();
		self::SetSessionData( self::$_user );
		return true;

	}

// Logout function , logs out username and unsets $_SESSION variables , returns boolean , true on success , false if user is not logged in
	public static function logout()
	{
		$SSO = C::_( 'SSO', self::$_user, 0 );
		$SAMLSSO = C::_( 'SAMLSSO', self::$_user, 0 );
		if ( $SSO )
		{
			self::SSOLogout();
			self::$_user = NULL;
			Session::Destroy();
			die;
		}
		elseif ( $SAMLSSO )
		{
			self::SAMLSSOLogout();
			self::$_user = NULL;
		}
		else
		{
			self::$_user = NULL;
			Session::Destroy();
			self::Redirect( '?ref=LogOut' );
		}
		return false;

	}

	/**
	 * 
	 * @staticvar array $Users
	 * @param type $ID
	 * @return TableCws_workersInterface Object
	 */
	public static function getUser( $ID = NULL )
	{
		if ( !is_null( $ID ) )
		{
			$ID = (int) $ID;
			static $Users = array();
			if ( !isset( $Users[$ID] ) )
			{
				$Query = 'select * from slf_persons '
								. ' where ID = ' . (int) $ID
				;
				$Users[$ID] = XRedis::getDBCache( 'slf_persons', $Query, 'LoadObject' );
//				$Users[$ID] = DB::LoadObject( $Query );
			}
			return (object) $Users[$ID];
		}
		else if ( self::$_user )
		{
			return (object) self::$_user;
		}
		return false;

	}

	public static function isLogged()
	{
		if ( !empty( self::$_user ) )
		{
			return true;
		}
		return false;

	}

	public static function GetUserID()
	{
		$ID = C::_( 'ID', self::$_user );
		if ( $ID )
		{
			return $ID;
		}
		return false;

	}

	public static function GetUserRole()
	{
		if ( self::GetUserID() )
		{
			$USER_ROLE = C::_( 'USER_ROLE', self::$_user );
			if ( $USER_ROLE )
			{
				return $USER_ROLE;
			}
		}
		return false;

	}

	public static function CloseConnection( $urlIN = null, $msg = '', $type = 'msg' )
	{
		$url = trim( $urlIN );
		if ( empty( $url ) )
		{
			$url = '?ref=Redirect';
		}
		$uri = URI::getInstance( $url );
		$uri->delVar( 'msg' );
		$uri->delVar( 'error' );
		if ( !empty( $msg ) )
		{
			switch ( $type )
			{
				default:
				case 'msg':
					$uri->setVar( 'msg', $msg );
					break;
				case 'error':
					$uri->setVar( 'error', $msg );
					break;
			}
		}
		$Response = 'Data Saved!';
		$size = strlen( $Response );
		header( 'Content-Length: ' . $size );
		header( 'Content-type: text/plain' );
		header( 'Connection: Close' );
//		header( 'Transfer-Encoding: chunked' );
		header( 'Location: ' . $uri->toString( array( 'path', 'query', 'fragment' ) ) );
		echo $Response;
		ignore_user_abort();
		ob_flush();
		ob_flush();
		ob_flush();
		ob_flush();
		flush();
		flush();
		flush();
		flush();

	}

	public static function Redirect( $urlIN = null, $msg = '', $type = 'msg' )
	{
		$url = trim( $urlIN );
		if ( empty( $url ) )
		{
			$url = '?ref=Redirect';
		}
		$uri = URI::getInstance( $url );
		$uri->delVar( 'msg' );
		$uri->delVar( 'error' );
		if ( !empty( $msg ) )
		{
			switch ( $type )
			{
				default:
				case 'msg':
					$uri->setVar( 'msg', $msg );
					break;
				case 'error':
					$uri->setVar( 'error', $msg );
					break;
			}
		}
		header( 'Location: ' . $uri->toString( array( 'path', 'query', 'fragment' ) ) );
		die;

	}

	public static function CanAccess( $option = null )
	{
		$id = Users::GetUserID();
		if ( empty( $id ) )
		{
			return false;
		}
		if ( $id == -500 )
		{
			return true;
		}
		$config = MenuConfig::getInstance();
		if ( is_null( $option ) )
		{
			$option = mb_strtolower( Request::getVar( 'option', DEFAULT_COMPONENT ) );
		}
		if ( $config->CheckOption( $option, self::GetUserRole() ) )
		{
			return true;
		}

		return false;

	}

	public static function CheckOldPassword( $pass_old )
	{
		if ( empty( $pass_old ) )
		{
			return false;
		}
		$user = self::getUser();
		if ( $user->PASSWORD == md5( $pass_old ) )
		{
			return true;
		}
		return false;

	}

	public static function CheckNewPassword( $pass_new, $pass_conf )
	{
		if ( empty( $pass_new ) || empty( $pass_conf ) )
		{
			return false;
		}

		if ( strlen( $pass_new ) < 7 )
		{
			return false;
		}

		if ( $pass_new == $pass_conf )
		{
			return true;
		}
		return false;

	}

	public static function GetUserName()
	{
		if ( self::GetUserID() )
		{
			$UserName = C::_( 'USER_NAME', self::$_user );
			if ( $UserName )
			{
				return $UserName;
			}
		}
		return '';

	}

	public static function GetUserFullName( $ID = NULL )
	{
		if ( !is_null( $ID ) )
		{
			$ID = (int) $ID;
			$User = self::getUser( $ID );
			return C::_( 'FIRSTNAME', $User ) . ' ' . C::_( 'LASTNAME', $User );
		}
		else if ( self::$_user )
		{
			return C::_( 'FIRSTNAME', self::$_user ) . ' ' . C::_( 'LASTNAME', self::$_user );
		}
		return false;

	}

	/**
	 * 
	 * @param Tableslf_personsInterface $UserData
	 * @return \Tableslf_personsInterface
	 * @deprecated since version 1.0
	 */
	public static function getDBUserData( Tableslf_personsInterface $UserData )
	{
		/* @var $UserData UserData */
		$sql = 'select u.* '
						. ' from slf_persons u '
						. ' where lower(u.ldap_username) = lower( \'' . $UserData->LDAP_USERNAME . '\')'
						. ' and u.active = 1 '
		;
		$result = DB::LoadObject( $sql );
		if ( empty( $result ) )
		{
			$UserData->USER_ROLE = Helper::getConfig( 'new_ldap_user_role' );
			$UserData->ACTIVE = 1;
			$UserData->store();
			return $UserData->ID;
		}
		$UserData->bind( $result );
		return $UserData;

	}

	protected static function SetSessionData( $result, $SessionToken = null )
	{
		self::$_user = $result;
		Session::Set( '_session', time() );
		Session::Set( '_user', $result );
		Session::Set( 'logged', true );
		$username = C::_( 'LDAP_USERNAME', $result );
		$UseExt = Helper::getConfig( 'extended_session_use' );
		if ( $UseExt )
		{
			$remember = Request::getInt( 'remember', 0 );
		}
		else
		{
			$remember = 0;
		}
		Session::SaveDBSession( $result, $remember, $username, true, $SessionToken );

	}

	public static function GetUserData( $Key )
	{
		return C::_( $Key, self::$_user, false );

	}

	public static function LoginAsUser( $LoginAsUserID )
	{
		Session::Set( '_user_bkp', self::getUser() );
		$NewUser = self::getUser( (int) $LoginAsUserID );
		if ( C::_( 'ID', $NewUser ) )
		{
			Session::Set( '_user', $NewUser );
			$username = C::_( 'LDAP_USERNAME', $NewUser );
			$remember = 0;
			Session::SaveDBSession( $NewUser, $remember, $username );
			XError::setMessage( 'Login Success!' );
			self::Redirect();
		}
		else
		{
			XError::setError( 'Login Failed!' );
			self::Redirect();
		}

	}

	public static function RestoreLoginAs()
	{
		$AdminUser = Session::get( '_user_bkp' );
		if ( C::_( 'ID', $AdminUser ) )
		{
			Session::Set( '_user', $AdminUser );
			$username = C::_( 'LDAP_USERNAME', $AdminUser );
			$remember = 0;
			Session::SaveDBSession( $AdminUser, $remember, $username );
			Session::set( '_user_bkp', false );
			XError::setMessage( 'Restore Success!' );
			$link = '?option=loginas';
			self::Redirect( $link );
		}
		else
		{
			XError::setMessage( 'Restore Failed!' );
			self::Redirect();
		}

	}

	public static function Log( $text, $Status = '', $Error = false )
	{
		$date = new PDate();
		if ( $Error )
		{
			$Subject = 'STAFF Login Error';
			$headers = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From:<hr@self.ge>' . "\r\n";
			$message = $text . "<br /><br />" . $Status;
			$email = 't.kevlishvili@self.ge';
			Cmail( $email, $Subject, $message, $headers );
		}
		file_put_contents( PATH_LOGS . DS . 'Login.log', $date->toFormat() . "\t" . $text . "\t\t" . $Status . PHP_EOL, FILE_APPEND );

	}

	public static function getUserByUserName( $username )
	{
		static $Users = array();
		if ( !isset( $Users[$username] ) )
		{
			$Query = ' Select * from slf_persons w '
							. ' where '
							. ' w.ldap_username = ' . DB::Quote( $username )
							. ' and w.active =1 '
			;
			$Users[$username] = DB::LoadObject( $Query );
		}
		return $Users[$username];

	}

	public static function getUserByField( $Field, $Value, $Fields = '*', $ListMethod = 'LoadObject', $Status = 1 )
	{
		if ( !is_array( $Value ) )
		{
			$Value = array( $Value );
		}
		$Query = ' Select ' . $Fields . ' from slf_persons '
						. ' where '
						. ' lower(' . $Field . ') in (\'' . implode( "','", $Value ) . '\' )'
						. ($Status ? ' and active= ' . $Status : '')
		;
		$Data = DB::{$ListMethod}( $Query );
		return $Data;

	}

	public static function logLogin()
	{
		$LOG_USER_ID = C::_( 'ID', self::$_user );
		if ( $LOG_USER_ID > 0 )
		{
			$PrevUserName = trim( Request::getVar( '_uName_', '', 'cookie' ) );
			$UserName = C::_( 'LDAP_USERNAME', self::$_user );
			$prev_user = Users::getUserByField( 'LDAP_USERNAME', $PrevUserName );
			if ( C::_( 'ID', $prev_user, -1 ) < 0 )
			{
				$PrevUserName = $UserName;
			}
			$scan = new XBrowserDetection();
			$table = new TableHrs_login_logInterface( 'HRS_LOGIN_LOG', 'ID', 'SQS_LOGS.nextval' );
			$table->bind( array_change_key_case( $scan->getAll(), CASE_UPPER ) );
			$table->LOG_PREV_USER_NAME = $PrevUserName;
			$table->LOG_USER_ID = $LOG_USER_ID;
			$table->LOG_USER_NAME = $UserName;
			setcookie( '_uName_', $table->LOG_USER_NAME, (time() + EXTENDED_SESSION_PERIOD * 60 ), COOKIE_PATH );
			return $table->store();
		}

	}

	public static function AzureSSO()
	{
		$AzureAppID = Helper::getConfig( 'azure_app_id' );
		$AzureTennant = Helper::getConfig( 'azure_tennant_id' );
		$login_url = "https://login.microsoftonline.com/" . $AzureTennant . "/oauth2/v2.0/authorize";
		$uri = URI::getInstance();
		$params = array(
				'client_id' => $AzureAppID,
				'redirect_uri' => $uri->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) ) . '/api/Azure/SSO',
				'response_type' => 'token',
				'response_mode' => 'form_post',
				'scope' => 'https://graph.microsoft.com/User.Read',
				'state' => session_id()
		);
		header( 'Location: ' . $login_url . '?' . http_build_query( $params ) );
		exit();

	}

	public static function AzureSAMLSSO( $Return )
	{
		$Auth = new Auth( );
		# If AuthNRequest ID need to be saved in order to later validate it, do instead
		# $ssoBuiltUrl = $auth->login(null, array(), false, false, true);
		$AuthNRequestID = $Auth->getLastRequestID();
		Session::Set( 'RETURN', $Return );
		Session::Set( 'AuthNRequestID', $AuthNRequestID );
		$Auth->login();
		exit();

	}

	public static function SSOLogout()
	{
		$uri = URI::getInstance();
		$AzureTennant = Helper::getConfig( 'azure_tennant_id' );
		$LogOutURL = 'https://login.microsoftonline.com/' . $AzureTennant
						. '/oauth2/v2.0/logout?'
						. 'post_logout_redirect_uri=' . urlencode( $uri->toString( array( 'scheme', 'user', 'pass', 'host', 'port' ) ) . '/api/Azure/Logout' );
		header( 'Location: ' . $LogOutURL );

	}

	public static function SAMLSSOLogout()
	{
		$SAMLData = C::_( 'SAMLSSODATA', self::$_user );
		$Auth = new Auth( );
		$returnTo = $Auth->getSettings()->getValue( 'sp.singleLogoutService.url' );
		$parameters = array();
		$nameId = C::_( 'samlNameId', $SAMLData );
		$sessionIndex = C::_( 'samlSessionIndex', $SAMLData );
		$nameIdFormat = C::_( 'samlNameIdFormat', $SAMLData );
		$samlNameIdNameQualifier = C::_( 'samlNameIdNameQualifier', $SAMLData );
		$samlNameIdSPNameQualifier = C::_( 'samlNameIdSPNameQualifier', $SAMLData );
		Session::Destroy();
		$Auth->logout( $returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat, $samlNameIdNameQualifier, $samlNameIdSPNameQualifier );
		exit();

	}

	public static function getUserByPermitID( $CardID )
	{
		$Query = 'select '
						. ' pp.person '
						. ' from rel_person_permit pp '
						. ' left join slf_persons p on p.id = pp.person '
						. ' where '
						. ' pp.permit_id = ' . DB::Quote( $CardID )
						. '  and p.active = 1 '
		;
		$UserID = DB::LoadResult( $Query );
		return self::getUser( $UserID );

	}

}
