<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class GraphTimeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new GraphTimeTable( );
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
		$Groups = C::_( 'GROUPS', $data, array() );
		$data['GROUPS'] = implode( ',', $Groups );

		if ( !isset( $data['REST_TYPE'] ) )
		{
			return FALSE;
		}
		if ( empty( $data['GROUPS'] ) )
		{
			return FALSE;
		}
        if ( !isset( $data['HOLIDAY_YN'] ) )
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

            $startTime = C::_('START_TIME', $data);
            $endTime = C::_('END_TIME', $data);
            $startBreak = C::_('START_BREAK', $data);
            $endBreak = C::_('END_BREAK', $data);

            if(!Xhelp::workedHours($startTime, $startBreak, $endBreak, $endTime)) {
                return false;
            }
		}

		$T = new XHRSTable();

		$RestType = (int) C::_( 'REST_TYPE', $data );
		if ( $RestType == 1 )
		{
			$data['REST_TIME'] = $T->CalculateHoursDiff( C::_( 'START_BREAK', $data ), C::_( 'END_BREAK', $data ) );
			$data['REST_MINUTES'] = 0;
		}
		elseif ( $RestType == 4 )
		{
			$Min = (int) C::_( 'REST_MINUTES', $data );

            $restDiff = $T->CalculateHoursDiff( C::_( 'START_BREAK', $data ), C::_( 'END_BREAK', $data ) );
            $m = number_format($restDiff * 60, 0);

            if ($m < C::_('REST_MINUTES', $data)) {
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

		if ( !$this->Table->bind( $data ) )
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

		$Result = $this->Table->insertid();
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

	public function CheckCodex( $data )
	{
		$MinHours = (float) Helper::getConfig( 'no_rest_howrs' );
		$MinRest = intval( Helper::getConfig( 'no_rest_howrs_rest_min' ) ) / 60;
		$DiffH = $this->CalculateHoursDiff( C::_( 'START_TIME', $data ), C::_( 'END_TIME', $data ) );

//			return false;
		switch ( $RestType )
		{
			case 0 :
				if ( $DiffH >= $MinHours )
				{
					XError::setError( 'This Time must have a rest' );
					return false;
				}
				break;

			case 1 :
				$SB = C::_( 'START_BREAK', $data );
				$EB = C::_( 'END_BREAK', $data );
				if ( empty( $SB ) || empty( $EB ) )
				{
					XError::setError( 'This Time Rest Type must have a breack Times.' );
					return false;
				}
				$DiffR = $this->CalculateHoursDiff( $SB, $EB );
				$DiffH = $DiffH - $DiffR;

				if ( $DiffH >= $MinHours && $DiffR < $MinRest )
				{
					XError::setError( 'This Time must have a rest grate than 1 hour' );
					return false;
				}
				break;

			case 2:
				$RestTime = C::_( 'REST_MINUTES', $data ) / 60;

				$DiffH = $DiffH - $RestTime;
				if ( $DiffH >= $MinHours && $RestTime < $MinRest )
				{
					XError::setError( 'This Time must have a rest grate than 1 hour' );
					return false;
				}
				break;
		}

		return true;

	}

}
