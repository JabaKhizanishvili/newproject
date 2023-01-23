<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class newgraphtimeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new newgraphtimeTable( );
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

		if ( !isset( $data['HOLIDAY_YN'] ) )
		{
			return FALSE;
		}

		if ( empty( $data['GROUPS'] ) )
		{
			return FALSE;
		}


		if ( !Xhelp::checkTime( C::_( 'START_TIME', $data ) ) )
		{
			return false;
		}
		$data['WORKING_TIME'] = C::_( 'MIN_WORKING_HOUR', $data );
		$dataX = $data;
		$keys = [
				'START_TIME',
				'END_TIME',
				'REST_TYPE',
				'START_BREAK',
				'END_BREAK',
				'HOLIDAY_YN',
				'MIN_WORKING_HOUR',
				'MAX_WORKING_HOUR',
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
