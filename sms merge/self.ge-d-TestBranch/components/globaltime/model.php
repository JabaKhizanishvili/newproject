<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class GlobalTimeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new GlobalTimeTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		if ( !isset( $data['REST_TYPE'] ) )
		{
			return FALSE;
		}
		if ( is_null( $data['REST_TYPE'] ) )
		{
			return FALSE;
		}

		if ( !Xhelp::checkTime( C::_( 'START_TIME', $data ) ) )
		{
			return false;
		}
		if ( !Xhelp::checkTime( C::_( 'END_TIME', $data ) ) )
		{
			return false;
		}

		$T = new XHRSTable();
		$restDiff = 0;

		if ( $data['REST_TYPE'] != 0 )
		{
			if ( !Xhelp::checkTime( C::_( 'START_BREAK', $data ) ) )
			{
				return false;
			}
			if ( !Xhelp::checkTime( C::_( 'END_BREAK', $data ) ) )
			{
				return false;
			}

			$startTime = C::_( 'START_TIME', $data );
			$endTime = C::_( 'END_TIME', $data );
			$startBreak = C::_( 'START_BREAK', $data );
			$endBreak = C::_( 'END_BREAK', $data );

			if ( !Xhelp::workedHours( $startTime, $startBreak, $endBreak, $endTime ) )
			{
				return false;
			}
			$restDiff = $T->CalculateHoursDiff( C::_( 'START_BREAK', $data ), C::_( 'END_BREAK', $data ) );
		}
		else
		{
			$data['START_BREAK'] = '';
			$data['END_BREAK'] = '';
		}


		$RestType = (int) C::_( 'REST_TYPE', $data );
		if ( $RestType == 1 )
		{
			$data['REST_TIME'] = $T->CalculateHoursDiff( C::_( 'START_BREAK', $data ), C::_( 'END_BREAK', $data ) );
			$data['REST_MINUTES'] = 0;
		}
		elseif ( $RestType == 4 )
		{
			$Min = (int) C::_( 'REST_MINUTES', $data );
			$m = number_format( $restDiff * 60, 0 );

			if ( $m < C::_( 'REST_MINUTES', $data ) )
			{
				return false;
			}
			$data['REST_TIME'] = round( $Min / 60, 2 );
		}
		else
		{
			$data['REST_MINUTES'] = 0;
			$data['REST_TIME'] = 0;
			$data['END_BREAK'] = '';
			$data['START_BREAK'] = '';
		}

		$dataX = $data;
		$dataX['restDiff'] = $restDiff;

		$keys = [
				'START_TIME',
				'END_TIME',
				'REST_TYPE',
				'START_BREAK',
				'END_BREAK',
				'HOLIDAY_YN',
				'restDiff',
				'ACTIVE',
				'VACATION_INDEX'
		];

		$result = Xhelp::multiImplode( $keys, $dataX, '|' );
		$md5 = md5( $result );
		$data['CHECKSUM'] = $md5;

		$data['WORKING_TIME'] = $T->CalculateHoursDiff( C::_( 'START_TIME', $data ), C::_( 'END_TIME', $data ) ) - $data['REST_TIME'];
		$Result = parent::SaveData( $data );
		return $Result;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$existGraphs = Xhelp::CheckStandartTimeInGlobalGrs( $id );
				if ( count( $existGraphs ) > 0 )
				{
					XError::setError( 'global graph is bind to standard graph!' );
					continue;
				}
				parent::Delete( (array) $id, $mode );
			}
		}
		else
		{
			return false;
		}
		return true;

	}

}
