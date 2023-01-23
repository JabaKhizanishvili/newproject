<?php

class checkinuser extends XObject
{
	protected $LogPath = '';

	public function __construct()
	{
		$this->LogPath = PATH_LOGS . DS . 'LoginData';
		if ( !Folder::exists( $this->LogPath ) )
		{
			Folder::create( $this->LogPath, 0777 );
		}

	}

	public function Login( $User = 0 )
	{
		if ( empty( $User ) )
		{
			$User = Users::getUser();
		}
		$DoorCode = 'BTNIN';
		$UserID = C::_( 'ID', $User, 0 );
		$UsersIDX = $this->_LoadOrgSchUser( $UserID );
		$Result = true;
		foreach ( $UsersIDX as $ID )
		{
			$Result = $Result && $this->_InsertRow( $DoorCode, $ID, $UserID );
		}
		if ( $Result )
		{
			file_put_contents( $this->LogPath . DS . $UserID, 1 );
		}
		return $Result;

	}

	public function LoginByCardID( $UserID, $DoorCode, $CardID )
	{
		if ( empty( $CardID ) )
		{
			return false;
		}
		$UsersIDX = $this->_LoadOrgSchUser( $UserID );
		$Result = true;
		foreach ( $UsersIDX as $ID )
		{
			$Result = $Result && $this->_InsertRow( $DoorCode, $ID, $UserID );
		}
		if ( $Result )
		{
			file_put_contents( $this->LogPath . DS . $UserID, 1 );
		}
		return $Result;

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

	public function LogOut( $User = 0 )
	{
		if ( empty( $User ) )
		{
			$User = Users::getUser();
		}
		$UserID = C::_( 'ID', $User );
		$UsersIDX = $this->_LoadOrgSchUser( $UserID );
		$DoorCode = 'BTNOUT';
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
