<?php

class XAzure extends XAPI
{
	public function SSO()
	{
		session_start();
		$Vars = $this->getVars( 'post' );
		$AccessToken = C::_( 'access_token', $Vars );
		$SessionToken = C::_( 'session_state', $Vars );
		if ( empty( $AccessToken ) )
		{
			Users::Redirect( '/?ref=sso', 'Login Failed!', 'error' );
		}
		$User = $this->_GetAzureUser( $AccessToken );
		return Users::SSOlogin( $User, $SessionToken );

	}

	/**
	 * @api {get} /OrgUnitTypes Get Organization Units Types Information
	 * @apiName OrgUnitTypes
	 * @apiGroup Workers
	 *
	 * @apiSuccess {json} Results Added employee information.
	 * @apiError {json} Results Failed information.
	 */
	public function Logout()
	{
		$Vars = $this->getVars( 'get' );
		$SID = C::_( 'sid', $Vars );
		XApiHelper::SetResponse( 400, 'application/json' );
		$Q = 'delete  from SYSTEM_SESSIONS t where t.session_token =  ' . DB::Quote( $SID );
		DB::Delete( $Q );
		Users::Redirect( '/?ref=sso', 'Logout Successfully!', 'msg' );
		return;

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

	protected function _GetAzureUser( $AccessToken )
	{
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $AccessToken, 'Conent-type: application/json' ) );
		curl_setopt( $ch, CURLOPT_URL, 'https://graph.microsoft.com/v1.0/me/' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$rez = json_decode( curl_exec( $ch ), 1 );
		return $rez;

	}

}
