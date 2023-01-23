<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IF
 *
 * @author teimuraz
 */
class XIF
{
	public static function Leasing( $WorkFlow = null )
	{
		$Markers = TaskHelper::GetFlowAttributesMarkers( $WorkFlow, 0 );
		$PrivateNumber = C::_( 'PRIVATE_NUMBER', $Markers );
		if ( empty( $PrivateNumber ) )
		{
			return false;
		}
		$Query = 'select '
						. ' w.car_driver + '
						. ' w.cardriverreserve + '
						. ' w.bucket_truck_driver + '
						. ' w.buckettruckdriverreserve + '
						. ' w.companycardriver + '
						. ' w.companycardriverreserve '
//						. ' w.level5safety '
						. ' as leasing '
						. ' from hrs.hrs_Workers w '
						. ' where '
						. ' w.private_number = ' . DB::Quote( $PrivateNumber )
						. ' and w.active = 1 '
		;
		$Result = (int) DB::LoadResult( $Query );
		return $Result;

	}

	public static function Position( array $Position = null )
	{
		$Positions = array_flip( $Position );
		$UserPosition = Users::GetUserData( 'POSITION_ID' );
		if ( isset( $Positions[$UserPosition] ) )
		{
			return true;
		}
		return false;

	}

	public static function IsInGroup( array $Groups = null )
	{
		$Workers = array();
		foreach ( $Groups as $Group )
		{
			$Workers = array_merge( $Workers, TaskHelper::getGroupUsers( $Group ) );
		}
		$WorkersData = array_flip( $Workers );
		$UserPosition = Users::GetUserID();
		if ( isset( $WorkersData[$UserPosition] ) )
		{
			return true;
		}
		return false;

	}

	public static function IsAnswered( $WorkFlow = null )
	{
		if ( empty( $WorkFlow ) )
		{
			return false;
		}
		$Query = 'select '
						. ' count(1) '
						. ' from REL_WORKFLOW_LETTERS_ANSWERS t '
						. ' where '
						. ' workflow_id =' . DB::Quote( $WorkFlow )
		;
		$Result = (int) DB::LoadResult( $Query );
		if ( $Result > 0 )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

}
