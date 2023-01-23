<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class hrgraphtimeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new hrgraphtimeTable( );
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
		$Groups = (array) C::_( 'GROUPS', $data );
		$data['GROUPS'] = implode( ',', $Groups );
		if ( !isset( $data['REST_TYPE'] ) )
		{
			return FALSE;
		}
		if ( !isset( $data['HOLIDAY_YN'] ) )
		{
			return FALSE;
		}

		if ( empty( $data['GROUPS'] ) )
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

		if ( $data['REST_TYPE'] != '0' )
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

			if ( !Xhelp::workedHours( $startTime, $startBreak, $endBreak, $endTime ) )
			{
				return false;
			}
		}


		/**
		 * Start
		 */
		$RestType = (int) C::_( 'REST_TYPE', $data );
		if ( $RestType == 1 )
		{
			if ( empty( $data['START_BREAK'] ) )
			{
				return FALSE;
			}
			if ( empty( $data['END_BREAK'] ) )
			{
				return FALSE;
			}
			$data['REST_TIME'] = $T->CalculateHoursDiff( C::_( 'START_BREAK', $data ), C::_( 'END_BREAK', $data ) );
			$data['REST_MINUTES'] = 0;
		}
		elseif ( $RestType == 4 )
		{
			if ( empty( $data['START_BREAK'] ) )
			{
				return FALSE;
			}
			if ( empty( $data['END_BREAK'] ) )
			{
				return FALSE;
			}

			if ( empty( $data['REST_MINUTES'] ) )
			{
				return FALSE;
			}

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

		$data['WORKING_TIME'] = $T->CalculateHoursDiff( C::_( 'START_TIME', $data ), C::_( 'END_TIME', $data ) ) - $data['REST_TIME'];

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
				'ACTIVE'
		];

		$result = Xhelp::multiImplode( $keys, $dataX, '|' );
		$md5 = md5( $result );
		$data['CHECKSUM'] = $md5;

		$Result = parent::SaveData( $data );
		if ( $Result > 1 )
		{
			$this->SaveGroupsRel( $Groups, $Result );
		}

		return $Result;

	}

	public function SaveGroupsRel( $data, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_time_group cp '
						. ' where '
						. ' cp.time_id = ' . (int) $id;

		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $data as $DD )
		{
			$query .= ' into rel_time_group '
							. ' (time_id, group_id) '
							. 'values '
							. '('
							. (int) $id
							. ','
							. (int) $DD
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

}
