<?php

class ajaxchiefworkers
{
	public function GetService()
	{
		$Query = trim( Request::getVar( 'query' ) );
		Request::setVar( 'format', 'json' );
		$Response = new stdClass();
		$Response->query = $Query;
		$Response->suggestions = $this->_getWorkers( $Query );
		return json_encode( $Response );

	}

	protected function _getWorkers( $Query )
	{
		$QueryData = explode( ' ', $Query );
		$Where = array();
		$ChiefID = Users::GetUserID();
		foreach ( $QueryData as $Key )
		{
			$Where[] = ' (u.firstname like ' . DB::Quote( '%' . $Key . '%' )
							. ' or u.lastname  like ' . DB::Quote( '%' . $Key . '%' ) . ')';
		}
		$Where[] = ' u.active=1 ';
		$Where[] = ' u.id in (select t.worker from REL_WORKER_CHIEF t where t.chief in (select m.id from hrs_workers m where m.PARENT_ID = ' . DB::Quote( $ChiefID ) . ')) ';
		$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';
		$query = 'select u.id "data", '
						. ' u.firstname || \' \' || u.lastname as "value" '
						. ' from ' . DB_SCHEMA . '.hrs_workers_data u '
						. $whereQ
						. ' order by "data" asc '
		;
		return DB::LoadObjectList( $query );

	}

	protected function cleanData( $DataList )
	{
		$Return = array();
		foreach ( $DataList as $d )
		{
			$d = trim( $d );
			if ( !empty( $d ) )
			{
				$Return[] = $d;
			}
		}
		return $Return;

	}

}
