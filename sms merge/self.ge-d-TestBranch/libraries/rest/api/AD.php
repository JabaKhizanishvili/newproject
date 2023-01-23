<?php

class XAD extends XAPI
{
	protected $requestContentType = 'application/json';
	protected $StatusCode = 400;
	protected $Key = '4b8cf832-a82c-4e3e-896a-83db4d10e037';
	protected $_GrantType = 'client_credentials';
	protected $_API_Path = null;

	public function __construct()
	{
		$URI = URI::getInstance();
		$Domain = mb_strtolower( trim( C::_( '2', array_reverse( explode( '.', $URI->getHost() ) ) ) ) );
		$this->_API_Path = X_PATH_BUFFER . DS . 'API' . DS . $Domain;
		if ( !Folder::exists( $this->_API_Path ) )
		{
			Folder::create( $this->_API_Path, 0777 );
		}
		XApiHelper::SetContentType( 'application/json' );

	}

	public function Authentication()
	{
		XApiHelper::SetResponse( 400 );
		$Vars = $this->getVars( 'post' );
		$ClientID = C::_( 'client_id', $Vars );
		$Secret = C::_( 'client_secret', $Vars );
		$GrantType = C::_( 'grant_type', $Vars );
		$this->StatusCode = 403;
		if ( $GrantType != $this->_GrantType )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}
		if ( empty( $ClientID ) || empty( $Secret ) )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}

		$ApiData = $this->_GetAPIKey( $ClientID );
		$ApiSecret = C::_( 'APIKEY', $ApiData );
		$ApiTime = C::_( 'APITIME', $ApiData );
		if ( $ApiSecret != $Secret )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}
		$Token = hash( 'sha256', openssl_random_pseudo_bytes( 128 ) );
		$this->_WriteApiKey( $Token, $ApiTime );
		$Response = new stdClass();
		$Response->access_tocken = $Token;
		$Response->expires_in = $ApiTime;
		$Response->token_type = 'bearer';
		$Response->scope = 'ALL';
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Response );

	}

	/**
	 * @api {get} /OrgUnitTypes Get Organization Units Types Information
	 * @apiName OrgUnitTypes
	 * @apiGroup Workers
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function OrgUnitTypes()
	{
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::OrgUnitTypes();
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /Orgs Get Organizations Information
	 * @apiName Orgs
	 * @apiGroup Orgs
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function Orgs()
	{
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::Orgs();
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /Positions Get Positions Information
	 * @apiName Positions
	 * @apiGroup Positions
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function Positions()
	{
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::Positions();
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /StaffSchedules Get StaffSchedules Information
	 * @apiName StaffSchedules
	 * @apiGroup StaffSchedules
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function StaffSchedules()
	{
		$OrgID = $this->getVars( '0' );
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::StaffSchedules( $OrgID );
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /Workers Get Workers Information
	 * @apiName Workers
	 * @apiGroup Workers
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function Workers()
	{
		$OrgID = $this->getVars( '0' );
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::Workers( $OrgID );
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /OrgUnits Get Organization Units Information
	 * @apiName OrgUnits
	 * @apiGroup Workers
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function OrgUnits()
	{
		$OrgID = $this->getVars( '0' );
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::OrgUnits( $OrgID );
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /Persons Get Persons Information
	 * @apiName Persons
	 * @apiGroup Persons
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function Persons()
	{
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::Persons();
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /GetWorkers Get Workers Information
	 * @apiName GetWorkers
	 * @apiGroup Workers
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function GetWorkers()
	{
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::GetWorkers();
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	/**
	 * @api {get} /GetWorkers Get Workers Information
	 * @apiName GetWorkers
	 * @apiGroup Workers
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function GetGraph()
	{
		$Date = trim( C::_( 'post.Date', $this->getVars(), false ) );
		$UserID = trim( C::_( 'post.UserID', $this->getVars(), false ) );
		$this->getVar( 'Date' );
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Data = XApiMethods::GetWorkersGraps( $Date, $UserID );
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Data );

	}

	public function _GetAPIKey( $ClientID )
	{
		$Q = ' select '
						. ' apikey, '
						. ' a.apitime '
						. ' from lib_apis a '
						. ' where '
						. ' a.apiid = ' . DB::Quote( $ClientID )
						. ' and a.active = 1 '
		;
		return DB::LoadObject( $Q );

	}

	public function _WriteApiKey( $Token, $ApiTime )
	{
		$File = $this->_API_Path . DS . $Token;
		return file_put_contents( $File, time() + $ApiTime );

	}

	public function CheckAuthToken()
	{
		$Headers = apache_request_headers();
		$Authorization = C::_( 'Authorization', $Headers );
		$this->StatusCode = 403;
		if ( empty( $Authorization ) )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			exit();
		}
		$Auth = explode( ' ', $Authorization );
		$Method = C::_( '0', $Auth );
		$Token = C::_( '1', $Auth );
		if ( empty( $Token ) || empty( $Method ) )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			exit();
		}
		if ( $Method != 'Bearer' )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			exit();
		}
		$Status = $this->_ReadToken( $Token );
		if ( $Status )
		{
			return true;
		}
		else
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			exit();
		}

	}

	public function _ReadToken( $Token )
	{
		$File = $this->_API_Path . DS . $Token;
		if ( !File::exists( $File ) )
		{
			return false;
		}
		$Time = floatval( file_get_contents( $File ) );
		if ( $Time < time() )
		{
			return false;
		}
		return true;

	}

	public function OAuth()
	{
		echo '<pre><pre>';
		print_r( $_POST );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
		echo '<pre><pre>';
		print_r( $_SERVER );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";

		echo '<pre><pre>';
		print_r( $_REQUEST );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
		die;

	}

}
