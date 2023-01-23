<?php

class XDevice extends XAPI
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
		$ApiTime = C::_( 'APITIME', $ApiData );
		if ( $ApiSecret != $Secret )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}
		$Token = hash( 'sha256', openssl_random_pseudo_bytes( 128 ) );
		$this->_WriteApiKey( $Token, $ApiTime, $ClientID );
		$Response = new stdClass();
		$Response->access_token = $Token;
		$Response->expires_in = $ApiTime;
		$Response->token_type = 'bearer';
		$Response->scope = 'ALL';
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		echo json_encode( $Response );

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
		return DB::LoadObject( $Q );

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
		$Data = json_decode( file_get_contents( $File ) );
		$Time = floatval( C::_( 'Time', $Data ) );
		if ( $Time < time() )
		{
			return false;
		}
		$this->_ClientID = C::_( 'ClientID', $Data );
		return true;

	}

	public function Cards()
	{
		$this->CheckAuthToken();
		XApiHelper::SetResponse( 400 );
		$Cards = $this->_LoadAllCards();
		XApiHelper::SetResponse( 200, 'application/json' );
		$this->_Print( $Cards );
		return;

	}

	public function GetPins()
	{
		XApiHelper::SetResponse( 400 );
		$Response = new stdClass();
		$Response->message = 'Invalid controller code!';
		$Device = $this->getVars( 'post.CONTROLLER_CODE' );
		if ( !$this->_CheckDevice( $Device ) )
		{
			$this->_Print( $Response );
			return false;
		}

		$Cards = $this->_LoadPins();
		XApiHelper::SetResponse( 200, 'application/json' );
		$this->_Print( $Cards );
		return;

	}

	private function _LoadPins()
	{
		$Query = 'select '
						. ' pp.permit_id "pass_code",'
						. ' w.firstname "first_name", '
						. ' w.lastname "last_name"'
						. ' from slf_persons w '
						. ' left join rel_person_permit pp on w.id = pp.person '
						. ' where '
						. ' w.active = 1 '
						. ' and nvl(w.permit_id, null) is not null '
						. ' and length(pp.permit_id) = 5'
						. ' order by pp.permit_id asc '
		;
		return DB::LoadObjectList( $Query );

	}

	/**
	 * 
	 * @param type $CardID
	 * @return type
	 */
	private function _LoadUser( $CardID )
	{
		$Query = 'select '
						. ' w.*'
						. ' from rel_person_permit pp '
						. ' left join slf_persons w on w.id = pp.person '
						. ' where '
						. ' w.active = 1 '
						. ' and pp.permit_id = ' . DB::Quote( $CardID )
		;
		return DB::LoadObject( $Query );

	}

	public function RegisterPinTransaction()
	{
		XApiHelper::SetResponse( 400 );
		$this->StatusCode = 500;
		$Data = $this->getVars( 'post' );
		$Hash = trim( C::_( 'HASH', $Data ) );
		$Response = new stdClass();
		$Response->message = 'Invalid data given!';
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		if ( $Data )
		{
			$Device = C::_( 'CONTROLLER_CODE', $Data );
			$PIN = trim( C::_( 'PIN', $Data ) );
			$Date = C::_( 'DATETIME', $Data );
			$Picture = C::_( 'PICTURE', $Data );
			$FileName = C::_( 'PHOTO_NAME', $Data );
			$Name = File::stripExt( $FileName );
			if ( $PIN == '' )
			{
				$PP = C::_( '1', explode( ' - ', $FileName ) );
				if ( empty( $PP ) )
				{
					$this->StatusCode = 200;
					$Response->message = 'OK';
					XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
					$this->_Print( $Response );
					exit;
				}
			}
			if ( empty( $Date ) )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				$this->_Print( $Response );
				exit;
			}
			if ( empty( $Picture ) )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				$this->_Print( $Response );
				exit;
			}
			if ( empty( $Hash ) )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				$this->_Print( $Response );
				exit;
			}
			if ( !$this->_CheckDevice( $Device ) )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				$this->_Print( $Response );
				exit;
			}
			$CalculatedHash = md5( $Device . '|' . $PIN . '|' . $Date . '|' . $Picture );
			if ( $CalculatedHash != $Hash )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				$this->_Print( $Response );
				exit;
			}
			$FIlePath = X_PATH_TMP . DS . 'APPImages';
			if ( !Folder::exists( $FIlePath ) )
			{
				Folder::create( $FIlePath, 0777 );
			}
			Helper::Base64ToImage( 'data:image/jpg;base64,' . $Picture, $Name, $FIlePath, '' );
			$this->insertPinRecord( $PIN, $Device, $Date, $Name );

			$this->StatusCode = 200;
			$Response->message = 'OK';
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->_Print( $Response );
		}

	}

	private function _LoadAllCards()
	{
		$Query = 'select * from ( '
						. 'select '
						. ' pp.permit_id CARD,'
						. ' rwi.device_id id, '
						. ' w.id real_id,'
						. ' w.firstname || \' \' || w.lastname as name '
						. ' from slf_persons w '
						. ' left join rel_person_permit pp on w.id = pp.person '
						. ' left join rel_worker_device_id rwi on rwi.worker = w.id and rwi.permit_id = pp.permit_id '
						. ' where '
						. ' w.active = 1 '
						. ' and nvl(w.permit_id, null) is not null '
						. ' UNION ALL '
						. ' select '
						. ' v.code CARD, '
						. ' rwi.device_id id, '
						. ' v.id real_id,'
						. ' v.lib_title as name'
						. ' from lib_visitors v '
						. ' left join rel_worker_device_id rwi on rwi.worker = v.id '
						. ' where '
						. ' v.active = 1 '
						. ' ) m '
						. ' order by m.id desc '
		;
		$Items = DB::LoadObjectList( $Query, 'PERMIT_ID' );
		$Result = [];
		$K = $this->GetMaxID();
		foreach ( $Items as $Item )
		{
			$ID = C::_( 'ID', $Item );
			$RID = C::_( 'REAL_ID', $Item );
			$CARD = C::_( 'CARD', $Item );
			if ( empty( $ID ) )
			{
				$ID = $K;
				if ( !$this->_SaveRel( $RID, $K, $CARD ) )
				{
					return [];
				}
				$Item->ID = $K;
				$K++;
			}
			$Item->NAME = ucwords( $this->TranslitToLat( C::_( 'NAME', $Item ) ) );
			unset( $Item->REAL_ID );
			$Result[] = $Item;
		}
		return $Result;

	}

	public function GetMaxID()
	{
		$Query = 'select max(nvl(r.device_id, 0)) from rel_worker_device_id r ';
		return ((int) DB::LoadResult( $Query )) + 1;

	}

	protected function _Clean( $ID )
	{
		return preg_replace( '/[^0-9]/', '', $ID );

	}

	public function _SaveRel( $RID, $K, $CARD )
	{
		$Query = 'insert '
						. ' into rel_worker_device_id '
						. ' ( '
						. 'worker, '
						. ' device_id, '
						. ' permit_id '
						. ' ) '
						. ' values '
						. ' ( '
						. $RID . ' , '
						. $K . ' , '
						. DB::Quote( $CARD )
						. ' ) ';
		return DB::Insert( $Query );

	}

	public function TranslitToLat( $text )
	{
		$str_from = 'ა, ბ, გ, დ, ე, ვ, ზ, თ, ი, კ, ლ, მ, ნ, ო, პ, ჟ, რ, ს, ტ, უ, ფ, ქ, ღ, ყ, შ, ჩ, ც, ძ, წ, ჭ, ხ, ჯ, ჰ';
		$str_to = 'a, b, g, d, e, v, z, t, i, k, l, m, n, o, p, zh, r, s, t, u, f, q, gh, k, sh, ch, c, dz, ts, tc, kh, j, h';

		if ( !empty( $text ) )
		{
			$from = explode( ', ', $str_from );
			$to = explode( ', ', $str_to );
			$trans = str_replace( $from, $to, trim( $text ) );
			return $trans;
		}
		return $text;

	}

	public function RegisterTransaction()
	{
		XApiHelper::SetResponse( 400 );
		$this->requestContentType = 'text/html';
		$this->StatusCode = 500;
		$RowData = trim( C::_( 'post.data', $this->getVars() ) );
		$Hash = trim( C::_( 'post.hash', $this->getVars() ) );
		$ClientID = trim( C::_( 'post.client_id', $this->getVars() ) );
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
		if ( $RowData )
		{
			if ( empty( $Hash ) )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				exit;
			}

			if ( empty( $RowData ) )
			{
				$this->StatusCode = 200;
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				exit;
			}
			$ApiData = $this->_GetAPIKey( $ClientID );
			$ApiKey = C::_( 'APIKEY', $ApiData );
			$CalculatedHash = md5( $RowData . '|' . $ApiKey );
			if ( $CalculatedHash != $Hash )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				exit;
			}

			$Rows = explode( '$$$', $RowData );
			foreach ( $Rows as $Row )
			{
				$Row = ltrim( trim( $Row ), '|' );
				if ( empty( $Row ) )
				{
					continue;
				}
				$Content = explode( '|', $Row );
				$this->insertRecord( $Content );
			}
			$this->StatusCode = 200;
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->_Print( 'Done!' );
		}

	}

	/**
	 * 
	 * @param type $Content
	 * @return boolean
	 */
	protected function insertPinRecord( $CardID, $Door, $TDate, $Name = null, $HasPhoto = 0 )
	{
		$Date = new PDate( $TDate );
		$User = $this->_LoadUser( $CardID );
		$UserID = C::_( 'ID', $User, 0 );
		$UsersIDX = [];
		if ( $UserID )
		{
			$UsersIDX = XGraph::getWorkerSCH_IDx( $UserID );
		}

		if ( count( $UsersIDX ) )
		{
			foreach ( $UsersIDX as $UserOrg )
			{
				$ID = C::_( 'ID', $UserOrg );
				XUserGraphs::RegisterEvent( $ID, $Date->toFormat() );
				$Query = 'insert into '
								. ' hrs_transported_data '
								. '(id, rec_date, access_point_code, user_id, card_id, cardname, parent_id, client_id ) '
								. 'values '
								. ' ( '
								. 'sqs_transported_data.nextval,'
								. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'),'
								. DB::Quote( $Door ) . ','
								. DB::Quote( $ID ) . ','
								. DB::Quote( $CardID ) . ','
								. DB::Quote( $Name ) . ','
								. DB::Quote( $UserID ) . ','
								. DB::Quote( $HasPhoto )
								. ' ) ';
				try
				{
					DB::Insert( $Query, 'ID' );
					XGraph::RecalculateOldEvents( $UserID, $Date->toFormat(), $Date->toFormat() );
				}
				catch ( Exception $exc )
				{
					echo $exc->getTraceAsString();
				}
			}
			return true;
		}
		else
		{
			$ID = 0;
			$Query = 'insert into '
							. ' hrs_transported_data '
							. '(id, rec_date, access_point_code, user_id, card_id, cardname, parent_id, client_id ) '
							. 'values '
							. ' ( '
							. 'sqs_transported_data.nextval,'
							. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'),'
							. DB::Quote( $Door ) . ','
							. DB::Quote( $ID ) . ','
							. DB::Quote( $CardID ) . ','
							. DB::Quote( $Name ) . ','
							. DB::Quote( $UserID ) . ','
							. DB::Quote( $HasPhoto )
							. ' ) ';
			try
			{
				$R = DB::Insert( $Query, 'ID' );
				XGraph::RecalculateOldEvents( $UserID, $Date->toFormat(), $Date->toFormat() );
				return $R;
			}
			catch ( Exception $exc )
			{
				echo $exc->getTraceAsString();
			}
		}
		return false;

	}

	protected function insertRecord( $Content )
	{
		$CardID = C::_( '0', $Content );
		$Door = C::_( '1', $Content );
		$Name = C::_( '4', $Content, '' );
		$Date = new PDate( C::_( '2', $Content ), 0 );
		$User = Users::getUserByField( 'permit_id', strtolower( $CardID ) );
		$UserID = C::_( 'ID', $User, 0 );
		$UsersIDX = [];
		if ( $UserID )
		{
			$UsersIDX = XGraph::getWorkerSCH_IDx( $UserID );
		}
		if ( count( $UsersIDX ) )
		{
			foreach ( $UsersIDX as $UserOrg )
			{
				$ID = C::_( 'ID', $UserOrg );
				$Query = 'insert into '
								. ' hrs_transported_data '
								. '(id, rec_date, access_point_code, user_id, card_id, cardname, parent_id ) '
								. 'values '
								. ' ( '
								. 'sqs_transported_data.nextval,'
								. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'),'
								. DB::Quote( $Door ) . ','
								. DB::Quote( $ID ) . ','
								. DB::Quote( $CardID ) . ','
								. DB::Quote( $Name ) . ','
								. DB::Quote( $UserID )
								. ' ) ';
				try
				{
					DB::Insert( $Query, 'id' );
					XGraph::RecalculateOldEvents( $UserID, $Date->toFormat(), $Date->toFormat() );
				}
				catch ( Exception $exc )
				{
					echo $exc->getTraceAsString();
				}
			}
		}
		else
		{
			$ID = 0;
			$Query = 'insert into '
							. ' hrs_transported_data '
							. '(id, rec_date, access_point_code, user_id, card_id, cardname, parent_id ) '
							. 'values '
							. ' ( '
							. 'sqs_transported_data.nextval,'
							. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'),'
							. DB::Quote( $Door ) . ','
							. DB::Quote( $ID ) . ','
							. DB::Quote( $CardID ) . ','
							. DB::Quote( $Name ) . ','
							. DB::Quote( $UserID )
							. ' ) ';
			try
			{
				DB::Insert( $Query, 'id' );
				XGraph::RecalculateOldEvents( $UserID, $Date->toFormat(), $Date->toFormat() );
			}
			catch ( Exception $exc )
			{
				echo $exc->getTraceAsString();
			}
		}
		return true;

	}

	public function InsertDeviceData()
	{
		$Response = new stdClass();
		$Response->message = 'Invalid data given!';
		$this->StatusCode = 500;
		XApiHelper::SetResponse( $this->StatusCode );
		$this->requestContentType = 'text/html';
		$data = $this->getVars( 'post' );
		$IP = Request::getVar( 'REMOTE_ADDR', 0, 'server' );
		if ( empty( $data ) )
		{
			$this->_Print( $Response );
			return false;
		}

		$controller_code = C::_( 'CONTROLLER_CODE', $data );
		if ( empty( $controller_code ) )
		{
			$this->_Print( $Response );
			return false;
		}

		$controller_model = C::_( 'CONTROLER_MODEL', $data );
		if ( empty( $controller_model ) )
		{
			$this->_Print( $Response );
			return false;
		}
		$device = $this->_CheckDevice( $controller_code );
		$Table = new TableSlf_api_controllersInterface( 'slf_api_controllers', 'ID', 'sqs_api_controllers.nextval' );
		$Table->bind( $data );
		$Table->IP = $IP;
		$Table->DEVICE_ID = C::_( 'ID', $device );
		$Table->REC_DATE = PDate::Get()->toFormat();
		if ( $Table->store() )
		{
			$Response->message = 'Data inserted!';
			$this->StatusCode = 200;
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->_Print( $Response, $this->StatusCode );
			return true;
		}

		$query = 'select '
						. ' * '
						. ' from lib_controllers c '
						. ' where '
						. ' c.active >= 0 '
						. ' and c.controller_code = ' . DB::Quote( trim( $controller_code ) )
		;
		$result = DB::LoadObject( $query );
		if ( !empty( $result ) )
		{
			$Table->REGISTERED = 1;
		}

		$Table->IP = $IP;
		$Table->DEVICE_ID = C::_( 'ID', $result );
		$Table->REC_DATE = PDate::Get()->toFormat();
		if ( $Table->store() )
		{
			$this->StatusCode = 200;
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$Response->message = 'Data inserted!';
			$this->_Print( $Response, $this->StatusCode );
			return true;
		}

	}

	protected function _CheckDevice( $controller_code )
	{
		$query = 'select '
						. ' * '
						. ' from lib_controllers c '
						. ' where '
						. ' c.active >= 0 '
						. ' and c.controller_code = ' . DB::Quote( trim( $controller_code ) )
		;
		return DB::LoadObject( $query );

	}

}
