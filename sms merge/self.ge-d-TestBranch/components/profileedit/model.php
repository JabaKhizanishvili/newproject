<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class ProfileEditModel extends Model
{
	protected $Table = null;
	protected $LogPath = '';

	public function __construct( $params )
	{
		$this->Table = new ProfileEditsTable( );
		parent::__construct( $params );
		$this->LogPath = PATH_LOGS . DS . 'LoginData';
		if ( !Folder::exists( $this->LogPath ) )
		{
			Folder::create( $this->LogPath, 0777 );
		}

	}

	public function getItem()
	{
		$id = Users::GetUserID();
		if ( !empty( $id ) )
		{
			$this->Table->load( $id );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$imageSource = C::_( 'PHOTO', $data );
		$Photo = Helper::Base64ToImage( $imageSource, Users::GetUserID() );
		if ( empty( $Photo ) )
		{
			return false;
		}
		$BindData = array();
		$BindData['PHOTO'] = $Photo;

//		$BindData['MOBILE_PHONE_NUMBER'] = C::_( 'MOBILE_PHONE_NUMBER', $data );
//		$BindData['WORK_PHONE_NUMBER'] = C::_( 'WORK_PHONE_NUMBER', $data );
//		$BindData['INTERNAL_PHONE_NUMBER'] = C::_( 'INTERNAL_PHONE_NUMBER', $data );
//		$BindData['WORKERS_OFFICIAL'] = (int) C::_( 'WORKERS_OFFICIAL', $data );
		$this->Table->load( Users::GetUserID() );
		if ( !$this->Table->bind( $BindData ) )
		{
			return false;
		}

		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}


		return $this->Table->insertid();

	}

	public function Login()
	{
		$User = Users::getUser();
		if ( C::_( 'COUNTING_TYPE', $User ) != 1 )
		{
			return false;
		}
		$Log = checkinuser::GetInstance();
		return $Log->Login();
//		$DoorCode = 'BTNIN';
//		$UserID = C::_( 'ID', $User, 0 );
//		$UsersIDX = $this->_LoadOrgSchUser( $UserID );
//		$Result = true;
//		foreach ( $UsersIDX as $ID )
//		{
//			$Result = $Result && $this->_InsertRow( $DoorCode, $ID, $UserID );
//		}
//		if ( $Result )
//		{
//			file_put_contents( $this->LogPath . DS . $UserID, 1 );
//		}
//		return $Result;

	}

	public function LogOut()
	{
		$User = Users::getUser();
		if ( C::_( 'COUNTING_TYPE', $User ) != 1 )
		{
			return false;
		}
		$Log = checkinuser::GetInstance();
		return $Log->LogOut();
//		$UserID = C::_( 'ID', $User );
//		$UsersIDX = $this->_LoadOrgSchUser( $UserID );
//		$DoorCode = 'BTNOUT';
//		$Result = true;
//		foreach ( $UsersIDX as $ID )
//		{
//			$Result = $Result && $this->_InsertRow( $DoorCode, $ID, $UserID );
//		}
//		if ( $Result )
//		{
//			file_put_contents( $this->LogPath . DS . C::_( 'ID', $User, 0 ), 2 );
//		}
//		return $Result;

	}

	public function _InsertRow( $DoorCode, $ID, $UserID )
	{
		$Date = new PDate( );
		$Query = ' insert '
						. ' into HRS_TRANSPORTED_DATA '
						. ' ( '
						. ' ID, '
						. ' REC_DATE, '
						. ' ACCESS_POINT_CODE, '
						. ' USER_ID, '
						. ' PARENT_ID '
						. ' ) '
						. ' values '
						. ' ( '
						. DB::Quote( 'CSB_' . substr( md5( microtime() . 'sdDSADaAscVS DB HGF3WQSA##%#%$^dfc' ), 0, 16 ) ) . ', '
						. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'), '
						. DB::Quote( $DoorCode ) . ','
						. DB::Quote( $ID ) . ','
						. DB::Quote( $UserID )
						. ' )'
		;

		return DB::Insert( $Query, 'ID' );

	}

	public function _LoadOrgUser( $UserID )
	{
		$Query = 'select '
						. ' w.id '
						. ' from hrs_workers w '
						. ' where '
						. ' w.parent_id =  ' . (int) $UserID
						. ' and w.enable = 1 '
		;
		return DB::LoadList( $Query );

	}

	public function _LoadOrgSchUser( $UserID )
	{
		$Query = 'select '
						. ' w.id '
						. ' from hrs_workers_sch w '
						. ' where '
						. ' w.parent_id =  ' . (int) $UserID
						. ' and w.enable = 1 '
		;
		return DB::LoadList( $Query );

	}

	public function GPSLogin()
	{
		$User = Users::getUser();
		if ( C::_( 'COUNTING_TYPE', $User ) != 2 )
		{
			return false;
		}
		$Longitude = Request::getVar( 'longitude', 0 );
		$Latiitude = Request::getVar( 'latitude', 0 );
		if ( empty( $Latiitude ) )
		{
			return false;
		}
		if ( empty( $Longitude ) )
		{
			return false;
		}
		$GetOfficeID = XGPS::CheckDIstance( $Longitude, $Latiitude );
		if ( !$GetOfficeID )
		{
			XError::setError( 'GPS Location Not Matching!' );
			return false;
		}
		$DoorCode = 'BTNIN';
		$UserID = C::_( 'ID', $User );
		$UsersIDX = $this->_LoadOrgSchUser( $UserID );
		$Result = true;
		foreach ( $UsersIDX as $ID )
		{
			$Result = $Result && $this->_InsertRow( $DoorCode, $ID, $UserID );
		}

		if ( $Result )
		{
			file_put_contents( $this->LogPath . DS . C::_( 'ID', $User, 0 ), 1 );
		}
		return $Result;

	}

	public function GPSLogOut()
	{
		$User = Users::getUser();
		if ( C::_( 'COUNTING_TYPE', $User ) != 2 )
		{
			return false;
		}
		$Longitude = Request::getVar( 'longitude', 0 );
		$Latiitude = Request::getVar( 'latitude', 0 );
		if ( empty( $Latiitude ) )
		{
			return false;
		}
		if ( empty( $Longitude ) )
		{
			return false;
		}
		$GetOfficeID = XGPS::CheckDIstance( $Longitude, $Latiitude );
		if ( !$GetOfficeID )
		{
			XError::setError( 'GPS Location Not Matching!' );
			return false;
		}

		$DoorCode = 'BTNOUT';
		$UserID = C::_( 'ID', $User );
		$UsersIDX = $this->_LoadOrgSchUser( $UserID );
		$Result = true;
		foreach ( $UsersIDX as $ID )
		{
			$Result = $Result && $this->_InsertRow( $DoorCode, $ID, $UserID );
		}

		if ( $Result )
		{
			file_put_contents( $this->LogPath . DS . C::_( 'ID', $User, 0 ), 2 );
		}
		return $Result;

	}

}
