<?php

abstract class RestHelper
{
	public static function RegisterRest( $Worker, $START_TIME, $Delete = 0 )
	{
		$UserTimeData = self::getUserTime( $Worker );
		$RestType = trim( C::_( 'REST_TYPE', $UserTimeData ) );
		$RestMin = intval( C::_( 'REST_MINUTES', $UserTimeData ) );
		if ( $RestType != 2 )
		{
			XError::setError( 'Uset Not have Dinamic Rest' );
			return false;
		}

		if ( !AppHelper::IsValidTime( $START_TIME ) )
		{
			XError::setError( 'Start Time Incorrect!' );
			return false;
		}

		$Now = new PDate();
		$WorkStartDate = new PDate( C::_( 'V_START_DATE', $UserTimeData ) );
		$WorkEndDate = new PDate( C::_( 'V_END_DATE', $UserTimeData ) );
		$RestStartDate = new PDate( $Now->toFormat( '%Y-%m-%d ' . $START_TIME ) );
		$RestEndDate = new PDate( $RestStartDate->toUnix() + ( 60 * $RestMin) );
		$OldRest = self::WorkerHasRest( $Worker, $WorkStartDate, $WorkEndDate );
		$HasRest = C::_( 'ID', $OldRest );
		if ( $HasRest && $Delete == 0 )
		{
			XError::setError( 'Worker Already Have Rest!' );
			return false;
		}
		if ( $WorkStartDate->toUnix() > $RestStartDate->toUnix() )
		{
			XError::setError( 'Start Time Incorrect!' );
			return false;
		}
		if ( $RestEndDate->toUnix() > $WorkEndDate->toUnix() )
		{
			XError::setError( 'Start Time Incorrect!' );
			return false;
		}
		if ( $Now->toUnix() > $RestStartDate->toUnix() )
		{
			XError::setError( 'Start Time Incorrect!' );
			return false;
		}
		$UserID = Users::GetUserID();
		if ( empty( $UserID ) )
		{
			$UserID = $Worker;
		}
		$data['WORKER'] = $Worker;
		$data['TYPE'] = APP_REST_TIME;
		$data['START_DATE'] = $RestStartDate->toFormat();
		$data['END_DATE'] = $RestEndDate->toFormat();
		$data['DAY_COUNT'] = 0;
		$data['STATUS'] = 1;
		$data['APPROVE'] = $UserID;
		$data['APPROVE_DATE'] = $Now->toFormat();
		$Table = AppHelper::getTable();
		if ( !$Table->bind( $data ) )
		{
			return false;
		}
		if ( !$Table->check() )
		{
			return false;
		}

		if ( $HasRest )
		{
			self::Delete( array( $HasRest ) );
		}

		if ( !$Table->store() )
		{
			return false;
		}
		$IDx = $Table->insertid();
		return $IDx;

	}

	public static function getUserTime( $Worker )
	{
		$Query = 'select '
						. ' t.*, '
						. ' gt.*, '
						. ' to_char(gt.start_date, \'yyyy-mm-dd hh24:mi:ss\') v_start_date, '
						. ' to_char(gt.end_date, \'yyyy-mm-dd hh24:mi:ss\') v_end_date, '
						. ' to_char(t.real_date, \'yyyy-mm-dd hh24:mi:ss\') v_real_date '
						. ' from HRS_GRAPH t '
						. ' left join hrs_v_graph_times gt on gt.id=t.time_id '
						. ' where '
						. ' t.worker = ' . $Worker
						. ' and t.real_date = trunc(sysdate)';
		return DB::LoadObject( $Query );

	}

	public static function WorkerHasRest( $Worker, $WorkStartDate, $WorkEndDate )
	{
		$Query = ' select '
						. ' * '
						. ' from hrs_applications a '
						. ' where '
						. ' a.start_date > to_date(\'' . $WorkStartDate->toFormat() . '\', \'yyyy-mm-dd hh24:mi:ss\') '
						. ' and a.end_date <  to_date(\'' . $WorkEndDate->toFormat() . '\', \'yyyy-mm-dd hh24:mi:ss\') '
						. ' and a.type =  ' . APP_REST_TIME
						. ' and a.worker = ' . $Worker
						. ' and a.status = 1'
		;
		return DB::LoadObject( $Query );

	}

	public static function Delete( $data )
	{
		if ( is_array( $data ) )
		{
			$TableOBJ = AppHelper::getTable();
			foreach ( $data as $id )
			{
				$Table = clone $TableOBJ;
				$Date = new PDate();
				$Table->load( $id );				
				if ( !$Table->ID )
				{
					continue;
				}
				$Table->STATUS = -2;
				$Table->DEL_USER = Users::GetUserID();
				$Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$Table->store();
			}
		}
		return true;

	}

	public static function getGraph( $Worker, $date )
	{
		$Times = AppHelper::GetUserDayDates( $Worker, $date );
		$TimeID = C::_( 'UserTimeID', $Times );
		$TimeData = self::_getTimeData( $TimeID );

		$JsonTimes = array_change_key_case( $Times, CASE_UPPER );

		if ( C::_( 'REST_TYPE', $TimeData ) == 2 )
		{
			$RestData = self::_getRestApp( $Worker, C::_( 'DAYSTART', $JsonTimes ), C::_( 'DAYEND', $JsonTimes ) );
			if ( count( $RestData ) )
			{
				$JsonTimes = array_merge( $JsonTimes, (array) $RestData );
			}
		}
		return $JsonTimes;

	}

	public static function _getTimeData( $TimeID )
	{
		$Query = 'select * from LIB_GRAPH_TIMES t where t.id = ' . DB::Quote( $TimeID );
		return DB::LoadObject( $Query );

	}

	public static function _getRestApp( $UserID, $Start, $End )
	{
		$Query = 'select '
						. ' to_char(t.start_date, \'yyyy-mm-dd hh24:mi\') "BREAKSTART", '
						. ' to_char(t.end_date, \'yyyy-mm-dd hh24:mi\') "BREAKEND" '
						. ' from HRS_APPLICATIONS t '
						. ' where '
						. ' t.worker =  ' . DB::Quote( $UserID )
						. ' and t.type = 10 '
						. ' and t.status = 1 '
						. ' and t.start_date between to_date(' . DB::Quote( $Start ) . ', \'yyyy-mm-dd hh24:mi\') and to_date(' . DB::Quote( $End ) . ', \'yyyy-mm-dd hh24:mi\')';
		$Result = DB::LoadObject( $Query );
		if ( !$Result )
		{
			$Result = array(
					'BREAKSTART' => '',
					'BREAKEND' => ''
			);
		}
		return $Result;

	}

}
