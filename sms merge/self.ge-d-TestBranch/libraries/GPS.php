<?php

/**
 * Description of GPS
 *
 * @author teimuraz
 */
class XGPS
{
	public static function CheckDistance( $Longitude, $Latitude )
	{
		$OfficeGPSData = self::GetOfficeGPSData();
		$MinDistance = 1000000;
		foreach ( $OfficeGPSData as $ID => $Office )
		{
			$Distance = C::_( 'RADIUS', $Office );
			$RD = self::Distance( $Latitude, $Longitude, C::_( 'LAT', $Office ), C::_( 'LNG', $Office ) );
			if ( $RD < $MinDistance )
			{
				$MinDistance = $RD;
			}
			if ( $RD <= $Distance )
			{
				return $ID;
			}
		}
		XError::setMessage( Text::_( 'Distance To office:' ) . ' ' . (int) $MinDistance . ' ' . Text::_( 'M' ) );
		return false;

	}

	public static function GetOfficeGPSData()
	{
		$UserOffices = self::GetUserOffices();
		$Query = 'select '
						. ' t.id, '
						. ' t.lat, '
						. ' t.lng, '
						. ' t.radius, '
						. ' t.outer_radius, '
						. ' t.v_ip '
						. ' from lib_offices t '
						. ' where '
						. ' t.lat is not null '
						. (count( $UserOffices ) ? ' and t.id in ( ' . implode( ',', $UserOffices ) . ' ) ' : '')
						. ' and t.lng is not null '
						. ' and t.active = 1 '
		;
		return DB::LoadObjectList( $Query, 'ID' );

	}

	public static function GetOfficeData()
	{
		$UserOffices = self::GetUserOffices();
		$Query = 'select '
						. ' t.id, '
						. ' t.lat, '
						. ' t.lng, '
						. ' t.radius, '
						. ' t.outer_radius, '
						. ' t.v_ip '
						. ' from lib_offices t '
						. ' where '
						. ' 1=1 '
//						. ' t.lat is not null '
						. (count( $UserOffices ) ? ' and t.id in ( ' . implode( ',', $UserOffices ) . ' ) ' : '  ')
//						. ' and t.lng is not null '
						. ' and t.active = 1 '
		;
		return (array) XRedis::getDBCache( 'lib_offices', $Query, 'LoadObjectList', 'ID' );
//		return DB::LoadObjectList( $Query, 'ID' );

	}

	public static function Distance( $lat1, $lon1, $lat2, $lon2, $unit = 'M' )
	{
		$theta = $lon1 - $lon2;
		$dist = rad2deg( acos( sin( deg2rad( $lat1 ) ) * sin( deg2rad( $lat2 ) ) + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * cos( deg2rad( $theta ) ) ) );
		$miles = $dist * 60 * 1.1515;
		if ( strtoupper( $unit ) == "M" )
		{
			return ($miles * 1.609344) * 1000;
		}
		else
		{
			return $miles;
		}

	}

	public static function GetUserOffices()
	{
		$Query = 'select ao.office from rel_accounting_offices ao where ao.worker = ' . (int) Users::GetUserID();
		return (array) XRedis::getDBCache( 'rel_accounting_offices', $Query, 'LoadList', 'OFFICE' );
//		return DB::LoadList(, 'OFFICE' );

	}

	public static function GetOfficeIPs()
	{
		$Offices = self::GetOfficeData();
		$IPUsr = array();
		foreach ( $Offices as $Office )
		{
			$IPx = Helper::CleanArray( explode( ',', C::_( 'V_IP', $Office ) ), 'str' );
			$IPUsr = array_merge( $IPUsr, $IPx );
		}
		return array_flip( $IPUsr );

	}

	public static function GetUserAccessIPs()
	{
		return array_merge( self::GetOfficeIPs(), array_flip( helper::CleanArray( explode( ',', Helper::getConfig( 'access_click_ip' ) ), 'str' ) ) );

	}

	public static function UserCanButtonAccess()
	{
		$IP = Request::getVar( 'REMOTE_ADDR', '-999999', 'server' );
		$IPs = XGPS::GetUserAccessIPs();
		$AllowClick = true;
		if ( !empty( $IPs ) )
		{
			if ( !isset( $IPs[$IP] ) )
			{
				$AllowClick = false;
			}
		}
		$Excluded = array_flip( helper::CleanArray( explode( ',', Helper::getConfig( 'access_excluded_workers' ) ) ) );
		if ( !empty( $Excluded ) && !$AllowClick )
		{
			$UID = Users::GetUserID();
			if ( isset( $Excluded[$UID] ) )
			{
				$AllowClick = true;
			}
		}
		return $AllowClick;

	}

}
