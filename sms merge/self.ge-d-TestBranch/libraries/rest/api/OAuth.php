<?php

//error_reporting( 0 );
//ini_set( 'display_errors', 0 );

class XOAuth extends XAPI
{
	protected $requestContentType = 'application/json';
	protected $StatusCode = 400;
	protected $Key = '4b8cf832-a82c-4e3e-896a-83db4d10e037';
	protected $_GrantType = 'client_credentials';
	protected $_API_Path = null;
	protected $_ClientID = null;

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
		$ApiTime = (int) C::_( 'APITIME', $ApiData );
		if ( $ApiSecret != $Secret )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}
		$Token = hash( 'sha256', openssl_random_pseudo_bytes( 128 ) );
		$this->_WriteApiKey( $Token, $ApiTime, $ClientID );
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
	 * @api {get} /Table Get Table Information
	 * @apiName Table
	 * @apiGroup Table
	 *
	 * @apiSuccess {json} Results Added Table information.
	 * @apiError {json} Results Failed information.
	 */
	public function Table()
	{
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Table = strtolower( $this->getVar( 0 ) );
		$APIData = $this->_GetAPISchema();
		$TableCols = C::_( 'SCHEMA.' . $Table, $APIData, [] );
		$Response = new stdClass();
		$Response->start = 0;
		$Response->limit = 0;
		$Response->count = 0;
		$Response->items = [];
		if ( !count( $TableCols ) )
		{
			$Response->message = 'Invalid Query!';
			$this->_Print( $Response );
			return false;
		}
		$Folder = PATH_BASE . DS . 'libraries' . DS . 'rest' . DS . 'tables_xml';
		$XMLFile = $Folder . DS . mb_strtolower( $Table ) . '.xml';

		if ( !File::exists( $XMLFile ) )
		{
			$Response->message = 'Invalid Query!';
			$this->_Print( $Response );
			return false;
		}
		$XMLData = $this->loadXMLFile( $XMLFile );
		$XMLColumns = $XMLData->getElementByPath( 'columns' )->children();
		$TableColumns = $this->_RenderTableColumns( $TableCols, $XMLColumns );
		$Aliases = C::_( '_ALIASES', $TableColumns );

		$Where = $this->_RenderWhere( $Aliases, $TableColumns );
		if ( $Where === false )
		{
			$Response->message = 'Invalid Query!';
			$this->_Print( $Response );
			return false;
		}
		$DefWhere = null;
		$W = $XMLData->getElementByPath( 'where' );
		if ( $W )
		{
			$DefWhere = trim( $W->data() );
		}
		if ( !empty( $DefWhere ) )
		{
			$Where[] = $DefWhere;
		}
		$DBTable = $XMLData->attributes( 'name' );
		$DBOrderData = $XMLData->getElementByPath( 'order' );
		$DBOrder = trim( $DBOrderData->attributes( 'by' ) . ' ' . $DBOrderData->attributes( 'type' ) );
		if ( !empty( $DBOrder ) )
		{
			$DBOrder = 'order by ' . $DBOrder;
		}

//order by="ordering" type="asc"
		$BCols = $this->_RenderQueryCols( $TableColumns );
		$Cols = $this->_RenderQueryCols( $TableColumns, true );
		$Requests = $this->_RenderRequests( $XMLData->getElementByPath( 'request' )->children() );
//		$StartRequired = C::_( 'start.required', $Requests, 0 );
		$Start = $this->getVar( 'post.start', C::_( 'start.value.default', $Requests, 0 ) );
//		$LimitRequired = C::_( 'limit.required', $Requests, 0 );
		$Limit = $this->getVar( 'post.limit', C::_( 'limit.value.default', $Requests, 0 ) );
		$MaxLimit = C::_( 'limit.value.max', $Requests, 0 );
		if ( $Limit > $MaxLimit )
		{
			$Limit = $MaxLimit;
		}
		if ( $Limit < 1 )
		{
			$Limit = 1;
		}
		if ( $Start < 0 )
		{
			$Start = 0;
		}
		$Response->start = (int) $Start;
		$Response->limit = (int) $Limit;

		$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';
		$countQuery = 'select '
						. ' count(*) '
						. ' from ' . $DBTable . ' t '
						. $whereQ
		;

		$Response->count = (int) DB::LoadResult( $countQuery );
		$Query = 'select  '
						. implode( ', ', $BCols )
						. ' from ' . $DBTable . ' t '
						. $whereQ
						. $DBOrder
		;
		$Limit_query = 'select '
						. implode( ', ', $Cols )
						. ' from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Start
						. ' and rn <= ' . ($Start + $Limit)
		;
		$Response->items = DB::LoadObjectList( $Limit_query );
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo $this->_Print( $Response, $this->StatusCode );

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

	public function _GetAPISchema( $ClientID = null )
	{
		if ( is_null( $ClientID ) )
		{
			$ClientID = $this->_ClientID;
		}
		if ( empty( $ClientID ) )
		{
			return false;
		}
		$Q = ' select '
						. ' a.* '
						. ' from lib_apis a '
						. ' where '
						. ' a.apiid = ' . DB::Quote( $ClientID )
						. ' and a.active = 1 '
		;
		$Data = DB::LoadObject( $Q );
		$Schema = '';
		for ( $A = 0; $A < 11; $A++ )
		{
			$Schema .= C::_( 'API_TABLES' . $A, $Data );
		}
		$Data->SCHEMA = json_decode( $Schema );
		return $Data;

	}

	public function _WriteApiKey( $Token, $ApiTime, $ClientID )
	{
		$Data = new stdClass();
		$Data->Time = time() + $ApiTime;
		$Data->ClientID = $ClientID;
		$File = $this->_API_Path . DS . $Token;
		return file_put_contents( $File, json_encode( $Data ) );

	}

	public function CheckAuthToken()
	{
		XApiHelper::SetResponse( 400 );
		$Headers = apache_request_headers();
		$Authorization = C::_( 'Authorization', $Headers );
		$this->StatusCode = 403;
		$Response = new stdClass();
		$Response->start = 0;
		$Response->limit = 0;
		$Response->count = 0;
		$Response->items = [];
		if ( empty( $Authorization ) )
		{
			$Response->message = 'Empty Authorization Query!';
			$this->_Print( $Response );
			exit();
		}

		$Auth = explode( ' ', $Authorization );
		$Method = C::_( '0', $Auth );
		$Token = C::_( '1', $Auth );
		if ( empty( $Token ) || empty( $Method ) )
		{
			$Response->message = 'Bad Authorization Query!';
			$this->_Print( $Response );
			exit();
		}
		if ( $Method != 'Bearer' )
		{
			$Response->message = 'Bad Bearer Authorization Query!';
			$this->_Print( $Response );
			exit();
		}
		$Status = $this->_ReadToken( $Token );
		if ( $Status )
		{
			return true;
		}
		else
		{
			$Response->message = 'Expired Token!';
			$this->_Print( $Response );
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
		$Data = json_decode( file_get_contents( $File ) );
		$Time = floatval( C::_( 'Time', $Data ) );
		if ( $Time < time() )
		{
			return false;
		}
		$this->_ClientID = C::_( 'ClientID', $Data );
		return true;

	}

	public function Schema()
	{
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		session_start();
		Users::InitUser();
		if ( !Users::isLogged() )
		{
			return false;
		}
		$ClientID = $this->getVar( '0' );
		if ( empty( $ClientID ) )
		{
			return false;
		}
		$ApiSchema = $this->_GetAPISchema( $ClientID );
		$JsonData = '';
		if ( !$ApiSchema )
		{
			return false;
		}
		for ( $A = 0; $A < 10; $A++ )
		{
			$JsonData .= trim( C::_( 'API_TABLES' . $A, $ApiSchema ) );
		}
		$Data = json_decode( $JsonData );
		$Folder = PATH_BASE . DS . 'libraries' . DS . 'rest' . DS . 'tables_xml';
		$Schemas = array();
		foreach ( $Data as $Key => $Columns )
		{
			$XMLFile = $Folder . DS . mb_strtolower( $Key ) . '.xml';
			if ( !File::exists( $XMLFile ) )
			{
				continue;
			}
			$XMLData = $this->loadXMLFile( $XMLFile );
			$XMLColumns = $XMLData->getElementByPath( 'columns' )->children();
			$TableColumns = (object) $this->_GetTableColumns( $Columns, $XMLColumns );

			$Schema = new stdClass();
			$Schema->TABLE = ucfirst( $Key );
			$Schema->ENDPOINT = 'POST /api/OAuth/Table/' . ucfirst( $Key );
			$Schema->REQUEST = $this->_RenderRequests( $XMLData->getElementByPath( 'request' )->children() );
			$Schema->RESPONSE = (array) $this->_RenderResponse( $XMLData->getElementByPath( 'response' )->children() );
			$Schema->RESPONSE['items'] = (array) $TableColumns;
			$Schemas[] = $Schema;
		}

		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Schemas );

	}

	/**
	 * 
	 * @param type $path
	 * @return SimpleXMLElements
	 */
	public function loadXMLFile( $path )
	{
		if ( $path )
		{
			require_once PATH_BASE . DS . 'libraries' . DS . 'html' . DS . 'simplexml.php';
			$xml = new SimpleXML();
			if ( $xml->loadFile( $path ) )
			{
				return $xml->document;
			}
		}
		return false;

	}

	public function _RenderTableColumns( $ColumnsIN, $XMLColumns )
	{
		$Columns = array_flip( $ColumnsIN );
		$TableColumns = array();
		/** @var SimpleXMLElements $Column */
		$Aliases = [];
		foreach ( $XMLColumns as $Column )
		{
			$Name = $Column->attributes( 'name' );
			$A = [];
			if ( isset( $Columns[$Name] ) )
			{
				$Alias = $Column->attributes( 'alias', null );
				if ( $Alias )
				{
					$A = [ 'ALIAS' => $Alias ];
					$Aliases[$Alias] = $Name;
				}

				$TableColumns[$Name] = array_merge( array( 'TYPE' => mb_strtolower( $Column->attributes( 'type', 'string' ) ) ), $A );
			}
		}
		$TableColumns['_ALIASES'] = $Aliases;
		return $TableColumns;

	}

	public function _GetTableColumns( $ColumnsIN, $XMLColumns )
	{
		$Columns = array_flip( $ColumnsIN );
		$TableColumns = array();
		/** @var SimpleXMLElements $Column */
		foreach ( $XMLColumns as $Column )
		{
			$Name = $Column->attributes( 'name' );
			if ( isset( $Columns[$Name] ) )
			{
				$Name = strtoupper( $Column->attributes( 'alias', $Name ) );
				$TableColumns[$Name] = array( 'TYPE' => mb_strtolower( $Column->attributes( 'type', 'string' ) ) );
			}
		}
		return array( $TableColumns );

	}

	public function _RenderRequests( $Requests )
	{
		$RequestsData = array();
		/** @var SimpleXMLElements $Request */
		foreach ( $Requests as $Request )
		{
			$RequestData = array();
			$Name = $Request->attributes( 'name' );
			$Type = $Request->attributes( 'type' );
			$Required = boolval( $Request->attributes( 'required', 0 ) );
			$RequestData[$Name] = array();
			if ( $Type )
			{
				$RequestData[$Name]['type'] = $Type;
			}
			if ( $Required )
			{
				$RequestData[$Name]['required'] = true;
			}
			else
			{
				$RequestData[$Name]['required'] = false;
			}
			/** @var SimpleXMLElements $V */
			foreach ( $Request->children() as $V )
			{
				$VName = $V->name();
				$RequestData[$Name][$VName] = array();
				foreach ( $V->children() as $KV )
				{
					$RequestData[$Name][$VName][$KV->attributes( 'name' )] = $KV->attributes( 'value' );
				}
			}
			$RequestsData = array_merge( $RequestsData, $RequestData );
		}
		$RequestsData['where'] = array();
		$RequestsData['where']['required'] = false;
		$RequestsData['where']['type'] = 'Array Of JSON Objects';
		$RequestsData['where']['description'] = 'Query Where Expression(s)';
		$RequestsData['where']['value'] = [];
		$RequestsData['where']['value']['example'] = '{"key":"type","operand":"=","value":"5"}';
		$RequestsData['where']['value']['eperands'] = '=, >, <, >=, <=, like %%';
		return $RequestsData;

	}

	public function _RenderResponse( $Responses )
	{
		$ResponseData = array();
		/** @var SimpleXMLElements $Response */
		foreach ( $Responses as $Response )
		{
			$RequestData = array();
			$Name = $Response->attributes( 'name' );
			$Type = $Response->attributes( 'type' );
			$RequestData[$Name] = array();
			if ( $Type )
			{
				$RequestData[$Name]['type'] = $Type;
			}
			$ResponseData = array_merge( $ResponseData, $RequestData );
		}
		return $ResponseData;

	}

	public function _RenderWhere( $Aliases, $TableColumns )
	{
		$Wheres = $this->getVar( 'post.where' );
		$W = [];
		if ( empty( $Wheres ) )
		{
			return $W;
		}
		foreach ( $Wheres as $Key => $Where )
		{
			$Where = json_decode( trim( $Where ) );
			$K = strtolower( C::_( 'key', $Where ) );
			if ( !isset( $TableColumns[$K] ) )
			{
				return false;
			}
			$Op = C::_( 'operand', $Where );
			$V = C::_( 'value', $Where );
			$W[$Key] = C::_( $K, $Aliases, $K ) . ' ' . $Op . ' ' . DB::Quote( $V );
		}
		return $W;

	}

	public function _RenderQueryCols( $TableColumns, $A = false )
	{
		$C = [];
		foreach ( $TableColumns as $Key => $Value )
		{
			if ( '_' == substr( $Key, 0, 1 ) )
			{
				continue;
			}
			$Alias = C::_( 'ALIAS', $Value );
			if ( $Alias && $A )
			{
				$C[] = trim( $Key . ' "' . strtoupper( $Alias ) . '"' );
			}
			else
			{
				$C[] = trim( $Key );
			}
		}
		return $C;

	}

}
