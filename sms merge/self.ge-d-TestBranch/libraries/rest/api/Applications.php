<?php

class XApplications extends XAPI
{
	protected $requestContentType = 'application/json';
	protected $StatusCode = 400;
	protected $Key = '4b8cf832-a82c-4e3e-896a-83db4d10e037';

	public function __construct()
	{
		XApiHelper::SetContentType( 'application/json' );

	}

	public function ConfirmFromEmail()
	{
		XApiHelper::SetResponse( 400 );
		$this->requestContentType = 'text/html';
		define( 'TIME_DELAY_LIMIT', 60 );
		$this->StatusCode = 500;
		$Response = new stdClass();
		$RowData = trim( C::_( 'post.data', $this->getVars() ) );
		$Hash = trim( C::_( 'post.hash', $this->getVars() ) );
		if ( empty( $Hash ) )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}
		if ( empty( $RowData ) )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}
		$CalculatedHash = md5( $RowData . '|' . $this->Key );
		if ( $CalculatedHash != $Hash )
		{
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			return false;
		}

		$Rows = explode( PHP_EOL, $RowData );
		foreach ( $Rows as $Row )
		{
			$Content = explode( '|', $Row );
			$ID = C::_( 0, $Content );
			$Email = C::_( 1, $Content );
			$Application = $this->GetApplicationData( $ID, $Email );
			$Status = C::_( 'STATUS', $Application );
			if ( $Status != 1 )
			{
				$this->_ProcessConfirmApplication( $Application );
			}
		}
		$this->StatusCode = 200;
		XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );

	}

	protected function _Clean( $ID )
	{
		return preg_replace( '/[^0-9]/', '', $ID );

	}

	protected function _Print( $Response )
	{
		ignore_user_abort();
//		ob_start();
		echo json_encode( $Response, JSON_UNESCAPED_UNICODE );
		$Content = ob_get_clean();
		$Length = strlen( $Content );
		XApiHelper::SetHeader( 'Content-Encoding: none' );
		XApiHelper::SetHeader( 'Content-Length: ' . $Length );
		XApiHelper::SetHeader( 'Connection: Close' );
		echo $Content;
		@ob_flush();
		@flush();

	}

	public function GetApplicationData( $ID, $Email )
	{
		$Q = 'select '
						. ' a.id, '
						. ' a.worker, '
						. ' a.status, '
						. ' wc.chief, '
						. ' w.email '
						. ' from HRS_APPLICATIONS a '
						. ' left join rel_worker_chief wc on wc.worker = a.worker '
						. ' left join slf_persons w  on w.id = wc.chief '
						. ' where '
						. ' a.id =  ' . DB::Quote( $ID )
						. ' and w.email = ' . DB::Quote( $Email );
		return DB::LoadObject( $Q );

	}

	/**
	 * 
	 * @param Integer $ID
	 * @return ApplicationsTable
	 */
	public function getApplication( $ID )
	{
		$APPTable = AppHelper::getTable();
		$APPTable->load( $ID );
		return $APPTable;

	}

	public function _ProcessConfirmApplication( $App )
	{
		$ID = C::_( 'ID', $App );
		$Application = $this->getApplication( $ID );
		if ( empty( C::_( 'ID', $Application ) ) )
		{
			return false;
		}
		if ( C::_( 'STATUS', $Application ) )
		{
			return false;
		}
		$ApproverID = C::_( 'CHIEF', $App );
		if ( empty( $ApproverID ) )
		{
			return false;
		}

		$Application->APPROVE = $ApproverID;
		$Application->APPROVE_DATE = PDate::Get()->toFormat();
		$Worker = C::_( 'WORKER', $Application );
		$User = Users::getUser( $Worker );
		$FIRSTNAME = C::_( 'FIRSTNAME', $User );
		$LASTNAME = C::_( 'LASTNAME', $User );
		$AppStart = PDate::Get( C::_( 'START_DATE', $Application ) );
		$AppEnd = PDate::Get( C::_( 'END_DATE', $Application ) );
		$PHONE_WORK_NUMBER = C::_( 'MOBILE_PHONE_NUMBER', $User );
		$sms = new oneWaySMS( );
		$Application->STATUS = 1;
		$Application->check();
		$Application->store();
		if ( !empty( $PHONE_WORK_NUMBER ) )
		{
			$sms->SendLeaveSMS( $FIRSTNAME, $LASTNAME, $AppStart->toFormat( '%d-%m-%Y' ), $AppEnd->toFormat( '%d-%m-%Y' ), $PHONE_WORK_NUMBER );
		}
		return true;

	}

}
