<?php

class XTransactions extends XAPI
{
	protected $requestContentType = 'application/json';
	protected $StatusCode = 400;
	protected $Key = '315a48ba-52bf-4a0a-936e-e67032df85ed';

	public function __construct()
	{
		XApiHelper::SetContentType( 'application/json' );
		XApiHelper::SetResponse( $this->StatusCode );

	}

	public function Register()
	{
		XApiHelper::SetResponse( 400 );
		$this->requestContentType = 'text/html';
		$this->StatusCode = 500;
		$RowData = trim( C::_( 'post.data', $this->getVars() ) );
		$Hash = trim( C::_( 'post.hash', $this->getVars() ) );
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
			$CalculatedHash = md5( $RowData . '|' . $this->Key );
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

	protected function insertRecord( $Content )
	{
		$CardID = C::_( '0', $Content );
		$Door = C::_( '1', $Content );
		$Name = C::_( '4', $Content, '' );
		$Date = new PDate( C::_( '2', $Content ), 0 );
		$UserID = $this->_loadID( $CardID );
		$UsersIDX = [];
		if ( $UserID )
		{
			$UsersIDX = $this->getWorkerSCH_IDx( $UserID );
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

	public function RegisterByID()
	{
		XApiHelper::SetResponse( 400 );
		$this->requestContentType = 'text/html';
		$this->StatusCode = 500;
		$RowData = trim( C::_( 'post.data', $this->getVars() ) );
		$Hash = trim( C::_( 'post.hash', $this->getVars() ) );
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
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				exit;
			}
			$CalculatedHash = md5( $RowData . '|' . $this->Key );
			if ( $CalculatedHash != $Hash )
			{
				XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
				exit;
			}

			$Rows = explode( '$$$', $RowData );
			foreach ( $Rows as $Row )
			{
				$Row = trim( trim( $Row ), '|' );
				if ( empty( $Row ) )
				{
					continue;
				}
				$Content = explode( '|', $Row );
				$IDx = C::_( '0', $Content );
				$Content[0] = $this->GetCardID( $IDx );
				$this->insertRecord( $Content );
			}
			$this->StatusCode = 200;
			XApiHelper::setHttpHeaders( $this->requestContentType, $this->StatusCode );
			$this->_Print( 'Done!' );
		}

	}

	public function GetCardID( $IDx )
	{
		static $Cards = array();
		if ( !isset( $Cards[$IDx] ) )
		{
			$Query = 'select '
							. ' w.permit_id '
							. ' from REL_WORKER_DEVICE_ID wd '
							. ' left join slf_persons w on w.id = wd.worker '
							. ' where '
							. ' wd.device_id =' . (int) $IDx;
			$Cards[$IDx] = DB::LoadResult( $Query );
			if ( !$Cards[$IDx] )
			{
				$Cards[$IDx] = 0;
			}
		}
		return$Cards[$IDx];

	}

	public function _loadID( $IDx )
	{
		static $UIDs = array();
		if ( !isset( $UIDs[$IDx] ) )
		{
			$Query = 'select '
							. ' pp.person '
							. ' from rel_person_permit pp '
							. ' left join slf_persons p on p.id = pp.person '
							. ' where '
							. ' pp.permit_id = ' . DB::Quote( $IDx )
							. ' and p.active = 1 '
			;
			$UIDs[$IDx] = DB::LoadResult( $Query );
			if ( !$UIDs[$IDx] )
			{
				$UIDs[$IDx] = 0;
			}
		}
		return$UIDs[$IDx];

	}

	public function getWorkerSCH_IDx( $UserID )
	{
		if ( $UserID == 0 )
		{
			$UserID = Users::GetUserID();
		}
		$Q = ' select '
						. ' w.id, '
						. ' w.org '
						. ' from slf_worker w '
						. ' where '
						. ' w.person = ' . (int) $UserID
						. ' and w.active=1 '
		;
		return DB::LoadObjectList( $Q );

	}

}
